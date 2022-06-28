<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MaterialImageFileResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'uid' => $this->id,
            'file_path' => $this->file_path,
            'url' => config('app.url') . '/'. $this->file_path,
        ];
    }
}
