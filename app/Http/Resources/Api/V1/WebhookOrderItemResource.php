<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class WebhookOrderItemResource extends JsonResource
{
    public function toArray($request): array
    {

        return [
            'id'             => $this->id,
            'item_detail'             => json_decode($this->configuration),
            'adders_detail'             => json_decode($this->adders),
            'quantity'             => $this->quantity,
            'total_price'             => $this->total_price,
        ];
    }
}
