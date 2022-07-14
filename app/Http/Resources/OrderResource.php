<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'subsector_id' => $this->subsector_id,
            'subsector' => new SubsectorResource($this->whenLoaded('subsector')),

            'company_id' => $this->company_id,
            'company' => new CustomCompanyResource($this->whenLoaded('customCompany')),

            'po_number' => $this->po_number,
            'make_at' => $this->make_at,

            'order_status_id' => $this->order_status_id,
            'order_status' => new OrderStatusResource($this->whenLoaded('orderStatus')),

            'purchasing_agent' => $this->purchasing_agent,

            'payment_type_id' => $this->payment_type_id,
            'payment_type' => new PaymentTypeResource($this->whenLoaded('paymentType')),

            'order_materials' => OrderMaterialResource::collection($this->whenLoaded('orderMaterials')),
            'order_file' => new OrderFileResource($this->whenLoaded('orderFile')),

            'remark' => $this->remark,
            'created_at' => (string) $this->created_at,
        ];
    }
}
