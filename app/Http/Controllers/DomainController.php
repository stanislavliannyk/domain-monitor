<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDomainRequest;
use App\Http\Requests\UpdateDomainRequest;
use App\Jobs\CheckDomainJob;
use App\Models\Domain;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DomainController extends Controller
{
    public function index(Request $request): View
    {
        $domains = $request->user()
            ->domains()
            ->orderBy('name')
            ->paginate(15);

        return view('domains.index', compact('domains'));
    }

    public function create(): View
    {
        return view('domains.create');
    }

    public function store(StoreDomainRequest $request): RedirectResponse
    {
        $domain = $request->user()->domains()->create($request->validated());

        CheckDomainJob::dispatch($domain);

        return redirect()
            ->route('domains.show', $domain)
            ->with('success', 'Domain added. First check has been dispatched.');
    }

    public function show(Domain $domain): View
    {
        $this->authorize('view', $domain);

        $logs = $domain->checkLogs()
            ->latest('checked_at')
            ->paginate(30);

        $stats = [
            'uptime_7d'       => $domain->uptimePercentage(7),
            'avg_response_7d' => $domain->averageResponseTime(7),
        ];

        return view('domains.show', compact('domain', 'logs', 'stats'));
    }

    public function edit(Domain $domain): View
    {
        $this->authorize('update', $domain);

        return view('domains.edit', compact('domain'));
    }

    public function update(UpdateDomainRequest $request, Domain $domain): RedirectResponse
    {
        $domain->update($request->validated());

        return redirect()
            ->route('domains.show', $domain)
            ->with('success', 'Domain updated successfully.');
    }

    public function destroy(Domain $domain): RedirectResponse
    {
        $this->authorize('delete', $domain);

        $domain->delete();

        return redirect()
            ->route('domains.index')
            ->with('success', 'Domain deleted.');
    }

    /** Manually trigger an immediate check for this domain. */
    public function checkNow(Domain $domain): RedirectResponse
    {
        $this->authorize('update', $domain);

        CheckDomainJob::dispatch($domain);

        return back()->with('success', 'Check job dispatched. Results will appear shortly.');
    }
}
