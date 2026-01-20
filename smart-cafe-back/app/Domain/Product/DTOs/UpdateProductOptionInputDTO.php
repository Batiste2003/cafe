<?php

namespace App\Domain\Product\DTOs;

readonly class UpdateProductOptionInputDTO
{
    public function __construct(
        public ?string $name = null,
        public ?bool $isRequired = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            isRequired: $data['is_required'] ?? null,
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

        return $data;
    }
}
