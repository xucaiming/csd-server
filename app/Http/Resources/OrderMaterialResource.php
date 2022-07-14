<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderMaterialResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'factory_part_id' => $this->factory_part_id,
            'factory_part' => new CustomFactoryPartResource($this->whenLoaded('customFactoryPart')),

            'department_id' => $this->department_id,
            'department' => new CustomDepartmentResource($this->whenLoaded('customDepartment')),

            'office_id' => $this->office_id,
            'office' => new CustomOfficeResource($this->whenLoaded('customOffice')),

            'window_id' => $this->window_id,
            'window' => new CustomWindowResource($this->whenLoaded('customWindow')),

            'material_id' => $this->material_id,
            'material' => new MaterialResource($this->whenLoaded('material')),

            'material_code' => $this->material->code,
            'material_name' => $this->material->name,
            'material_type_id' => $this->material->material_type_id,
            'material_unit_id' => $this->material->material_unit_id,

            'order_id' => $this->order_id,
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'tax_rate' => $this->tax_rate,
            'tax_unit_price' => $this->tax_unit_price,
            'total_price' => $this->total_price,
            'total_rate_price' => $this->total_rate_price,
            'delivery_date' => $this->delivery_date,
        ];
    }
}
