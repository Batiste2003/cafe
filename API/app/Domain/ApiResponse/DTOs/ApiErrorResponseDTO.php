<?php

namespace App\Domain\ApiResponse\DTOs;

readonly class ApiErrorResponseDTO
{
    public function __construct(
        public bool $success,
        public string $message,
        public string $errorCode
    ) {}

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'error_code' => $this->errorCode,
        ];
    }
}
