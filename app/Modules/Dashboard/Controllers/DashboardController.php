<?php

namespace App\Modules\Dashboard\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Dashboard\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardService $service) {}

    public function __invoke(Request $request): JsonResponse
    {
        $data = $this->service->getData($request->user());

        if ($this->service->getError()) {
            return $this->error($this->service->getError());
        }

        return $this->success($data);
    }
}
