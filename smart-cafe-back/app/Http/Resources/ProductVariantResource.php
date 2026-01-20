<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
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
            'sku' => $this->sku,
            'price_cent_ht' => $this->price_cent_ht,
            'price_euros' => $this->price_euros,
            'cost_price_cent_ht' => $this->cost_price_cent_ht,
            'cost_price_euros' => $this->cost_price_euros,
            'is_default' => $this->is_default,
            'store_stocks' => StoreProductVariantResource::collection($this->whenLoaded('storeStocks')),
            'product' => new ProductResource($this->whenLoaded('product')),
            'gallery' => StoredFileResource::collection($this->whenLoaded('gallery')),
            'option_values' => ProductOptionValueResource::collection($this->whenLoaded('optionValues')),
            'is_deleted' => $this->trashed(),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->when($this->trashed(), $this->deleted_at?->format('Y-m-d H:i:s')),
        ];
    }
}
