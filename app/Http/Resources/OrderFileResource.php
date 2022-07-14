<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderFileResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'original_name' => $this->original_name,
            'file_path' => $this->file_path,
        ];
    }
}
