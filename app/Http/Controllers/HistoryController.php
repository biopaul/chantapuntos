<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\PointTransaction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HistoryController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $childIds = $request->user()->childrenAccessible()->pluck('id');
        $transactions = PointTransaction::with(['child', 'action'])
            ->whereIn('child_id', $childIds)
            ->orderByDesc('created_at')
            ->limit(100)
            ->get();

        return Inertia::render('History/Index', [
            'transactions' => $transactions,
        ]);
    }
}
