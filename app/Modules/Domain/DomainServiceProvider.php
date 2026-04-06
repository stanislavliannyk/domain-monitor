<?php

namespace App\Modules\Domain;

use App\Modules\Domain\Models\Domain;
use App\Modules\Domain\Policies\DomainPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class DomainServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(Domain::class, DomainPolicy::class);
    }
}
