<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        
        return [
            'id'             => $this->id,
            'category_id'    => $this->category_id,
            'category'       => $this->category,
            'price'          => $this->price,
            'user_id'        => $this->user_id,
            'configuration'  => $this->configuration,
            'adders'         => $this->adders,
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,

        ];
    }
}
