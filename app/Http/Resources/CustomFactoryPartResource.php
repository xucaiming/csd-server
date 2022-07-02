<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomFactoryPartResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'name' => $this->name,
            'remark' => $this->remark,

            'departments' => CustomDepartmentResource::collection($this->whenLoaded('departments')),
        ];
    }
}
