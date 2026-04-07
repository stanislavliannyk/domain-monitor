<?php

namespace App\Modules\Monitoring\Jobs;

use App\Modules\Domain\Models\Domain;
use App\Modules\Monitoring\Services\DomainMonitorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckDomainJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $backoff = 30;

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

        Log::info('Проверка домена', ['id' => $this->domain->id, 'url' => $this->domain->url]);

        $result = $monitorService->run($this->domain);

        if ($monitorService->getError()) {
            Log::error('Мониторинг завершился с ошибкой', [
                'id'     => $this->domain->id,
                'ошибка' => $monitorService->getError(),
            ]);
            return;
        }

        Log::info('Проверка завершена', [
            'id'              => $this->domain->id,
            'доступен'        => $result->isUp,
            'http_код'        => $result->httpCode,
            'время_ответа_мс' => $result->responseTimeMs,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Ошибка выполнения CheckDomainJob', [
            'domain_id' => $this->domain->id,
            'ошибка'    => $exception->getMessage(),
        ]);
    }
}
