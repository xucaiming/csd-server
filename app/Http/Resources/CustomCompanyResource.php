<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomCompanyResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'short_name' => $this->short_name,
            'subsector_id' => $this->subsector_id,
            'remark' => $this->remark,
            'created_at' => (string) $this->created_at,

            'subsector' => new SubsectorResource($this->whenLoaded('subsector')),

            'factory_parts' => CustomFactoryPartResource::collection($this->whenLoaded('factoryParts')),
        ];
    }
}
