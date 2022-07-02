<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomWindowResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'factory_part_id' => $this->factory_part_id,
            'department_id' => $this->department_id,
            'office_id' => $this->office_id,
            'name' => $this->name,
            'phone' => $this->phone,
            'remark' => $this->remark,
        ];
    }
}
