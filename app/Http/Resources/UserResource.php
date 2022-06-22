<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'name' => $this->name,
            'notifications' => NotificationResource::collection($this->whenLoaded('notifications')),
            'phone' => $this->phone,
            'entry_date' => $this->entry_date,
            'created_at' => (string) $this->created_at,

            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'subsectors' => SubsectorResource::collection($this->whenLoaded('subsectors')),
        ];
    }
}
