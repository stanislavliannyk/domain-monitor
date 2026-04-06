<?php

namespace App\Modules\Domain\Services;

use App\Models\User;
use App\Modules\Domain\Models\Domain;
use App\Modules\Monitoring\Jobs\CheckDomainJob;
use App\Traits\HasServiceError;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class DomainService
{
    use HasServiceError;

    public function paginate(User $user): ?LengthAwarePaginator
    {
        $this->clearError();

        try {
            return $user->domains()->orderBy('name')->paginate(15);
        } catch (\Throwable $e) {
            Log::error('Ошибка получения списка доменов', ['error' => $e->getMessage()]);
            $this->setError('Не удалось загрузить домены.');
            return null;
        }
    }

    public function create(User $user, array $data): ?Domain
    {
        $this->clearError();

        try {
            $domain = $user->domains()->create($data);
            CheckDomainJob::dispatch($domain);
            return $domain;
        } catch (\Throwable $e) {
            Log::error('Ошибка создания домена', ['error' => $e->getMessage()]);
            $this->setError('Не удалось добавить домен.');
            return null;
        }
    }

    public function get(Domain $domain): ?array
    {
        $this->clearError();

        try {
            return array_merge($domain->toArray(), [
                'stats' => [
                    'uptime_7d'       => $domain->uptimePercentage(7),
                    'avg_response_7d' => $domain->averageResponseTime(7),
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Ошибка получения домена', ['domain_id' => $domain->id, 'error' => $e->getMessage()]);
            $this->setError('Не удалось загрузить домен.');
            return null;
        }
    }

    public function update(Domain $domain, array $data): ?Domain
    {
        $this->clearError();

        try {
            $domain->update($data);
            return $domain->fresh();
        } catch (\Throwable $e) {
            Log::error('Ошибка обновления домена', ['domain_id' => $domain->id, 'error' => $e->getMessage()]);
            $this->setError('Не удалось обновить домен.');
            return null;
        }
    }

    public function delete(Domain $domain): bool
    {
        $this->clearError();

        try {
            $domain->delete();
            return true;
        } catch (\Throwable $e) {
            Log::error('Ошибка удаления домена', ['domain_id' => $domain->id, 'error' => $e->getMessage()]);
            $this->setError('Не удалось удалить домен.');
            return false;
        }
    }

    public function scheduleCheck(Domain $domain): bool
    {
        $this->clearError();

        try {
            CheckDomainJob::dispatch($domain);
            return true;
        } catch (\Throwable $e) {
            Log::error('Ошибка постановки задачи проверки', ['domain_id' => $domain->id, 'error' => $e->getMessage()]);
            $this->setError('Не удалось поставить задачу в очередь.');
            return false;
        }
    }

    public function paginateLogs(Domain $domain): ?LengthAwarePaginator
    {
        $this->clearError();

        try {
            return $domain->checkLogs()->latest('checked_at')->paginate(30);
        } catch (\Throwable $e) {
            Log::error('Ошибка получения логов домена', ['domain_id' => $domain->id, 'error' => $e->getMessage()]);
            $this->setError('Не удалось загрузить логи.');
            return null;
        }
    }
}
