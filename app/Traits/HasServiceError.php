<?php

namespace App\Traits;

trait HasServiceError
{
    private ?string $error = null;

    public function getError(): ?string
    {
        return $this->error;
    }

    private function setError(string $message): void
    {
        $this->error = $message;
    }

    private function clearError(): void
    {
        $this->error = null;
    }
}
