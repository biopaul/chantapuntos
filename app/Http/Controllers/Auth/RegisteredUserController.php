<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Si este correo tiene una invitación pendiente, aplicarla de inmediato (solo quien se registra con ese correo obtiene el acceso).
        $pendingInvitation = Invitation::where('email', $user->email)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->first();
        if ($pendingInvitation) {
            $pendingInvitation->acceptFor($user);
            return redirect()->route('dashboard')
                ->with('message', 'Cuenta creada. Ya podés ver y gestionar los hijos que te compartieron.');
        }

        // Solo respetar la URL "intended" si apunta a una invitación; en cualquier
        // otro caso ir siempre al dashboard para evitar redirecciones inesperadas.
        $intended = $request->session()->pull('url.intended');
        if ($intended && str_contains($intended, '/invitations/accept')) {
            return redirect($intended);
        }

        return redirect()->route('dashboard');
    }
}
