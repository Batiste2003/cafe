<?php

namespace App\Domain\Product\DTOs;

readonly class UpdateProductOptionInputDTO
{
    /**
     * @param  array<int, array{value: string, price_add_cent_ht?: int}>|null  $values
     */
    public function __construct(
        public ?string $name = null,
        public ?bool $isRequired = null,
        public ?array $values = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            isRequired: $data['is_required'] ?? null,
            values: $data['values'] ?? null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [];

        if ($this->name !== null) {
            $data['name'] = $this->name;
        }

        if ($this->isRequired !== null) {
            $data['is_required'] = $this->isRequired;
        }

        if ($this->values !== null) {
            $data['values'] = $this->values;
        }

        return $data;
    }
}
