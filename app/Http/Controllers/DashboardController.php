<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\ChildController;
use App\Models\Action;
use App\Models\Child;
use App\Models\PointTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response|RedirectResponse
    {
        $user = $request->user();
        $children = Child::accessibleBy($user)
            ->orderBy('created_at')
            ->withSum(
                ['pointTransactions as total_points_earned' => fn ($q) => $q->where('type', PointTransaction::TYPE_TASK)->where('points', '>', 0)],
                'points'
            )
            ->get();

        if ($children->isEmpty()) {
            return redirect()->route('onboarding');
        }

        $systemActions = Cache::remember('system_actions', now()->addHours(24), fn () =>
            Action::whereNull('user_id')->orderBy('name')->get()
        );
        $customActions = Cache::remember("user_actions:{$user->id}", now()->addMinutes(10), fn () =>
            $user->actions()->orderBy('name')->get()
        );
        $allActions = $systemActions->merge($customActions)->sortBy('name')->values();

        $canRedeem = $children->contains(fn ($c) => $c->points > 0);

        $childrenWithOwner = $children->map(function ($c) use ($user) {
            $child = array_merge($c->toArray(), ['is_owner' => $c->user_id === $user->id]);
            if (empty($c->share_token)) {
                $c->update(['share_token' => Str::random(48)]);
                $c->refresh();
            }
            $cardUrl = route('child.card', ['token' => $c->share_token], true);
            $text = 'Mi ficha de puntos: ' . $cardUrl;
            $child['whatsapp_share_url'] = 'https://wa.me/?text=' . rawurlencode($text);
            return $child;
        });

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
