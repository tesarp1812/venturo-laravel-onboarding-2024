<?php

namespace App\Http\Resources\Sales;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'm_customers_id' => $this->m_customer_id,
            'product_details' => SalesDetailResource::collection($this->details)
        ];
    }
}
