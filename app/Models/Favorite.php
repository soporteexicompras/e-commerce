<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class Favorite extends Model
{
    protected $fillable = [
        'user_id', 'session_id', 'product_id',
        'product_code', 'name', 'price', 'media_url',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /* ── Relaciones ───────────────────────────────────── */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /* ── Scopes ───────────────────────────────────────── */
    public function scopeForUser(Builder $q, int $userId): Builder
    {
        return $q->where('user_id', $userId);
    }

    public function scopeForSession(Builder $q, string $sessionId): Builder
    {
        return $q->where('session_id', $sessionId)->whereNull('user_id');
    }

    public function scopeCurrent(Builder $q): Builder
    {
        if ($userId = Auth::id()) {
            return $q->where('user_id', $userId);
        }
        if ($sid = self::getSessionId()) {
            return $q->where('session_id', $sid)->whereNull('user_id');
        }
        return $q->whereRaw('0 = 1'); // vacío
    }

    /* ── Helpers de identidad ─────────────────────────── */

    /**
     * Devuelve el session_id actual (crea uno si no existe).
     * Para usuarios autenticados, devuelve null.
     */
    public static function getSessionId(): ?string
    {
        if (Auth::id()) {
            return null;
        }
        $sid = Session::get('exi_fav_id');
        if (!$sid) {
            $sid = Str::random(32);
            Session::put('exi_fav_id', $sid);
            Cookie::queue(Cookie::make('exi_fav_id', $sid, 60 * 24 * 30, null, null, false, true, false, 'lax'));
        }
        return $sid;
    }

    /**
     * Devuelve el conteo actual de favoritos para el usuario/sesión.
     */
    public static function currentCount(): int
    {
        return self::current()->count();
    }

    /**
     * Sincroniza favoritos de session_id a user_id al hacer login.
     */
    public static function syncOnLogin(int $userId): int
    {
        $sid = self::getSessionId() ?? Session::pull('exi_fav_id');
        if (!$sid) {
            return 0;
        }
        $moved = self::where('session_id', $sid)
            ->whereNull('user_id')
            ->update(['user_id' => $userId, 'session_id' => null]);
        Session::forget('exi_fav_id');
        Cookie::queue(Cookie::forget('exi_fav_id'));
        return $moved;
    }
}
