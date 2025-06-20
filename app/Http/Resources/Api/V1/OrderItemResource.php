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
            'configuration'             => json_decode($this->configuration),
            'adders'             => json_decode($this->adders),
            'address'             => $this->address,
            'unit_price'             => $this->unit_price,
            'quantity'             => $this->quantity,
            'total_price'             => $this->total_price,
        ];
    }
}
