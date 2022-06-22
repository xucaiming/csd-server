<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\FeeItemRequest;
use App\Http\Resources\FeeItemResource;
use App\Models\FeeItem;
use Illuminate\Http\Request;

class FeeItemController extends Controller
{
    public function index()
    {
        $feeItems = FeeItem::query()
            ->orderBy('id', 'desc')
            ->get();
        return $this->success(FeeItemResource::collection($feeItems));
    }

    public function store(FeeItemRequest $request)
    {
        $saveData = $request->all();

        $feeItem = new FeeItem($saveData);
        $feeItem->save();

        return $this->success(new FeeItemResource($feeItem->refresh()));
    }

    public function update(FeeItemRequest $request, FeeItem $feeItem)
    {
        $saveData = $request->all();

        $feeItem->fill($saveData);
        $feeItem->save();

        return $this->success(new FeeItemResource($feeItem->refresh()));
    }

    public function destroy(FeeItem $feeItem)
    {
        $feeItem->delete();
        return $this->success();
    }
}
