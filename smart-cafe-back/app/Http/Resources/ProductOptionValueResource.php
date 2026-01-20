<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductOptionValueResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_option_id' => $this->product_option_id,
            'value' => $this->value,
            'price_add_cent_ht' => $this->price_add_cent_ht,
            'price_add_euros' => $this->price_add_euros,
            'option' => new ProductOptionResource($this->whenLoaded('option')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
