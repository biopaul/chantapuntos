<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\ChildController;
use App\Models\Action;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response|RedirectResponse
    {
        $user = $request->user();
        $children = $user->childrenAccessible();

        if ($children->isEmpty()) {
            return redirect()->route('onboarding');
        }

        $actionIds = $user->actions()->pluck('id')->toArray();
        $systemActions = Action::whereNull('user_id')->orderBy('name')->get();
        $customActions = $user->actions()->orderBy('name')->get();
        $allActions = $systemActions->merge($customActions)->sortBy('name')->values();

        $canRedeem = $children->contains(fn ($c) => $c->points > 0);

        $childrenWithOwner = $children->map(fn ($c) => array_merge($c->toArray(), ['is_owner' => $c->user_id === $user->id]));

        return Inertia::render('Dashboard', [
            'children' => $childrenWithOwner->values(),
            'actions' => $allActions->values(),
            'actionsPositive' => $allActions->where('points', '>', 0)->values(),
            'actionsNegative' => $allActions->where('points', '<', 0)->values(),
            'canRedeem' => $canRedeem,
            'canInvite' => $user->children()->exists(),
            'availableIcons' => ChildController::availableIcons(),
        ]);
    }
}
