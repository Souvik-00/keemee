<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdateOwnProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class ProfileController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('checkPermission:profile.view_own', only: ['show', 'edit']),
            new Middleware('checkPermission:profile.update_own', only: ['update']),
        ];
    }

    public function show(): View
    {
        return view('profile.show', [
            'user' => auth()->user()->load('roles'),
        ]);
    }

    public function edit(): View
    {
        return view('profile.edit', [
            'user' => auth()->user(),
        ]);
    }

    public function update(UpdateOwnProfileRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $user = $request->user();

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()
            ->route('profile.show')
            ->with('success', 'Profile updated successfully.');
    }
}
