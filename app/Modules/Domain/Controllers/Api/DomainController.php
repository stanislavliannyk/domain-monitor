<?php

namespace App\Modules\Domain\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Domain\Models\Domain;
use App\Modules\Domain\Requests\StoreDomainRequest;
use App\Modules\Domain\Requests\UpdateDomainRequest;
use App\Modules\Monitoring\Jobs\CheckDomainJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DomainController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $domains = $request->user()
            ->domains()
            ->orderBy('name')
            ->paginate(15);

        return response()->json($domains);
    }

    public function store(StoreDomainRequest $request): JsonResponse
    {
        $domain = $request->user()->domains()->create($request->validated());

        // Сразу ставим первую проверку в очередь
        CheckDomainJob::dispatch($domain);

        return response()->json(['data' => $domain], 201);
    }

    public function show(Domain $domain): JsonResponse
    {
        $this->authorize('view', $domain);

        return response()->json([
            'data' => array_merge($domain->toArray(), [
                'stats' => [
                    'uptime_7d'       => $domain->uptimePercentage(7),
                    'avg_response_7d' => $domain->averageResponseTime(7),
                ],
            ]),
        ]);
    }

    public function update(UpdateDomainRequest $request, Domain $domain): JsonResponse
    {
        $domain->update($request->validated());

        return response()->json(['data' => $domain->fresh()]);
    }

    public function destroy(Domain $domain): JsonResponse
    {
        $this->authorize('delete', $domain);

        $domain->delete();

        return response()->json(['message' => 'Домен удалён.']);
    }

    public function checkNow(Domain $domain): JsonResponse
    {
        $this->authorize('update', $domain);

        CheckDomainJob::dispatch($domain);

        return response()->json(['message' => 'Проверка поставлена в очередь.']);
    }

    public function logs(Domain $domain, Request $request): JsonResponse
    {
        $this->authorize('view', $domain);

        $logs = $domain->checkLogs()
            ->latest('checked_at')
            ->paginate(30);

        return response()->json($logs);
    }
}
