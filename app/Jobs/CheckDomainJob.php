<?php

namespace App\Jobs;

use App\Models\Domain;
use App\Services\DomainMonitorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckDomainJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Retry up to 2 times before giving up. */
    public int $tries = 2;

    /** Wait 30 s between retries. */
    public int $backoff = 30;

    /** Prevent duplicate concurrent jobs for the same domain. */
    public function uniqueId(): string
    {
        return 'domain:' . $this->domain->id;
    }

    public function __construct(private readonly Domain $domain) {}

    public function handle(DomainMonitorService $monitorService): void
    {
        if (! $this->domain->is_active) {
            return;
        }

        Log::info('Checking domain', ['id' => $this->domain->id, 'url' => $this->domain->url]);

        $result = $monitorService->run($this->domain);

        Log::info('Domain check complete', [
            'id'              => $this->domain->id,
            'is_up'           => $result->isUp,
            'http_code'       => $result->httpCode,
            'response_time_ms' => $result->responseTimeMs,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('CheckDomainJob failed', [
            'domain_id' => $this->domain->id,
            'error'     => $exception->getMessage(),
        ]);
    }
}
