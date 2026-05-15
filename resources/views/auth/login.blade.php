<x-guest-layout>

    {{-- Título de la tarjeta --}}
    <p class="auth-title" style="text-align:center;margin-bottom:1.75rem;font-size:.95rem">
        Bienvenido de nuevo — inicia sesión para continuar
    </p>

    {{-- Estado de sesión (ej. "Enlace enviado") --}}
    @if (session('status'))
        <div class="auth-status">{{ session('status') }}</div>
    @endif

    {{-- Errores globales --}}
    @if ($errors->any() && !$errors->has('email') && !$errors->has('password'))
        <div class="auth-status error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Email --}}
        <div class="field">
            <label for="email">Correo electrónico</label>
            <div class="field-wrap">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <input id="email" type="email" name="email"
                    value="{{ old('email') }}"
                    placeholder="tu@correo.com"
                    required autofocus autocomplete="username">
            </div>
            @error('email')
                <div class="field-error">
                    <svg width="13" height="13" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Contraseña --}}
        <div class="field">
            <label for="password">Contraseña</label>
            <div class="field-wrap">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <input id="password" type="password" name="password"
                    placeholder="••••••••"
                    required autocomplete="current-password">
                <button type="button" class="pw-toggle" tabindex="-1" aria-label="Mostrar contraseña">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>
            @error('password')
                <div class="field-error">
                    <svg width="13" height="13" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Recuérdame + olvidé contraseña --}}
        <div class="auth-row">
            <label class="check-row" style="margin:0">
                <input type="checkbox" name="remember" id="remember_me">
                <span>Recuérdame</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="auth-link">¿Olvidaste tu contraseña?</a>
            @endif
        </div>

        <button type="submit" class="btn-primary">Iniciar sesión</button>
    </form>

    @if (Route::has('register'))
        <div class="auth-footer">
            ¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate gratis</a>
        </div>
    @endif

</x-guest-layout>
