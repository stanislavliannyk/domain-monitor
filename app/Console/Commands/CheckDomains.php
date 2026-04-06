<?php

namespace App\Console\Commands;

use App\Jobs\CheckDomainJob;
use App\Models\Domain;
use Illuminate\Console\Command;

class CheckDomains extends Command
{
    protected $signature   = 'domains:check {--domain-id= : Check a specific domain by ID}';
    protected $description = 'Dispatch check jobs for all domains due for their next check';

    public function handle(): int
    {
        if ($domainId = $this->option('domain-id')) {
            $domain = Domain::active()->find($domainId);

            if (! $domain) {
                $this->error("Active domain #{$domainId} not found.");
                return self::FAILURE;
            }

            CheckDomainJob::dispatch($domain);
            $this->info("Dispatched check for domain #{$domainId}: {$domain->url}");

            return self::SUCCESS;
        }

        $domains = Domain::dueForCheck()->get();

        if ($domains->isEmpty()) {
            $this->info('No domains due for check at this time.');
            return self::SUCCESS;
        }

        $count = 0;
        foreach ($domains as $domain) {
            CheckDomainJob::dispatch($domain);
            $count++;
        }

        $this->info("Dispatched {$count} domain check job(s).");

        return self::SUCCESS;
    }
}
