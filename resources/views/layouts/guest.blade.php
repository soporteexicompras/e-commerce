<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Exicompras') }}</title>
    @php
        $__site    = \DB::table('mshop_locale_site')->where('code', 'default')->first();
        $__icon    = $__site->icon ?? null;
        $__favicon = $__icon ? asset('aimeos/' . ltrim($__icon, '/')) : asset('vendor/shop/themes/default/assets/icon.png');
    @endphp
    <link rel="icon" href="{{ $__favicon }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:         #0d1117;
            --bg-card:    #161b27;
            --bg-input:   #1c2333;
            --border:     #2d3550;
            --border-focus: #4a7eff;
            --primary:    #4a7eff;
            --primary-h:  #7aa3ff;
            --accent:     #ff6b35;
            --text:       #e8ecf4;
            --text-muted: #7a85a3;
            --error:      #f87171;
            --success:    #34d399;
            --radius:     14px;
        }

        html, body {
            height: 100%;
            font-family: 'Figtree', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            -webkit-font-smoothing: antialiased;
        }

        /* ── Fondo animado ── */
        .auth-bg {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .auth-bg::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 20% -10%, rgba(74,126,255,.18) 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 80% 110%, rgba(255,107,53,.12) 0%, transparent 55%),
                radial-gradient(ellipse 50% 40% at 50% 50%, rgba(74,126,255,.05) 0%, transparent 70%);
            pointer-events: none;
            z-index: 0;
        }

        /* Partículas flotantes */
        .auth-bg::after {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                radial-gradient(1.5px 1.5px at 15% 25%, rgba(74,126,255,.5) 0%, transparent 100%),
                radial-gradient(1px 1px at 35% 65%, rgba(255,107,53,.4) 0%, transparent 100%),
                radial-gradient(1.5px 1.5px at 60% 20%, rgba(122,163,255,.45) 0%, transparent 100%),
                radial-gradient(1px 1px at 80% 55%, rgba(74,126,255,.3) 0%, transparent 100%),
                radial-gradient(1px 1px at 90% 15%, rgba(255,107,53,.35) 0%, transparent 100%),
                radial-gradient(1.5px 1.5px at 5%  80%, rgba(122,163,255,.4) 0%, transparent 100%),
                radial-gradient(1px 1px at 45% 90%, rgba(74,126,255,.3) 0%, transparent 100%),
                radial-gradient(1.5px 1.5px at 70% 78%, rgba(255,107,53,.25) 0%, transparent 100%);
            pointer-events: none;
            z-index: 0;
            animation: twinkle 8s ease-in-out infinite alternate;
        }

        @keyframes twinkle {
            0%   { opacity: .6; }
            100% { opacity: 1; }
        }

        /* ── Tarjeta ── */
        .auth-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 440px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 2.5rem 2.25rem;
            box-shadow:
                0 0 0 1px rgba(74,126,255,.08),
                0 20px 60px rgba(0,0,0,.5),
                0 4px 16px rgba(0,0,0,.3);
            animation: cardIn .45s cubic-bezier(.22,1,.36,1) both;
        }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(24px) scale(.97); }
            to   { opacity: 1; transform: translateY(0)    scale(1); }
        }

        /* ── Header tarjeta ── */
        .auth-brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 2rem;
        }

        .auth-title {
            font-size: 1rem;
            color: var(--text-muted);
            font-weight: 400;
        }

        /* ── Grupos de campos ── */
        .field {
            margin-bottom: 1.1rem;
        }

        .field label {
            display: block;
            font-size: .8rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: .45rem;
        }

        .field-wrap {
            position: relative;
        }

        .field-wrap > svg,
        .field-wrap input ~ svg {
            position: absolute;
            left: .9rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            pointer-events: none;
            transition: color .2s;
        }

        .field-wrap input {
            width: 100%;
            padding: .75rem .9rem .75rem 2.6rem;
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--text);
            font-size: .95rem;
            font-family: inherit;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        .field-wrap input::placeholder { color: var(--text-muted); opacity: .6; }

        .field-wrap input:focus {
            border-color: var(--border-focus);
            box-shadow: 0 0 0 3px rgba(74,126,255,.18);
        }

        .field-wrap input:focus ~ svg,
        .field-wrap:focus-within > svg { color: var(--primary); }

        /* toggle password */
        .field-wrap .pw-toggle {
            position: absolute;
            right: .9rem;
            top: 50%;
            transform: translateY(-50%);
            left: auto;
            cursor: pointer;
            background: none;
            border: none;
            padding: 0;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            pointer-events: all;
        }
        .field-wrap .pw-toggle svg { pointer-events: none; }
        .field-wrap .pw-toggle:hover { color: var(--primary); }

        .field-error {
            font-size: .78rem;
            color: var(--error);
            margin-top: .35rem;
            display: flex;
            align-items: center;
            gap: .3rem;
        }

        /* ── Checkbox ── */
        .check-row {
            display: flex;
            align-items: center;
            gap: .6rem;
            margin: .25rem 0 1.1rem;
        }

        .check-row input[type=checkbox] {
            appearance: none;
            -webkit-appearance: none;
            width: 18px;
            height: 18px;
            border: 1.5px solid var(--border);
            border-radius: 5px;
            background: var(--bg-input);
            cursor: pointer;
            flex-shrink: 0;
            transition: background .15s, border-color .15s;
            position: relative;
        }

        .check-row input[type=checkbox]:checked {
            background: var(--primary);
            border-color: var(--primary);
        }

        .check-row input[type=checkbox]:checked::after {
            content: '';
            position: absolute;
            top: 2px; left: 5px;
            width: 5px; height: 9px;
            border: 2px solid #fff;
            border-top: none;
            border-left: none;
            transform: rotate(45deg);
        }

        .check-row label {
            font-size: .875rem;
            color: var(--text-muted);
            cursor: pointer;
        }

        /* ── Botón principal ── */
        .btn-primary {
            width: 100%;
            padding: .8rem;
            background: linear-gradient(135deg, var(--primary) 0%, #6a96ff 100%);
            color: #fff;
            font-size: .95rem;
            font-weight: 600;
            font-family: inherit;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: opacity .2s, transform .15s, box-shadow .2s;
            box-shadow: 0 4px 16px rgba(74,126,255,.35);
            letter-spacing: .01em;
            margin-top: .25rem;
        }

        .btn-primary:hover {
            opacity: .9;
            transform: translateY(-1px);
            box-shadow: 0 6px 22px rgba(74,126,255,.5);
        }

        .btn-primary:active {
            transform: translateY(0);
            opacity: 1;
        }

        /* ── Links ── */
        .auth-link {
            color: var(--primary);
            font-size: .875rem;
            text-decoration: none;
            transition: color .2s;
        }
        .auth-link:hover { color: var(--primary-h); text-decoration: underline; }

        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: .875rem;
            color: var(--text-muted);
        }

        .auth-footer a { color: var(--primary); text-decoration: none; font-weight: 600; }
        .auth-footer a:hover { color: var(--primary-h); }

        /* ── Separador ── */
        .auth-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: .5rem 0 1.25rem;
        }

        /* ── Alert / Status ── */
        .auth-status {
            background: rgba(52,211,153,.1);
            border: 1px solid rgba(52,211,153,.3);
            color: var(--success);
            border-radius: 8px;
            padding: .7rem .9rem;
            font-size: .875rem;
            margin-bottom: 1.25rem;
        }

        .auth-status.error {
            background: rgba(248,113,113,.08);
            border-color: rgba(248,113,113,.3);
            color: var(--error);
        }

        /* ── Descripción ── */
        .auth-desc {
            font-size: .875rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        /* ── Responsive ── */
        @media (max-width: 480px) {
            .auth-card { padding: 2rem 1.5rem; }
        }
    </style>
</head>
<body>
    <div class="auth-bg">
        <div class="auth-card">
            <div class="auth-brand">
                @php
                    try {
                        $ctx     = app('aimeos.context')->get(false);
                        $baseUrl = $ctx->config()->get('resource/fs-media/baseurl');
                        $logo    = $ctx->locale()->getSiteItem()->getLogo();
                        $logoUrl = $logo ? asset($baseUrl . '/' . $logo) : asset('vendor/shop/themes/default/assets/logo.png');
                    } catch (\Exception $e) {
                        // Fallback directo a BD — el contexto Aimeos no está disponible en rutas de auth
                        $site     = \DB::table('mshop_locale_site')->where('code', 'default')->first();
                        $logoJson = $site ? json_decode($site->logo ?? 'null', true) : null;
                        // logo es {"id":"path"} — tomamos el último valor
                        $logoPath = is_array($logoJson) ? end($logoJson) : null;
                        $logoUrl  = $logoPath
                            ? asset('aimeos/' . ltrim($logoPath, '/'))
                            : asset('vendor/shop/themes/default/assets/logo.png');
                    }
                @endphp
                <a href="/" style="display:block;text-align:center">
                    <img src="{{ $logoUrl }}" alt="Exicompras" style="height:64px;width:auto;max-width:200px;object-fit:contain">
                </a>
            </div>
            {{ $slot }}
        </div>
    </div>
    <script>
        // Toggle mostrar/ocultar contraseña
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.pw-toggle').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var input = btn.closest('.field-wrap').querySelector('input');
                    var icon  = btn.querySelector('svg');
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/>';
                    } else {
                        input.type = 'password';
                        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
                    }
                });
            });
        });
    </script>
</body>
</html>
