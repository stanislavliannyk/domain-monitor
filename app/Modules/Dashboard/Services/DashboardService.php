<?php

namespace App\Modules\Dashboard\Services;

use App\Models\User;
use App\Traits\HasServiceError;
use Illuminate\Support\Facades\Log;

class DashboardService
{
    use HasServiceError;

    public function getData(User $user): ?array
    {
        $this->clearError();

        try {
            $domains = $user->domains()->orderBy('name')->get();

            $stats = [
                'total'   => $domains->count(),
                'up'      => $domains->where('status', 'up')->count(),
                'down'    => $domains->where('status', 'down')->count(),
                'unknown' => $domains->where('status', 'unknown')->count(),
            ];

            return compact('stats', 'domains');
        } catch (\Throwable $e) {
            Log::error('Ошибка получения данных дашборда', ['error' => $e->getMessage()]);
            $this->setError('Не удалось загрузить данные.');
            return null;
        }
    }
}
