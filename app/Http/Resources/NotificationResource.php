<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'data' => $this->data,
            'read_at' => (string) $this->read_at,
            'created_at' => (string) $this->created_at,
        ];
    }
}
