<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\PointTransaction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AchievementsController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        $children = Child::accessibleBy($user)
            ->orderBy('created_at')
            ->withSum(
                ['pointTransactions as total_points_earned' => fn ($q) => $q->where('type', PointTransaction::TYPE_TASK)->where('points', '>', 0)],
                'points'
            )
            ->get()
            ->map(fn ($c) => array_merge($c->toArray(), [
                'is_owner' => $c->user_id === $user->id,
            ]));

        return Inertia::render('Achievements/Index', [
            'children' => $children->values(),
        ]);
    }
}
