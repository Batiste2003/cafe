<?php

namespace App\Domain\Product\DTOs;

readonly class UpdateProductOptionValueInputDTO
{
    public function __construct(
        public ?string $value = null,
        public ?int $priceAddCentHt = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            value: $data['value'] ?? null,
            priceAddCentHt: isset($data['price_add_cent_ht']) ? (int) $data['price_add_cent_ht'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [];

        if ($this->value !== null) {
            $data['value'] = $this->value;
        }

        if ($this->priceAddCentHt !== null) {
            $data['price_add_cent_ht'] = $this->priceAddCentHt;
        }

        return $data;
    }
}
