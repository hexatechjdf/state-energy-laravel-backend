<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
            'dial_code' => $this->dial_code,
            'city' => $this->city,
            'zip_code' => $this->zip_code,
            'country' => $this->country,
            'email' => $this->email,
            'contact_id' => $this->contact_id,
            'user_id' => $this->user_id,
            'avatar' => $this->avatar,
            'avatar_url' => $this->avatar_url,
        ];
    }
}
