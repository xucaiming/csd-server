<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubsectorResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => boolval($this->status),
            'remark' => $this->remark,
            'created_at' => (string) $this->created_at,

            'users_count' => $this->when($this->users_count, $this->users_count),
        ];
    }
}
