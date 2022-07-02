<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomOfficeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'factory_part_id' => $this->factory_part_id,
            'department_id' => $this->department_id,
            'name' => $this->name,
            'remark' => $this->remark,

            'windows' => CustomWindowResource::collection($this->whenLoaded('windows')),
        ];
    }
}
