<?php

namespace App\Http\Controllers;

use App\Http\Requests\Common\ActionRequest;
use App\Http\Requests\Subscriber\StoreSubscriberRequest;
use App\Http\Requests\Subscriber\UpdateSubscriberRequest;
use App\Models\Subscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class SubscriberController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('checkPermission:subscribers.view', only: ['index', 'show']),
            new Middleware('checkPermission:subscribers.manage', except: ['index', 'show']),
        ];
    }

    public function index(Request $request): View
    {
        $subscribers = Subscriber::query()
            ->when($request->string('q')->toString(), function ($query, $search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('subscribers.index', compact('subscribers'));
    }

    public function create(): View
    {
        return view('subscribers.create');
    }

    public function store(StoreSubscriberRequest $request): RedirectResponse
    {
        Subscriber::query()->create($request->validated());

        return redirect()
            ->route('subscribers.index')
            ->with('success', 'Subscriber created successfully.');
    }

    public function edit(Subscriber $subscriber): View
    {
        return view('subscribers.edit', compact('subscriber'));
    }

    public function update(UpdateSubscriberRequest $request, Subscriber $subscriber): RedirectResponse
    {
        $subscriber->update($request->validated());

        return redirect()
            ->route('subscribers.index')
            ->with('success', 'Subscriber updated successfully.');
    }

    public function destroy(ActionRequest $request, Subscriber $subscriber): RedirectResponse
    {
        $subscriber->delete();

        return redirect()
            ->route('subscribers.index')
            ->with('success', 'Subscriber deleted successfully.');
    }
}
