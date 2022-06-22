<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class RoleResource extends JsonResource
{

    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'desc' => $this->desc,
            'status' => $this->status,
            // 'menu' => $this->permissions->pluck('id')
            'permissions' => $this->whenLoaded('permissions'),
        ];
        return $data;
    }
}
