<?php

namespace Database\Seeders;

use App\Models\Domain;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        $domains = [
            ['name' => 'Google', 'url' => 'https://google.com'],
            ['name' => 'GitHub', 'url' => 'https://github.com'],
            ['name' => 'Laravel', 'url' => 'https://laravel.com'],
        ];

        foreach ($domains as $domain) {
            Domain::create([
                'user_id'          => $user->id,
                'name'             => $domain['name'],
                'url'              => $domain['url'],
                'check_interval'   => 5,
                'request_timeout'  => 10,
                'check_method'     => 'HEAD',
                'is_active'        => true,
                'notify_on_failure' => false,
            ]);
        }
    }
}
