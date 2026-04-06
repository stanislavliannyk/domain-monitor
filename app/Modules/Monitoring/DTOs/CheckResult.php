<?php

namespace App\Modules\Monitoring\DTOs;

final readonly class CheckResult
{
    public function __construct(
        public bool    $isUp,
        public ?int    $httpCode,
        public ?int    $responseTimeMs,
        public ?string $errorMessage,
    ) {}

    public static function success(int $httpCode, int $responseTimeMs): self
    {
        return new self(
            isUp:           true,
            httpCode:       $httpCode,
            responseTimeMs: $responseTimeMs,
            errorMessage:   null,
        );
    }

    public static function failure(?int $httpCode, string $errorMessage): self
    {
        return new self(
            isUp:           false,
            httpCode:       $httpCode,
            responseTimeMs: null,
            errorMessage:   $errorMessage,
        );
    }
}
