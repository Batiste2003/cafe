<?php

namespace App\Domain\ApiResponse\DTOs;

readonly class ApiSuccessWithPagingResponseDTO
{
    public function __construct(
        public bool $success,
        public string $message,
        public mixed $data,
        public PagingDTO $paging
    ) {}

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->data,
            'paging' => $this->paging->toArray(),
        ];
    }
}
