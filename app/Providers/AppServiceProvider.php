<?php

namespace App\Providers;

use App\Models\Domain;
use App\Policies\DomainPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Register policies
        Gate::policy(Domain::class, DomainPolicy::class);

        // Default password strength rules
        Password::defaults(fn () => Password::min(8)->letters()->numbers());
    }
}
