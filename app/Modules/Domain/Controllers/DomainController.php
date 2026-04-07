<?php

namespace App\Modules\Domain\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Domain\Models\Domain;
use App\Modules\Domain\Requests\StoreDomainRequest;
use App\Modules\Domain\Requests\UpdateDomainRequest;
use App\Modules\Domain\Services\DomainService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DomainController extends Controller
{
    public function __construct(private readonly DomainService $service) {}

    public function index(Request $request): JsonResponse
    {
        $domains = $this->service->paginate($request->user());

        if ($this->service->getError()) {
            return $this->error($this->service->getError());
        }

        return response()->json([
            'data' => $domains->items(),
            'meta' => [
                'current_page' => $domains->currentPage(),
                'last_page'    => $domains->lastPage(),
                'from'         => $domains->firstItem(),
                'to'           => $domains->lastItem(),
                'total'        => $domains->total(),
            ],
        ]);
    }

    public function store(StoreDomainRequest $request): JsonResponse
    {
        $domain = $this->service->create($request->user(), $request->validated());

        if ($this->service->getError()) {
            return $this->error($this->service->getError());
        }

        return $this->success($domain, 201);
    }

    public function show(Domain $domain): JsonResponse
    {
        $this->authorize('view', $domain);

        $data = $this->service->get($domain);

        if ($this->service->getError()) {
            return $this->error($this->service->getError());
        }

        return $this->success($data);
    }

    public function update(UpdateDomainRequest $request, Domain $domain): JsonResponse
    {
        $updated = $this->service->update($domain, $request->validated());

        if ($this->service->getError()) {
            return $this->error($this->service->getError());
        }

        return $this->success($updated);
    }

    public function destroy(Domain $domain): JsonResponse
    {
        $this->authorize('delete', $domain);

        $this->service->delete($domain);

        if ($this->service->getError()) {
            return $this->error($this->service->getError());
        }

        return response()->json(['message' => 'Домен удалён.']);
    }

    public function checkNow(Domain $domain): JsonResponse
    {
        $this->authorize('update', $domain);

        $this->service->scheduleCheck($domain);

        if ($this->service->getError()) {
            return $this->error($this->service->getError());
        }

        return response()->json(['message' => 'Проверка поставлена в очередь.']);
    }

    public function logs(Domain $domain): JsonResponse
    {
        $this->authorize('view', $domain);

        $logs = $this->service->paginateLogs($domain);

        if ($this->service->getError()) {
            return $this->error($this->service->getError());
        }

        return response()->json([
            'data' => $logs->items(),
            'meta' => [
                'current_page' => $logs->currentPage(),
                'last_page'    => $logs->lastPage(),
                'from'         => $logs->firstItem(),
                'to'           => $logs->lastItem(),
                'total'        => $logs->total(),
            ],
        ]);
    }
}
