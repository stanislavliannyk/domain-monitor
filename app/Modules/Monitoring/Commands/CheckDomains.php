<?php

namespace App\Modules\Monitoring\Commands;

use App\Modules\Domain\Models\Domain;
use App\Modules\Monitoring\Jobs\CheckDomainJob;
use Illuminate\Console\Command;

class CheckDomains extends Command
{
    protected $signature   = 'domains:check {--domain-id= : Проверить конкретный домен по ID}';
    protected $description = 'Поставить в очередь задачи проверки для всех доменов, у которых наступило время следующей проверки';

    public function handle(): int
    {
        if ($domainId = $this->option('domain-id')) {
            $domain = Domain::active()->find($domainId);

            if (! $domain) {
                $this->error("Активный домен #{$domainId} не найден.");
                return self::FAILURE;
            }

            CheckDomainJob::dispatch($domain);
            $this->info("Задача проверки домена #{$domainId} ({$domain->url}) поставлена в очередь.");

            return self::SUCCESS;
        }

        $domains = Domain::dueForCheck()->get();

        if ($domains->isEmpty()) {
            $this->info('Нет доменов, требующих проверки в данный момент.');
            return self::SUCCESS;
        }

        $count = 0;
        foreach ($domains as $domain) {
            CheckDomainJob::dispatch($domain);
            $count++;
        }

        $this->info("Поставлено в очередь задач проверки: {$count}.");

        return self::SUCCESS;
    }
}
