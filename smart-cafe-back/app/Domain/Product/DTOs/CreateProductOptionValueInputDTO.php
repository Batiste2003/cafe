<?php

namespace App\Domain\Product\DTOs;

readonly class CreateProductOptionValueInputDTO
{
    public function __construct(
        public int $productOptionId,
        public string $value,
        public int $priceAddCentHt = 0,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            productOptionId: (int) $data['product_option_id'],
            value: $data['value'],
            priceAddCentHt: (int) ($data['price_add_cent_ht'] ?? 0),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'product_option_id' => $this->productOptionId,
            'value' => $this->value,
            'price_add_cent_ht' => $this->priceAddCentHt,
        ];
    }
}
