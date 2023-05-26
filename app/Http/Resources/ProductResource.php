<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Product */
class ProductResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'sku' => $this->sku,
            'name' => $this->name,
            'price' => $this->price,
            'amount' => $this->amount,
            'description' => $this->description,
            'additional_info' => $this->additional_info,
            'avg_rating' => $this->avg_rating,
            'tags' => $this->tags,
        ];
    }
}
