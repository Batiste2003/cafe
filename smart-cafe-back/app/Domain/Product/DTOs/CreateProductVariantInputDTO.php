<?php

namespace App\Domain\Product\DTOs;

readonly class CreateProductVariantInputDTO
{
    public function __construct(
        public int $productId,
        public string $sku,
        public int $priceCentHt,
        public ?int $costPriceCentHt = null,
        public bool $isDefault = false,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            productId: (int) $data['product_id'],
            sku: $data['sku'],
            priceCentHt: (int) $data['price_cent_ht'],
            costPriceCentHt: isset($data['cost_price_cent_ht']) ? (int) $data['cost_price_cent_ht'] : null,
            isDefault: $data['is_default'] ?? false,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'product_id' => $this->productId,
            'sku' => $this->sku,
            'price_cent_ht' => $this->priceCentHt,
            'cost_price_cent_ht' => $this->costPriceCentHt,
            'is_default' => $this->isDefault,
        ];
    }
}
