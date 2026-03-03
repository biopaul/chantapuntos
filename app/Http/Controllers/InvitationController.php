<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Mail\InvitationMail;
use App\Models\Invitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class InvitationController extends Controller
{
    public function index(Request $request): Response|RedirectResponse
    {
        $user = $request->user();
        if (! $user->children()->exists()) {
            return redirect()->route('dashboard');
        }

        $invitations = Invitation::where('inviter_id', $user->id)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('Invitations/Index', [
            'invitations' => $invitations,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user->children()->exists()) {
            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'email' => ['required', 'email'],
            'method' => ['required', 'in:email,whatsapp'],
        ]);

        $invitation = Invitation::create([
            'inviter_id' => $user->id,
            'email' => $validated['email'],
            'token' => Str::random(48),
            'expires_at' => now()->addDays(7),
        ]);

        $acceptUrl = route('invitations.accept', ['token' => $invitation->token], true);

        if ($validated['method'] === 'email') {
            try {
                Mail::to($validated['email'])->send(new InvitationMail(
                    $invitation,
                    $acceptUrl,
                    $user->name
                ));
                return redirect()
                    ->route('invitations.index')
                    ->with('message', 'Invitación enviada por correo a ' . $validated['email'] . '.');
            } catch (\Throwable $e) {
                report($e);
                return redirect()
                    ->route('invitations.index')
                    ->with('error', 'No se pudo enviar el correo. Revisá la configuración SMTP o compartí el enlace por WhatsApp.');
            }
        }

        return redirect()
            ->route('invitations.index')
            ->with('message', 'Enlace creado. Compartilo por WhatsApp.')
            ->with('new_invitation_url', $acceptUrl);
    }

    public function showAccept(Request $request, string $token): Response|RedirectResponse
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if ($invitation->isAccepted()) {
            return redirect()->route('dashboard')->with('message', 'Esta invitación ya fue aceptada.');
        }

        if ($invitation->isExpired()) {
            return redirect()->route('dashboard')->with('error', 'Esta invitación ha expirado.');
        }

        if (! $request->user()) {
            return redirect()->route('login')->with('url.intended', $request->fullUrl());
        }

        if ($request->user()->email !== $invitation->email) {
            return Inertia::render('Invitations/AcceptError', [
                'message' => 'Esta invitación fue enviada a otro correo. Inicia sesión con ' . $invitation->email . ' para aceptarla.',
            ]);
        }

        $inviter = $invitation->inviter;
        $childrenCount = $inviter->children()->count();

        return Inertia::render('Invitations/Accept', [
            'token' => $token,
            'inviterName' => $inviter->name,
            'childrenCount' => $childrenCount,
        ]);
    }

    public function processAccept(Request $request, string $token): RedirectResponse
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if ($invitation->isAccepted()) {
            return redirect()->route('dashboard')->with('message', 'Esta invitación ya fue aceptada.');
        }

        if ($invitation->isExpired()) {
            return redirect()->route('dashboard')->with('error', 'Esta invitación ha expirado.');
        }

        $user = $request->user();
        if (! $user || $user->email !== $invitation->email) {
            return redirect()->route('login')->with('url.intended', route('invitations.accept', ['token' => $token]));
        }

        $invitation->acceptFor($user);

        return redirect()->route('dashboard')->with('message', 'Invitación aceptada. Ya puedes ver y gestionar los hijos.');
    }
}
