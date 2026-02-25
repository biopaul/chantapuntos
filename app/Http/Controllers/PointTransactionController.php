<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\Child;
use App\Models\PointTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PointTransactionController extends Controller
{
    public function storeTask(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'child_id' => ['required', 'exists:children,id'],
            'action_id' => ['required', 'exists:actions,id'],
        ]);

        $child = Child::where('id', $validated['child_id'])->where('user_id', $request->user()->id)->firstOrFail();
        $action = Action::where('id', $validated['action_id'])
            ->where(function ($q) use ($request) {
                $q->whereNull('user_id')->orWhere('user_id', $request->user()->id);
            })
            ->firstOrFail();

        $points = (int) $action->points;
        if ($points === 0) {
            throw ValidationException::withMessages(['action_id' => ['La acción no tiene puntaje.']]);
        }

        DB::transaction(function () use ($child, $action, $points) {
            PointTransaction::create([
                'child_id' => $child->id,
                'action_id' => $action->id,
                'points' => $points,
                'type' => PointTransaction::TYPE_TASK,
            ]);
            $child->increment('points', $points);
        });

        return redirect()->route('dashboard')->with('message', 'Puntos actualizados.');
    }

    public function storeRedeem(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'child_id' => ['required', 'exists:children,id'],
            'description' => ['required', 'string', 'max:255'],
            'points' => ['required', 'integer', 'min:1'],
        ]);

        $child = Child::accessibleBy($request->user())->where('id', $validated['child_id'])->firstOrFail();

        if ($child->points < $validated['points']) {
            throw ValidationException::withMessages([
                'points' => ['No tiene suficientes puntos. Tiene ' . $child->points . '.'],
            ]);
        }

        $pointsToDeduct = - (int) $validated['points'];

        DB::transaction(function () use ($child, $validated, $pointsToDeduct) {
            PointTransaction::create([
                'child_id' => $child->id,
                'action_id' => null,
                'points' => $pointsToDeduct,
                'type' => PointTransaction::TYPE_REDEEM,
                'description' => $validated['description'],
            ]);
            $child->increment('points', $pointsToDeduct);
        });

        return redirect()->route('dashboard')->with('message', 'Canje registrado.');
    }
}
