<?php

namespace App\Http\Resources\Sales;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalesDetailResource extends JsonResource
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
            'total_item' => $this->totalItem,
            'price' => $this->Price,
        ];
    }
}

// Error: Call to a member function first() on null in file /Users/mac/Documents/coding/venturo-laravel-onboarding-2024/vendor/laravel/framework/src/Illuminate/Http/Resources/CollectsResources.php on line 34
