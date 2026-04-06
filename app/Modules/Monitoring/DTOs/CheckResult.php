<?php

namespace App\Modules\Monitoring\DTOs;

final class CheckResult
{
    public function __construct(
        public readonly bool    $isUp,
        public readonly ?int    $httpCode,
        public readonly ?int    $responseTimeMs,
        public readonly ?string $errorMessage,
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
