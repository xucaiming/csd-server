<?php

namespace App\Handlers\ShipmentPlatforms;

use EasyPost\EasyPost;
use EasyPost\Util;

class EasyPostShipment extends Shipment
{
    protected $accountInfo;

    public function __construct($accountInfo = [])
    {
        $this->accountInfo = $accountInfo;
    }

    public function createShipment()
    {
        $requestData = [
            'to_address' => [
                'street1' => $this->toAddress['street1'],
                'street2' => $this->toAddress['street2'],
                'city' => $this->toAddress['city'],
                'state' => $this->toAddress['state'],
                'zip' => $this->toAddress['zip'],
                'country' => $this->toAddress['country'],
                'name' => $this->toAddress['name'],
                'company' => $this->toAddress['company'],
                'phone' => $this->toAddress['phone'],
            ],
            'from_address' => [
                'street1' => $this->fromAddress['street1'],
                'street2' => $this->fromAddress['street2'],
                'city' => $this->fromAddress['city'],
                'state' => $this->fromAddress['state'],
                'zip' => $this->fromAddress['zip'],
                'country' => $this->fromAddress['country'],
                'name' => $this->fromAddress['name'],
                'company' => $this->fromAddress['company'],
                'phone' => $this->fromAddress['phone'],
            ],
            'parcel' => [
                'length' => ceil($this->parcel['length'] * 0.393700787402 * 10) / 10,
                'width' => ceil($this->parcel['width'] * 0.393700787402 * 10) / 10,
                'height' => ceil($this->parcel['height'] * 0.393700787402 * 10) /10,
                'weight' => ceil($this->parcel['weight'] * 35.2739619496 * 10) / 10,
            ],
            'carrier_accounts' => $this->accountInfo['accounts'],
        ];
        $shipment = \EasyPost\Shipment::create($requestData);
        return $shipment;
    }

    public function getRates()
    {
        EasyPost::setApiKey($this->accountInfo['api_key']);
        $shipment = $this->createShipment();
        $responseRates = Util::convertEasyPostObjectToArray($shipment->get_rates());
        $rates = [];
        foreach ($responseRates['rates'] as $rate) {
            array_push($rates, $this->formatRate($rate));
        }
        return $rates;
    }

