<x-guest-layout>

    <div style="text-align:center;margin-bottom:1.75rem">
        <div style="display:inline-flex;align-items:center;justify-content:center;width:52px;height:52px;border-radius:50%;background:rgba(74,126,255,.12);border:1px solid rgba(74,126,255,.25);margin-bottom:1rem">
            <svg width="24" height="24" fill="none" stroke="#4a7eff" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
        </div>
        <p class="auth-title" style="font-size:.95rem">
            Ingresa tu correo y te enviaremos un enlace para restablecer tu contraseña
        </p>
    </div>

    {{-- Estado: enlace enviado --}}
    @if (session('status'))
        <div class="auth-status">
            <svg width="15" height="15" fill="currentColor" viewBox="0 0 20 20" style="display:inline;margin-right:.4rem;vertical-align:-.2em">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
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
                    required autofocus>
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

        <button type="submit" class="btn-primary">Enviar enlace de recuperación</button>
    </form>

    <div class="auth-footer">
        <a href="{{ route('login') }}" style="display:inline-flex;align-items:center;gap:.35rem;color:var(--text-muted);text-decoration:none;font-weight:400;transition:color .2s"
            onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver al inicio de sesión
        </a>
    </div>

</x-guest-layout>
