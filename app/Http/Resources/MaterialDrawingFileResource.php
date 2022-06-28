<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MaterialDrawingFileResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'name' => $this->original_name,
            'original_name' => $this->original_name,
            'file_path' => $this->file_path,
            'url' => config('app.url') . '/'. $this->file_path,

            'created_user' => new UserResource($this->whenLoaded('createdUser')),
        ];
    }
}
