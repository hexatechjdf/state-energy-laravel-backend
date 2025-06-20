<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray($request): array
    {

        return [
            'id'             => $this->id,
            'category_id'    => $this->category_id,
            'category'       => new CategoryResource($this->category),
            'price'          => $this->price,
            'user_id'        => $this->user_id,
            'configuration'  => $this->configuration,
            'configuration_meta'  => $this->configuration_meta,
            'pricing_meta'  => $this->pricing_meta,
            'adders'         => $this->adders,
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,

        ];
    }
}
