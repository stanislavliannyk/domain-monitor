<?php

namespace App\Modules\Domain\Models;

use App\Models\User;
use App\Modules\Monitoring\Models\CheckLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Domain extends Model
{
    protected $fillable = [
        'name',
        'url',
        'check_interval',
        'request_timeout',
        'check_method',
        'is_active',
        'status',
        'last_checked_at',
        'notify_on_failure',
        'notification_email',
    ];

    protected $casts = [
        'is_active'         => 'boolean',
        'notify_on_failure' => 'boolean',
        'last_checked_at'   => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function checkLogs(): HasMany
    {
        return $this->hasMany(CheckLog::class);
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /** Domains whose next check time has arrived. */
    public function scopeDueForCheck(Builder $query): void
    {
        $query->active()->where(function (Builder $q) {
            $q->whereNull('last_checked_at')
              ->orWhereRaw("last_checked_at + (check_interval * interval '1 minute') <= NOW()");
        });
    }

    public function uptimePercentage(int $days = 7): float
    {
        $total = $this->checkLogs()
            ->where('checked_at', '>=', now()->subDays($days))
            ->count();

        if ($total === 0) {
            return 0.0;
        }

        $up = $this->checkLogs()
            ->where('checked_at', '>=', now()->subDays($days))
            ->where('is_up', true)
            ->count();

        return round(($up / $total) * 100, 2);
    }

    public function averageResponseTime(int $days = 7): ?float
    {
        return $this->checkLogs()
            ->where('checked_at', '>=', now()->subDays($days))
            ->where('is_up', true)
            ->avg('response_time_ms');
    }
}
