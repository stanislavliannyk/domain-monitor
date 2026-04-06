<?php

namespace App\Modules\Dashboard\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $domains = $request->user()
            ->domains()
            ->orderBy('name')
            ->get();

        $stats = [
            'total'   => $domains->count(),
            'up'      => $domains->where('status', 'up')->count(),
            'down'    => $domains->where('status', 'down')->count(),
            'unknown' => $domains->where('status', 'unknown')->count(),
        ];

        return response()->json([
            'data' => [
                'stats'   => $stats,
                'domains' => $domains,
            ],
        ]);
    }
}
