<?php

namespace App\Notifications;

use App\DTOs\CheckResult;
use App\Models\Domain;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DomainStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Domain      $domain,
        private readonly CheckResult $result,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusLabel = $this->result->isUp ? 'recovered (UP)' : 'went DOWN';

        return (new MailMessage)
            ->subject("[Domain Monitor] {$this->domain->name} {$statusLabel}")
            ->greeting("Alert: {$this->domain->name}")
            ->line("Your domain **{$this->domain->url}** has {$statusLabel}.")
            ->line($this->result->httpCode
                ? "HTTP status code: {$this->result->httpCode}"
                : "Error: {$this->result->errorMessage}")
            ->action('View Details', route('domains.show', $this->domain))
            ->line('This notification was sent by Domain Monitor.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'domain_id' => $this->domain->id,
            'is_up'     => $this->result->isUp,
            'http_code' => $this->result->httpCode,
        ];
    }
}
