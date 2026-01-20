<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'category' => new ProductCategoryResource($this->whenLoaded('category')),
            'stores' => StoreResource::collection($this->whenLoaded('stores')),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'variants' => ProductVariantResource::collection($this->whenLoaded('variants')),
            'options' => ProductOptionResource::collection($this->whenLoaded('options')),
            'gallery' => StoredFileResource::collection($this->whenLoaded('gallery')),
            'default_variant' => $this->when(
                $this->relationLoaded('variants'),
                fn () => new ProductVariantResource($this->defaultVariant())
            ),
            'variants_count' => $this->when(isset($this->variants_count), $this->variants_count),
            'is_deleted' => $this->trashed(),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->when($this->trashed(), $this->deleted_at?->format('Y-m-d H:i:s')),
        ];
    }
}
