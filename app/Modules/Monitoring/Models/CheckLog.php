<?php

namespace App\Modules\Monitoring\Models;

use App\Modules\Domain\Models\Domain;
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

}
