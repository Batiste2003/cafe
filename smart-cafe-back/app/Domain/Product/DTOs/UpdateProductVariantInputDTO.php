<?php

namespace App\Domain\Product\DTOs;

readonly class UpdateProductVariantInputDTO
{
    public function __construct(
        public ?string $sku = null,
        public ?int $priceCentHt = null,
        public ?int $costPriceCentHt = null,
        public ?int $stock = null,
        public ?bool $isDefault = null,
        public bool $clearCostPrice = false,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            sku: $data['sku'] ?? null,
            priceCentHt: isset($data['price_cent_ht']) ? (int) $data['price_cent_ht'] : null,
            costPriceCentHt: isset($data['cost_price_cent_ht']) ? (int) $data['cost_price_cent_ht'] : null,
            stock: isset($data['stock']) ? (int) $data['stock'] : null,
            isDefault: $data['is_default'] ?? null,
            clearCostPrice: array_key_exists('cost_price_cent_ht', $data) && $data['cost_price_cent_ht'] === null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [];

        if ($this->sku !== null) {
            $data['sku'] = $this->sku;
        }

        if ($this->priceCentHt !== null) {
            $data['price_cent_ht'] = $this->priceCentHt;
        }

        if ($this->costPriceCentHt !== null || $this->clearCostPrice) {
            $data['cost_price_cent_ht'] = $this->costPriceCentHt;
        }

        if ($this->stock !== null) {
            $data['stock'] = $this->stock;
        }

        if ($this->isDefault !== null) {
            $data['is_default'] = $this->isDefault;
        }

        return $data;
    }
}
