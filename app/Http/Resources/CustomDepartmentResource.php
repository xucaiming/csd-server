<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomDepartmentResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'factory_part_id' => $this->factory_part_id,
            'name' => $this->name,
            'remark' => $this->remark,

            'offices' => CustomOfficeResource::collection($this->whenLoaded('offices')),
        ];
    }
}
