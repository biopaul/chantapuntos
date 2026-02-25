<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Child;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ChildCardController extends Controller
{
    public function show(Request $request, string $token): Response|HttpResponse
    {
        $child = Child::where('share_token', $token)->firstOrFail();

        $transactions = $child->pointTransactions()
            ->with('action')
            ->orderByDesc('created_at')
            ->limit(100)
            ->get();

        return Inertia::render('ChildCard/Show', [
            'child' => $child,
            'transactions' => $transactions,
        ]);
    }
}
