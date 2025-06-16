<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'thumbnail'      => $this->thumbnail,
            'thumbnail_url'  => $this->thumbnail_url,
            'detail_photo'   => $this->detail_photo,
            'detail_photo_url'   => $this->detail_photo_url,
            'pricing'        => Json_decode($this->pricing),
            'configuration'  => Json_decode($this->configuration),
            'adders'         => $this->adders->map(fn($adder) => [
                'id'    => $adder->id,
                'name'  => $adder->name,
                'price' => $adder->price,
            ]),
        ];
    }
}
