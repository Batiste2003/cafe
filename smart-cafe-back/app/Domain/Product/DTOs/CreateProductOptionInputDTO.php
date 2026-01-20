<?php

namespace App\Domain\Product\DTOs;

readonly class CreateProductOptionInputDTO
{
    public function __construct(
        public string $name,
        public bool $isRequired = false,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            isRequired: $data['is_required'] ?? false,
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
        ];
    }
}
