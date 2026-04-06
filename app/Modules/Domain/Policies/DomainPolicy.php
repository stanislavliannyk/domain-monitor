<?php

namespace App\Modules\Domain\Policies;

use App\Models\User;
use App\Modules\Domain\Models\Domain;

class DomainPolicy
{
    public function view(User $user, Domain $domain): bool
    {
        return $user->id === $domain->user_id;
    }

    public function update(User $user, Domain $domain): bool
    {
        return $user->id === $domain->user_id;
    }

    public function delete(User $user, Domain $domain): bool
    {
        return $user->id === $domain->user_id;
    }
}
