<?php

namespace App\Domain\Product\DTOs;

readonly class CreateProductOptionInputDTO
{
    /**
     * @param  array<int, array{value: string, price_add_cent_ht?: int}>  $values
     */
    public function __construct(
        public string $name,
        public bool $isRequired = false,
        public array $values = [],
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            isRequired: $data['is_required'] ?? false,
            values: $data['values'] ?? [],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'is_required' => $this->isRequired,
            'values' => $this->values,
        ];
    }
}
