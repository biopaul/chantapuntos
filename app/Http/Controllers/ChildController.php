<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Child;
use App\Http\Requests\StoreChildRequest;
use App\Http\Requests\UpdateChildRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ChildController extends Controller
{
    public static function availableIcons(): array
    {
        return [
            ['id' => 'star', 'label' => 'Estrella'],
            ['id' => 'heart', 'label' => 'Corazón'],
            ['id' => 'sun', 'label' => 'Sol'],
            ['id' => 'moon', 'label' => 'Luna'],
            ['id' => 'cat', 'label' => 'Gato'],
            ['id' => 'dog', 'label' => 'Perro'],
            ['id' => 'fish', 'label' => 'Pez'],
            ['id' => 'bird', 'label' => 'Pájaro'],
            ['id' => 'balloon', 'label' => 'Globo'],
            ['id' => 'gift', 'label' => 'Regalo'],
            ['id' => 'cake', 'label' => 'Pastel'],
            ['id' => 'music', 'label' => 'Música'],
            ['id' => 'book', 'label' => 'Libro'],
            ['id' => 'pencil', 'label' => 'Lápiz'],
            ['id' => 'flower', 'label' => 'Flor'],
            ['id' => 'rainbow', 'label' => 'Arcoíris'],
        ];
    }

    public function index(Request $request): Response
    {
        $children = $request->user()->childrenAccessible();
        $childrenWithOwner = $children->map(fn ($c) => array_merge($c->toArray(), ['is_owner' => $c->user_id === $request->user()->id]));

        $user = $request->user();
        $canAddChild = $user->children()->exists() || $user->childrenAccessible()->isEmpty();

        return Inertia::render('Onboarding', [
            'children' => $childrenWithOwner->values(),
            'canAddChild' => $canAddChild,
            'availableIcons' => self::availableIcons(),
        ]);
    }

    public function store(StoreChildRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $avatarPath = null;

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        $request->user()->children()->create([
            'name' => $data['name'],
            'icon' => $data['icon'] ?? 'star',
            'avatar_path' => $avatarPath,
        ]);

        $childrenCount = $request->user()->children()->count();

        if ($childrenCount === 1) {
            return redirect()->back()->with('message', 'Hijo añadido. Añade otro o continúa.');
        }

        return redirect()->back()->with('message', 'Hijo añadido.');
    }

    public function update(UpdateChildRequest $request, Child $child): RedirectResponse
    {
        $data = $request->validated();
        $update = [
            'name' => $data['name'],
            'icon' => $data['icon'] ?? $child->icon,
        ];

        if ($request->hasFile('avatar')) {
            if ($child->avatar_path) {
                Storage::disk('public')->delete($child->avatar_path);
            }
            $update['avatar_path'] = $request->file('avatar')->store('avatars', 'public');
        }

        $child->update($update);

        return redirect()->back()->with('message', 'Hijo actualizado.');
    }

    public function shareUrl(Request $request, Child $child): JsonResponse
    {
        $this->authorize('update', $child);

        if (empty($child->share_token)) {
            $child->update(['share_token' => Str::random(48)]);
        }

        $url = route('child.card', ['token' => $child->share_token], true);
        $text = 'Mi ficha de puntos: ' . $url;
        $whatsappUrl = 'https://wa.me/?text=' . rawurlencode($text);

        return response()->json(['url' => $url, 'whatsapp_url' => $whatsappUrl]);
    }

    public function destroy(Request $request, Child $child): RedirectResponse
    {
        $this->authorize('delete', $child);

        if ($child->avatar_path) {
            Storage::disk('public')->delete($child->avatar_path);
        }
        $child->delete();

        $childrenCount = $request->user()->children()->count();
        if ($childrenCount === 0) {
            return redirect()->route('onboarding');
        }

        return redirect()->back()->with('message', 'Hijo eliminado.');
    }
}
