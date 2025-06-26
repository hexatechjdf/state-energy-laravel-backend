<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray($request): array
    {
        
        return [
            'id'             => $this->id,
            'order_id'             => $this->order_id,
            'category_id'             => $this->category_id,
            'category'       => new CategoryResource($this->category),
            'configuration'             => json_decode($this->configuration),
            'adders'             => json_decode($this->adders),
            'address'             => $this->address,
            'quantity'             => $this->quantity,
            'total_price'             => $this->total_price,
        ];
    }
}
