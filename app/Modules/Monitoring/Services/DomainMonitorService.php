<?php

namespace App\Modules\Monitoring\Services;

use App\Modules\Domain\Models\Domain;
use App\Modules\Monitoring\DTOs\CheckResult;
use App\Modules\Monitoring\Models\CheckLog;
use App\Modules\Monitoring\Notifications\AnonymousNotifiable;
use App\Modules\Monitoring\Notifications\DomainStatusChanged;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DomainMonitorService
{
    public function __construct(
        private readonly DomainCheckService $checker,
    ) {}

    public function run(Domain $domain): CheckResult
    {
        // Сохраняем предыдущий статус до обновления
        $previousStatus = $domain->status;

        $result = $this->checker->check($domain);

        DB::transaction(function () use ($domain, $result) {
            $this->saveLog($domain, $result);
            $this->updateStatus($domain, $result);
        });

        $this->notifyIfStatusChanged($domain, $result, $previousStatus);

        return $result;
    }

    private function saveLog(Domain $domain, CheckResult $result): void
    {
        CheckLog::create([
            'domain_id'        => $domain->id,
            'checked_at'       => now(),
            'is_up'            => $result->isUp,
            'http_code'        => $result->httpCode,
            'response_time_ms' => $result->responseTimeMs,
            'error_message'    => $result->errorMessage,
        ]);
    }

    private function updateStatus(Domain $domain, CheckResult $result): void
    {
        $domain->update([
            'status'          => $result->isUp ? 'up' : 'down',
            'last_checked_at' => now(),
        ]);
    }

    /**
     * Отправляем уведомление только при смене статуса на «down»,
     * а не при каждой неудачной проверке.
     */
    private function notifyIfStatusChanged(Domain $domain, CheckResult $result, string $previousStatus): void
    {
        if (! $domain->notify_on_failure) {
            return;
        }

        if (! $result->isUp && $previousStatus !== 'down') {
            try {
                $notifiable = $domain->notification_email
                    ? new AnonymousNotifiable($domain->notification_email)
                    : $domain->user;

                $notifiable->notify(new DomainStatusChanged($domain, $result));
            } catch (\Throwable $e) {
                Log::error('Не удалось отправить уведомление об изменении статуса домена', [
                    'domain_id' => $domain->id,
                    'ошибка'    => $e->getMessage(),
                ]);
            }
        }
    }
}
