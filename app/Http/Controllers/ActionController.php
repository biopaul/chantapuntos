<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreActionRequest;
use App\Http\Requests\UpdateActionRequest;
use App\Models\Action;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class ActionController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $systemActions = Cache::remember('system_actions', now()->addHours(24), fn () =>
            Action::whereNull('user_id')->orderBy('name')->get()
        );
        $customActions = Cache::remember("user_actions:{$user->id}", now()->addMinutes(10), fn () =>
            $user->actions()->orderBy('name')->get()
        );
        $actions = $systemActions->merge($customActions)->sortBy('name')->values();

        return Inertia::render('Actions/Index', [
            'actions' => $actions,
        ]);
    }

    public function store(StoreActionRequest $request): RedirectResponse
    {
        $request->user()->actions()->create($request->validated());
        Cache::forget("user_actions:{$request->user()->id}");
        return redirect()->route('actions.index')->with('message', 'Acción creada.');
    }

    public function update(UpdateActionRequest $request, Action $action): RedirectResponse
    {
        if ($action->user_id !== null && $action->user_id !== $request->user()->id) {
            abort(403);
        }
        $action->update($request->validated());
        if ($action->user_id === null) {
            Cache::forget('system_actions');
        } else {
            Cache::forget("user_actions:{$request->user()->id}");
        }
        return redirect()->route('actions.index')->with('message', 'Acción actualizada.');
    }

    public function destroy(Request $request, Action $action): RedirectResponse
    {
        if ($action->user_id === null) {
            abort(403, 'No se pueden eliminar acciones del sistema.');
        }
        if ($action->user_id !== $request->user()->id) {
            abort(403);
        }
        $action->delete();
        Cache::forget("user_actions:{$request->user()->id}");
        return redirect()->route('actions.index')->with('message', 'Acción eliminada.');
    }
}
