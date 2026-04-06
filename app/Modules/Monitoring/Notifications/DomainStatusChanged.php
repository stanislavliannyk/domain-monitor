<?php

namespace App\Modules\Monitoring\Notifications;

use App\Modules\Domain\Models\Domain;
use App\Modules\Monitoring\DTOs\CheckResult;
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
        $statusLabel = $this->result->isUp ? 'восстановлен (РАБОТАЕТ)' : 'недоступен (DOWN)';

        return (new MailMessage)
            ->subject("[Монитор доменов] {$this->domain->name} — {$statusLabel}")
            ->greeting("Оповещение: {$this->domain->name}")
            ->line("Домен **{$this->domain->url}** стал {$statusLabel}.")
            ->line($this->result->httpCode
                ? "HTTP-код ответа: {$this->result->httpCode}"
                : "Ошибка: {$this->result->errorMessage}")
            ->action('Открыть в Мониторе доменов', config('app.url') . '/domains/' . $this->domain->id)
            ->line('Это уведомление отправлено системой мониторинга доменов.');
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
