<?php
namespace App\Handlers\OrderPlatforms;

use App\Models\Country;
use App\Models\State;
use App\Models\StoreAccount;
use App\Models\StoreOrder;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class Mabang implements OrderPlatform {

    public function syncOrder(StoreAccount $account, $options)
    {
        //$date_end = $options['date_end'] ?: date('Y-m-d');
        //$date_start = $options['date_start'];
        //if (Carbon::parse($date_end)->diffInDays($date_start, true) > 4) {
        //    $date_start = date('Y-m-d', strtotime('-4 days', strtotime($date_end)));
        //}
        $date_start = $options['date_start'];
        $date_end = $options['date_end'];

        $allCountries = Country::all()->toArray();
        $allStates = State::all()->toArray();

        $orders = [];
        $page = 1;
        do {
            $requestData = [
                'sysData' => [
                    'token' => $account->storeApp->app_token,
                    'accessToken' => $account->account_token,
                    'appKey' => $account->storeApp->app_key,
                    'action' => 'get-Order-Info',
                    'version' => 'v1',
                ],
                'data' => [
                    'page' => $page,
                    'startTime' => $date_start . ' 00:00:00',
                    'endTime' => $date_end . ' 23:59:59',
                ]
            ];

            $response = Http::post($account->storeApp->api_url, $requestData);

            if (isset($response['data']['order'])) {
                $orders = array_merge($orders, $response['data']['order']);
            }

            $page ++;
        } while ($response->ok() && isset($response['data']['order']) && count($response['data']['order']) == 100);

        $skus = $account->user->skus;

        foreach ($orders as $orderItem) {

            $country = Arr::first($allCountries, function ($item) use ($orderItem) {
                return strtoupper($item['abb']) == strtoupper($orderItem['countryCode']) || strtoupper($item['name']) == strtoupper($orderItem['countryCode']);
            });

            $state = null;
            if ($country) {
                $state = Arr::first($allStates, function ($item) use ($orderItem, $country) {
                    return $item['country_id'] == $country['id'] && (strtoupper($item['abb']) == strtoupper($orderItem['province']) || strtoupper($item['name']) == strtoupper($orderItem['province']));
                });
            }

            $saveData = [
                'store_account_id' => $account->id,
                'store_id' => $account->store->id,
                'sale_order_number' => $orderItem['platformOrderId'],
                'order_id' => $orderItem['orderId'],
                'name' => $orderItem['buyerName'],
                'street1' => $orderItem['street1'],
                'street2' => $orderItem['street2'],
                'city' => $orderItem['city'],
                'state' => $orderItem['province'],
                'state_id' => $state ? $state['id'] : NULL,
                'country' => $orderItem['countryCode'],
                'country_id' => $country ? $country['id'] : NULL,
                'zip_code' => $orderItem['postCode'],
                'phone' => $orderItem['phone1'],
                'warehouse_code' => $orderItem['tWarehourseCode'],
                'buyer_email' => $orderItem['email'],

                'store_created_at' => $orderItem['createTime'],
                'can_ship' => $orderItem['canship'] ? 1 : 0,
                'remark' => $orderItem['remark'],
                'last_sync_at' => Carbon::now(),
                'user_id' => $account->user_id,
            ];

            $order = StoreOrder::query()
                ->where('store_account_id', $account->id)
                ->where('store_id', $account->store->id)
                ->where('order_id', $orderItem['orderId'])
                ->first();

            $wmsSkuItems = [];
            $sku_match_status = true;
            foreach ($orderItem['orderItem'] as $skuItem) {
                $wmsSku = $skus->where('sku', $skuItem['stockSku'])->first();
                $sku_match_status = $sku_match_status && $wmsSku;

                if ($wmsSku) {
                    array_push($wmsSkuItems, [
                        'sku_id' => $wmsSku->id,
                        'sku' => $wmsSku->sku,
                        'quantity' => $skuItem['quantity'],
                    ]);
                } else {
                    array_push($wmsSkuItems, [
                        'sku_id' => 0,
                        'sku' => $skuItem['stockSku'],
                        'quantity' => $skuItem['quantity'],
                    ]);
                }
            }

            $orderSkuSaveData = [
                'sku_items' => $orderItem['orderItem'],
                'wms_sku_items' => $wmsSkuItems,
                'sku_match_status' => $sku_match_status,
            ];

            if (!$order) {
                $order = new StoreOrder();
                $order->fill($saveData);
                $order->save();
                $order->storeOrderSku()->create($orderSkuSaveData);
            } else {
                if ($options['is_cover_update'] && !$order->wms_order_id) {
                    $order->fill($saveData);
                    $order->save();
                    $order->storeOrderSku()->update($orderSkuSaveData);
                } else {
                    continue;
                }
            }
        }
    }

    public function syncShip($items, $account)
    {
        $requestData = [
            'sysData' => [
                'token' => $account->storeApp->app_token,
                'accessToken' => $account->account_token,
                'appKey' => $account->storeApp->app_key,
                'action' => 'translation-Order-Info',
                'version' => 'v1',
            ],
        ];

        foreach ($items as $item) {
            $requestData['data'][] = [
                'orderId' => $item['order_id'],
                'trackNumber' => $item['tracking_code'],
                'orderWeight' => $item['weight'],
                'shippingCost' => $item['cost'],
                'isDeliverGoods' => 1,
            ];
        }
        $response = Http::post($account->storeApp->api_url, $requestData);

        $orderIds = array_column($response['data'], 'orderId');
        foreach ($items as $item) {
            if (!in_array($item['order_id'], $orderIds)) {
                StoreOrder::query()->where('order_id', $item['order_id'])->update([
                    'wms_tracking_code' => $item['tracking_code'],
                ]);
            }
        }

        foreach ($response['data'] as $item) {
            if ($storeOrder = StoreOrder::query()->where('order_id', $item['orderId'])->first()) {
                $storeOrder->wms_error = $item['message'];
                $storeOrder->save();
            }
        }

    }
}
