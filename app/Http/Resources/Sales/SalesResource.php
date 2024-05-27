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
        $data = [
            'id' => $this->id,
            'm_customer_id' => $this->m_customer_id,
            'date' => $this->date,
        ];
    
        // Memeriksa apakah details tidak null sebelum membuat collection
        if ($this->details !== null) {
            $data['details'] = SalesDetailResource::collection($this->details);
        }
    
        return $data;
    }
}
