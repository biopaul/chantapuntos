<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RedemptionController extends Controller
{
    public function create(Request $request): Response
    {
        $children = $request->user()->childrenAccessible()
            ->where('points', '>', 0)
            ->sortBy('name')
            ->values();

        return Inertia::render('Redemptions/Create', [
            'children' => $children,
            'canjeImageUrl' => asset('images/canje-de-puntos.png'),
        ]);
    }
}
