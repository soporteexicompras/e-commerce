<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class FavoriteController extends Controller
{
    /**
     * GET /favorites — vista de lista de favoritos.
     */
    public function index()
    {
        $favorites = Favorite::current()->latest()->paginate(20);
        return view('shop::account.favorites', compact('favorites'));
    }

    /**
     * POST /favorites — añade un producto a favoritos.
     * Devuelve JSON {count: N} para AJAX o redirect para navegador.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id'   => ['required', 'string', 'max:64'],
            'product_code' => ['nullable', 'string', 'max:128'],
            'name'         => ['nullable', 'string', 'max:255'],
            'price'        => ['nullable', 'numeric', 'min:0'],
            'media_url'    => ['nullable', 'string', 'max:500'],
        ]);

        $userId = Auth::id();
        $sid    = Favorite::getSessionId();

        // No duplicar (user_id, product_id) o (session_id, product_id)
        $exists = Favorite::query()
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->when(!$userId && $sid, fn($q) => $q->where('session_id', $sid)->whereNull('user_id'))
            ->where('product_id', $data['product_id'])
            ->exists();

        if (!$exists) {
            Favorite::create([
                'user_id'      => $userId,
                'session_id'   => $sid,
                'product_id'   => $data['product_id'],
                'product_code' => $data['product_code'] ?? null,
                'name'         => $data['name'] ?? null,
                'price'        => $data['price'] ?? null,
                'media_url'    => $data['media_url'] ?? null,
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'count' => Favorite::currentCount(),
                'ok'    => true,
            ]);
        }
        return back()->with('success', __('Producto añadido a favoritos'));
    }

    /**
     * DELETE /favorites/{product} — elimina un producto de favoritos.
     */
    public function destroy(Request $request, string $product)
    {
        $userId = Auth::id();
        $sid    = Favorite::getSessionId();

        $query = Favorite::query()->where('product_id', $product);
        if ($userId) {
            $query->where('user_id', $userId);
        } elseif ($sid) {
            $query->where('session_id', $sid)->whereNull('user_id');
        }
        $query->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'count' => Favorite::currentCount(),
                'ok'    => true,
            ]);
        }
        return back()->with('success', __('Producto eliminado de favoritos'));
    }

    /**
     * POST /favorites/sync — consolida favoritos guest → user al hacer login.
     */
    public function sync(Request $request): JsonResponse|RedirectResponse
    {
        $userId = Auth::id();
        if (!$userId) {
            if ($request->wantsJson()) {
                return response()->json(['ok' => false, 'error' => 'auth_required'], 401);
            }
            return redirect()->route('login');
        }
        $moved = Favorite::syncOnLogin($userId);
        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'moved' => $moved, 'count' => Favorite::currentCount()]);
        }
        return back()->with('success', __('Favoritos sincronizados'));
    }
}
