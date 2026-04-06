<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheckLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'domain_id',
        'checked_at',
        'is_up',
        'http_code',
        'response_time_ms',
        'error_message',
    ];

    protected $casts = [
        'checked_at' => 'datetime',
        'is_up'      => 'boolean',
    ];

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    public function statusLabel(): string
    {
        return $this->is_up ? 'UP' : 'DOWN';
    }

    public function responseTimeSec(): ?string
    {
        return $this->response_time_ms !== null
            ? number_format($this->response_time_ms / 1000, 3) . 's'
            : null;
    }
}
