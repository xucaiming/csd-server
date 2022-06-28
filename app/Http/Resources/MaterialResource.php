<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MaterialResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'custom_code' => $this->custom_code,
            'factory_code' => $this->factory_code,
            'name' => $this->name,
            'material_type_id' => $this->material_type_id,
            'material_type' => new MaterialTypeResource($this->whenLoaded('materialType')),
            'material_unit_id' => $this->material_unit_id,
            'material_unit' => new MaterialUnitResource($this->whenLoaded('materialUnit')),
            'material_drawing' => new MaterialDrawingFileResource($this->whenLoaded('materialDrawingFile')),
            'material_images' => MaterialImageFileResource::collection($this->whenLoaded('materialImageFiles')),
            'created_at' => (string) $this->created_at,
            'created_user' => new UserResource($this->whenLoaded('createdUser')),
            'remark' => $this->remark,
        ];
    }
}