    public function createLabel()
    {
        try {
            EasyPost::setApiKey($this->accountInfo['api_key']);
            $shipment = $this->createShipment();

            $rate = $shipment->lowest_rate($this->selectedRate['carrier'], $this->selectedRate['service']);
            //\Log::info('rate', Util::convertEasyPostObjectToArray($rate));
            $shipment->buy($rate);
            $shipmentArr = Util::convertEasyPostObjectToArray($shipment);
            // \Log::info('shipment', $shipmentArr);

            // 模拟数据
            //$jsonStr = '{"id":"shp_3b9ca5637dfe42828bedc3d04d7e6b5d","created_at":"2021-12-13T08:37:42Z","is_return":false,"messages":[],"mode":"production","options":{"currency":"USD","payment":{"type":"SENDER"},"date_advance":0},"reference":null,"status":"unknown","tracking_code":"287459190630","updated_at":"2021-12-13T08:37:45Z","batch_id":null,"batch_status":null,"batch_message":null,"customs_info":null,"from_address":{"id":"adr_f01a502a5bef11ec8892ac1f6b0a0d1e","object":"Address","created_at":"2021-12-13T08:37:42+00:00","updated_at":"2021-12-13T08:37:42+00:00","name":"Sam","company":"ShengZhen HYC Co. , Ltd","street1":"255 Park Center Drive","street2":null,"city":"Patterson","state":"CA","zip":"95363","country":"US","phone":"8888888888","email":null,"mode":"production","carrier_facility":null,"residential":null,"federal_tax_id":null,"state_tax_id":null,"verifications":[]},"insurance":null,"order_id":null,"parcel":{"id":"prcl_8a1a66ee11d44ecc903cc7c2671ed23a","object":"Parcel","created_at":"2021-12-13T08:37:42Z","updated_at":"2021-12-13T08:37:42Z","length":13.8,"width":7.9,"height":9.9,"predefined_package":null,"weight":28.3,"mode":"production"},"postage_label":{"id":"pl_305957d10d9447838df0e11392ba2175","object":"PostageLabel","created_at":"2021-12-13T08:37:45Z","updated_at":"2021-12-13T08:37:45Z","date_advance":0,"integrated_form":"none","label_date":"2021-12-13T08:37:45Z","label_resolution":200,"label_size":"PAPER_4X6","label_type":"default","label_file_type":"image/png","label_url":"https://easypost-files.s3.us-west-2.amazonaws.com/files/postage_label/20211213/6999ebbcc5c74a82bae01752757ea623.png","label_pdf_url":null,"label_zpl_url":null,"label_epl2_url":null,"label_file":null},"rates":[{"id":"rate_16523ecd6fb343e19e394cb8188cdc93","object":"Rate","created_at":"2021-12-13T08:37:43Z","updated_at":"2021-12-13T08:37:43Z","mode":"production","service":"FIRST_OVERNIGHT","carrier":"FedEx","rate":"154.91","currency":"USD","retail_rate":null,"retail_currency":null,"list_rate":"154.91","list_currency":"USD","delivery_days":1,"delivery_date":"2021-12-14T09:30:00Z","delivery_date_guaranteed":true,"est_delivery_days":1,"shipment_id":"shp_3b9ca5637dfe42828bedc3d04d7e6b5d","carrier_account_id":"ca_cbc4d2a0e0164a5394ea67c96690b94d"},{"id":"rate_1bc9490e1ded467aae890066577a6709","object":"Rate","created_at":"2021-12-13T08:37:43Z","updated_at":"2021-12-13T08:37:43Z","mode":"production","service":"PRIORITY_OVERNIGHT","carrier":"FedEx","rate":"84.40","currency":"USD","retail_rate":null,"retail_currency":null,"list_rate":"120.57","list_currency":"USD","delivery_days":1,"delivery_date":"2021-12-14T11:30:00Z","delivery_date_guaranteed":true,"est_delivery_days":1,"shipment_id":"shp_3b9ca5637dfe42828bedc3d04d7e6b5d","carrier_account_id":"ca_cbc4d2a0e0164a5394ea67c96690b94d"},{"id":"rate_eefc98bdfe1a4a60b47c101d54f3a2a9","object":"Rate","created_at":"2021-12-13T08:37:43Z","updated_at":"2021-12-13T08:37:43Z","mode":"production","service":"STANDARD_OVERNIGHT","carrier":"FedEx","rate":"82.18","currency":"USD","retail_rate":null,"retail_currency":null,"list_rate":"117.40","list_currency":"USD","delivery_days":1,"delivery_date":"2021-12-14T16:30:00Z","delivery_date_guaranteed":true,"est_delivery_days":1,"shipment_id":"shp_3b9ca5637dfe42828bedc3d04d7e6b5d","carrier_account_id":"ca_cbc4d2a0e0164a5394ea67c96690b94d"},{"id":"rate_8fbaf131b7b44f3a8427ff4f45fcbf96","object":"Rate","created_at":"2021-12-13T08:37:43Z","updated_at":"2021-12-13T08:37:43Z","mode":"production","service":"FEDEX_2_DAY_AM","carrier":"FedEx","rate":"33.08","currency":"USD","retail_rate":null,"retail_currency":null,"list_rate":"47.26","list_currency":"USD","delivery_days":2,"delivery_date":"2021-12-15T11:30:00Z","delivery_date_guaranteed":true,"est_delivery_days":2,"shipment_id":"shp_3b9ca5637dfe42828bedc3d04d7e6b5d","carrier_account_id":"ca_cbc4d2a0e0164a5394ea67c96690b94d"},{"id":"rate_64ee68f7e3d342c0a9d4d9530312e63f","object":"Rate","created_at":"2021-12-13T08:37:43Z","updated_at":"2021-12-13T08:37:43Z","mode":"production","service":"FEDEX_2_DAY","carrier":"FedEx","rate":"29.86","currency":"USD","retail_rate":null,"retail_currency":null,"list_rate":"42.66","list_currency":"USD","delivery_days":2,"delivery_date":"2021-12-15T16:30:00Z","delivery_date_guaranteed":true,"est_delivery_days":2,"shipment_id":"shp_3b9ca5637dfe42828bedc3d04d7e6b5d","carrier_account_id":"ca_cbc4d2a0e0164a5394ea67c96690b94d"},{"id":"rate_985b923275304a2a87c88f81dac17746","object":"Rate","created_at":"2021-12-13T08:37:43Z","updated_at":"2021-12-13T08:37:43Z","mode":"production","service":"FEDEX_GROUND","carrier":"FedEx","rate":"13.19","currency":"USD","retail_rate":null,"retail_currency":null,"list_rate":"15.52","list_currency":"USD","delivery_days":2,"delivery_date":"2021-12-15T23:59:00Z","delivery_date_guaranteed":true,"est_delivery_days":2,"shipment_id":"shp_3b9ca5637dfe42828bedc3d04d7e6b5d","carrier_account_id":"ca_cbc4d2a0e0164a5394ea67c96690b94d"},{"id":"rate_af784913b6ce4500b91cccf993c62068","object":"Rate","created_at":"2021-12-13T08:37:43Z","updated_at":"2021-12-13T08:37:43Z","mode":"production","service":"FEDEX_EXPRESS_SAVER","carrier":"FedEx","rate":"25.38","currency":"USD","retail_rate":null,"retail_currency":null,"list_rate":"36.27","list_currency":"USD","delivery_days":3,"delivery_date":"2021-12-16T16:30:00Z","delivery_date_guaranteed":true,"est_delivery_days":3,"shipment_id":"shp_3b9ca5637dfe42828bedc3d04d7e6b5d","carrier_account_id":"ca_cbc4d2a0e0164a5394ea67c96690b94d"}],"refund_status":null,"scan_form":null,"selected_rate":{"id":"rate_985b923275304a2a87c88f81dac17746","object":"Rate","created_at":"2021-12-13T08:37:45Z","updated_at":"2021-12-13T08:37:45Z","mode":"production","service":"FEDEX_GROUND","carrier":"FedEx","rate":"13.19","currency":"USD","retail_rate":null,"retail_currency":null,"list_rate":"15.52","list_currency":"USD","delivery_days":2,"delivery_date":"2021-12-15T23:59:00Z","delivery_date_guaranteed":true,"est_delivery_days":2,"shipment_id":"shp_3b9ca5637dfe42828bedc3d04d7e6b5d","carrier_account_id":"ca_cbc4d2a0e0164a5394ea67c96690b94d"},"tracker":{"id":"trk_71163304d8f64c5589e0b5c5af60ece8","object":"Tracker","mode":"production","tracking_code":"287459190630","status":"unknown","status_detail":"unknown","created_at":"2021-12-13T08:37:45Z","updated_at":"2021-12-13T08:37:45Z","signed_by":null,"weight":null,"est_delivery_date":null,"shipment_id":"shp_3b9ca5637dfe42828bedc3d04d7e6b5d","carrier":"FedEx","tracking_details":[],"fees":[],"carrier_detail":null,"public_url":"https://track.easypost.com/djE6dHJrXzcxMTYzMzA0ZDhmNjRjNTU4OWUwYjVjNWFmNjBlY2U4"},"to_address":{"id":"adr_f018aecd5bef11eca937ac1f6bc72124","object":"Address","created_at":"2021-12-13T08:37:42+00:00","updated_at":"2021-12-13T08:37:42+00:00","name":"TEST SAM","company":"TEST COMPANY","street1":"6835 West Buckeye Road","street2":null,"city":"Phoenix","state":"AZ","zip":"85043","country":"US","phone":"8888888888","email":null,"mode":"production","carrier_facility":null,"residential":null,"federal_tax_id":null,"state_tax_id":null,"verifications":[]},"usps_zone":5,"return_address":{"id":"adr_f01a502a5bef11ec8892ac1f6b0a0d1e","object":"Address","created_at":"2021-12-13T08:37:42+00:00","updated_at":"2021-12-13T08:37:42+00:00","name":"Sam","company":"ShengZhen HYC Co. , Ltd","street1":"255 Park Center Drive","street2":null,"city":"Patterson","state":"CA","zip":"95363","country":"US","phone":"8888888888","email":null,"mode":"production","carrier_facility":null,"residential":null,"federal_tax_id":null,"state_tax_id":null,"verifications":[]},"buyer_address":{"id":"adr_f018aecd5bef11eca937ac1f6bc72124","object":"Address","created_at":"2021-12-13T08:37:42+00:00","updated_at":"2021-12-13T08:37:42+00:00","name":"TEST SAM","company":"TEST COMPANY","street1":"6835 West Buckeye Road","street2":null,"city":"Phoenix","state":"AZ","zip":"85043","country":"US","phone":"8888888888","email":null,"mode":"production","carrier_facility":null,"residential":null,"federal_tax_id":null,"state_tax_id":null,"verifications":[]},"forms":[],"fees":[{"object":"Fee","type":"LabelFee","amount":"0.00000","charged":true,"refunded":false}],"object":"Shipment"}';
            //$shipmentArr = json_decode($jsonStr, true);

            $label = $this->formatLabel($shipmentArr);
            return $this->succeed($label);

            //throw new InternalRequestException('模拟打单抛异常');
        } catch (\Exception $e) {
            \Log::info('EasyPostShipment@createLabel', [$e->getMessage(), $e->getTrace()]);
            return $this->failed($e->getMessage());
        }
    }

