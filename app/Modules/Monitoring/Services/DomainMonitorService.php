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
        $previousStatus = $domain->status;

        $result = $this->checker->check($domain);

        DB::transaction(function () use ($domain, $result) {
            $this->persistLog($domain, $result);
            $this->updateDomainStatus($domain, $result);
        });

        $this->dispatchNotificationIfNeeded($domain, $result, $previousStatus);

        return $result;
    }

    private function persistLog(Domain $domain, CheckResult $result): void
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

    private function updateDomainStatus(Domain $domain, CheckResult $result): void
    {
        $domain->update([
            'status'          => $result->isUp ? 'up' : 'down',
            'last_checked_at' => now(),
        ]);
    }

    private function dispatchNotificationIfNeeded(
        Domain      $domain,
        CheckResult $result,
        string      $previousStatus,
    ): void {
        if (! $domain->notify_on_failure) {
            return;
        }

        // Notify only on transition to "down"
        if (! $result->isUp && $previousStatus !== 'down') {
            try {
                $notifiable = $domain->notification_email
                    ? new AnonymousNotifiable($domain->notification_email)
                    : $domain->user;

                $notifiable->notify(new DomainStatusChanged($domain, $result));
            } catch (\Throwable $e) {
                Log::error('Failed to send domain status notification', [
                    'domain_id' => $domain->id,
                    'error'     => $e->getMessage(),
                ]);
            }
        }
    }
}
