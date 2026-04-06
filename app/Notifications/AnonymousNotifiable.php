<?php

namespace App\Notifications;

use Illuminate\Notifications\Notifiable;

/**
 * Lightweight notifiable for sending to an arbitrary email address
 * without requiring an Eloquent model.
 */
class AnonymousNotifiable
{
    use Notifiable;

    public function __construct(private readonly string $email) {}

    public function routeNotificationForMail(): string
    {
        return $this->email;
    }
}
