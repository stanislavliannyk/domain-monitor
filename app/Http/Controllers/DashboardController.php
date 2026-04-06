<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $domains = $user->domains()
            ->withCount(['checkLogs as total_checks'])
            ->with('checkLogs', fn ($q) => $q->latest('checked_at')->limit(1))
            ->orderBy('name')
            ->get();

        $stats = [
            'total'   => $domains->count(),
            'up'      => $domains->where('status', 'up')->count(),
            'down'    => $domains->where('status', 'down')->count(),
            'unknown' => $domains->where('status', 'unknown')->count(),
        ];

        return view('dashboard', compact('domains', 'stats'));
    }
}
