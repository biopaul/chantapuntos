<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\PointTransaction;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Export all user data as JSON (data portability / download before delete).
     */
    public function export(Request $request): \Illuminate\Http\Response
    {
        $user = $request->user();

        $children = $user->childrenAccessible()->orderBy('created_at')->get();
        $childIds = $children->pluck('id')->all();

        $customActions = $user->actions()->orderBy('created_at')->get();

        $transactions = PointTransaction::query()
            ->whereIn('child_id', $childIds)
            ->with(['child:id,name,points', 'action:id,name,points,user_id'])
            ->orderBy('created_at')
            ->get();

        $export = [
            'exportado_en' => now()->toIso8601String(),
            'app' => 'Chanta Puntos',
            'cuenta' => [
                'nombre' => $user->name,
                'email' => $user->email,
                'fecha_registro' => $user->created_at?->toIso8601String(),
            ],
            'hijos' => $children->map(fn ($c) => [
                'nombre' => $c->name,
                'icono' => $c->icon,
                'puntos_actuales' => $c->points,
                'fecha_creacion' => $c->created_at?->toIso8601String(),
            ])->values()->all(),
            'tareas_personalizadas' => $customActions->map(fn ($a) => [
                'nombre' => $a->name,
                'puntos' => $a->points,
                'fecha_creacion' => $a->created_at?->toIso8601String(),
            ])->values()->all(),
            'movimientos_de_puntos' => $transactions->map(fn ($t) => [
                'fecha' => $t->created_at?->toIso8601String(),
                'hijo' => $t->child?->name,
                'tarea_o_concepto' => $t->action?->name ?? $t->description,
                'puntos' => $t->points,
                'tipo' => $t->type,
                'descripcion' => $t->description,
            ])->values()->all(),
        ];

        $json = json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return response($json, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="chantapuntos-datos-' . $user->id . '-' . now()->format('Y-m-d') . '.json"',
        ]);
    }
}
