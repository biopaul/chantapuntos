<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreActionRequest;
use App\Http\Requests\UpdateActionRequest;
use App\Models\Action;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ActionController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $systemActions = Action::whereNull('user_id')->orderBy('name')->get();
        $customActions = $user->actions()->orderBy('name')->get();
        $actions = $systemActions->merge($customActions)->sortBy('name')->values();

        return Inertia::render('Actions/Index', [
            'actions' => $actions,
        ]);
    }

    public function store(StoreActionRequest $request): RedirectResponse
    {
        $request->user()->actions()->create($request->validated());
        return redirect()->route('actions.index')->with('message', 'Acción creada.');
    }

    public function update(UpdateActionRequest $request, Action $action): RedirectResponse
    {
        if ($action->user_id !== null && $action->user_id !== $request->user()->id) {
            abort(403);
        }
        $action->update($request->validated());
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
        return redirect()->route('actions.index')->with('message', 'Acción eliminada.');
    }
}
