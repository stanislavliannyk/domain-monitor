<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Domain\Models\Domain;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Администратор',
                'password' => bcrypt('password'),
            ]
        );

        $domains = [
            ['name' => 'Google',  'url' => 'https://google.com'],
            ['name' => 'GitHub',  'url' => 'https://github.com'],
            ['name' => 'Laravel', 'url' => 'https://laravel.com'],
        ];

        foreach ($domains as $domain) {
            Domain::create([
                'user_id'           => $user->id,
                'name'              => $domain['name'],
                'url'               => $domain['url'],
                'check_interval'    => 5,
                'request_timeout'   => 10,
                'check_method'      => 'HEAD',
                'is_active'         => true,
                'notify_on_failure' => false,
            ]);
        }
    }
}