    public function formatRate($rate)
    {
        return [
            'shipment_id' => $rate['shipment_id'], // 取消运单的唯一标识
            'carrier' => $rate['carrier'],
            'service' => $rate['service'], // account_service
            'rate' => $rate['rate'],
            'currency' => $rate['currency'],
            'delivery_days' => $rate['delivery_days'],
        ];
    }

    public function formatLabel($shipment)
    {
        $labelFileType = 'png';
        $trackingCode = $shipment['tracker']['tracking_code'];
        $labelPath = 'label/platform/' . $labelFileType . '/' . $trackingCode . '.' . $labelFileType;
        copy($shipment['postage_label']['label_url'], $labelPath);
        return [
            'shipment_id' => $shipment['id'],
            'carrier' => $shipment['selected_rate']['carrier'],
            'service' => $shipment['selected_rate']['service'],
            'rate' => $shipment['selected_rate']['rate'],
            'currency' => $shipment['selected_rate']['currency'],
            'label_file_type' => $labelFileType,
            'label_url' => $shipment['postage_label']['label_url'],
            'tracking_code' => $trackingCode,
            'label_path' => $labelPath,
            'selected_rate' => $this->selectedRate,
            'is_master' => true,
            'label_split' => true,
            'label_qty' => 1,
        ];
    }

    public function refundLabel($shipmentId)
    {
        //return true;
        try {
            EasyPost::setApiKey(env('EASY_POST_KEY'));
            $shipment = \EasyPost\Shipment::retrieve($shipmentId);
            $shipment->refund();
            return true;
        } catch (\Exception $e) {
            \Log::info('EasyPostShipment@refundLabel', [$e->getMessage()]);
            return false;
        }
    }
}
