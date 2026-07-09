<!DOCTYPE html>
<html class="no-js" lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), ['ar', 'az', 'dv', 'fa', 'he', 'ku', 'ur']) ? 'rtl' : 'ltr' }}">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<meta name="theme-color" content="#E3E7EB">

		@if( config('app.debug') !== true )
			<meta http-equiv="Content-Security-Policy" content="base-uri 'self'; default-src 'self' 'nonce-{{ app( 'aimeos.context' )->get()->nonce() }}'; {{ config( 'shop.csp.frontend', 'style-src \'unsafe-inline\' \'self\'; img-src \'self\' data: https://aimeos.org; frame-src https://www.youtube.com https://player.vimeo.com' ) }}">
		@endif

		@php
			$_siteVer = app( 'aimeos.context' )->get()->locale()->getSiteItem()->getConfigValue( 'theme_version' );
			$_ver     = $_siteVer ?? config( 'shop.version', 1 );
		@endphp
		@if( in_array(app()->getLocale(), ['ar', 'az', 'dv', 'fa', 'he', 'ku', 'ur']) )
			<link type="text/css" rel="stylesheet" href="{{ asset('vendor/shop/themes/default/app.rtl.css?v=' . $_ver ) }}">
		@else
			<link type="text/css" rel="stylesheet" href="{{ asset('vendor/shop/themes/default/app.css?v=' . $_ver ) }}">
		@endif
		<link type="text/css" rel="stylesheet" href="{{ asset('vendor/shop/themes/default/aimeos.css?v=' . $_ver ) }}">
		{{-- Navbar global — garantiza consistencia entre home, list, tree, detail --}}
		<link type="text/css" rel="stylesheet" href="{{ asset('css/exinavbar.css?v=' . $_ver) }}">
		{{-- 2-row layout + sticky shrink + mega-menú + search prominente + wishlist --}}
		<link type="text/css" rel="stylesheet" href="{{ asset('css/exinavbar.dark.css?v=' . $_ver) }}">
		{{-- Drawer móvil (off-canvas) --}}
		<link type="text/css" rel="stylesheet" href="{{ asset('css/exinavbar.drawer.css?v=' . $_ver) }}">
		{{-- Footer rediseñado + páginas legales (Terminos, Privacidad, etc.) --}}
		<link type="text/css" rel="stylesheet" href="{{ asset('css/exifooter.css?v=1') }}">
		{{-- Page loader premium para categoria Influencers --}}
		<link type="text/css" rel="stylesheet" href="{{ asset('css/exi-page-loader.css?v=1') }}">

		@yield('aimeos_header')

		@php
			$_site       = app( 'aimeos.context' )->get()->locale()->getSiteItem();
			$_presets    = config( 'shop.client.html.theme-presets.default', [] );
			$_overrides  = $_site->getConfigValue( 'theme/default', [] );
			$themeVars   = array_merge( $_presets ?? [], $_overrides ?? [] );
			$logoBase   = max( 32, min( 200, (int) ($themeVars['--ai-nav-logo-height'] ?? 64) ) );
			$logoMobile = max( 32, (int) round( $logoBase * 0.65 ) );
			$logoTablet = max( 32, (int) round( $logoBase * 0.80 ) );
			$logoXl     = max( 32, (int) round( $logoBase * 1.15 ) );
		@endphp
		<style nonce="{{ app( 'aimeos.context' )->get()->nonce() }}">
			:root {
				@foreach( $themeVars as $key => $value )
					{{ $key }}: {{ $value }};
				@endforeach
			}
		/* ── Responsive global ── */
		*, *::before, *::after { box-sizing: border-box; }
		html { scroll-behavior: smooth; }
		/* overflow-x: clip (no hidden) para no romper position:sticky del navbar */
		html, body { overflow-x: clip; max-width: 100vw; }
		img { max-width: 100%; height: auto; }

		/* ── body margin-top: navbar es sticky, no fixed → no necesita compensación ── */
		body > .content, body .main-section { margin-top: 0 !important; }

		/* ── Logo footer — tamaño fijo independiente del navbar ── */
		.footer-logo { height: 48px; width: auto; max-width: 160px; display: block; margin-bottom: .75rem; }
		@media (min-width: 576px)  { .footer-logo { height: 56px; max-width: 180px; } }
		@media (min-width: 992px)  { .footer-logo { height: 60px; max-width: 200px; } }

		/* Footer responsive */
		footer .container-fluid { padding-left: clamp(.875rem,4vw,1.5rem); padding-right: clamp(.875rem,4vw,1.5rem); }
		footer .row { row-gap: 1.5rem; }
		@media (max-width: 767px) {
			footer .col-md-8, footer .col-md-4 { width: 100%; }
			footer .col-sm-6 { width: 50%; }
		}
		@media (max-width: 400px) {
			footer .col-sm-6 { width: 100%; }
		}
		/* Safe area insets para notch/island en iOS */
		.navbar { padding-left: max(.75rem, env(safe-area-inset-left)); padding-right: max(.75rem, env(safe-area-inset-right)); }
		footer   { padding-bottom: max(1rem, env(safe-area-inset-bottom)); }

		@if( isset($themeVars['--ai-nav-bg']) || isset($themeVars['--ai-nav-text']) || isset($themeVars['--ai-nav-text-hover']) || isset($themeVars['--ai-nav-icon']) )
		@php $navIcon = $themeVars['--ai-nav-icon'] ?? $themeVars['--ai-nav-text'] ?? 'var(--ai-primary)'; @endphp
		body { background-image: none; background-color: {{ $themeVars['--ai-nav-bg'] ?? 'var(--ai-bg-alt)' }}; }
		.navbar { background-color: {{ $themeVars['--ai-nav-bg'] ?? 'transparent' }} !important; }
		.navbar.scroll { background-color: {{ $themeVars['--ai-nav-bg'] ?? 'var(--ai-primary)' }} !important; }
			.navbar-light .navbar-nav .nav-link,
			.navbar-default .navbar-nav .nav-link,
			.navbar-default-transition .navbar-nav .nav-link { color: {{ $themeVars['--ai-nav-text'] ?? 'var(--ai-bg)' }} !important; }
			.navbar-light .navbar-nav .nav-link:hover,
			.navbar-default .navbar-nav .nav-link:hover,
			.navbar-default-transition .navbar-nav .nav-link:hover { color: {{ $themeVars['--ai-nav-text-hover'] ?? 'var(--ai-tertiary)' }} !important; }
			/* Iconos navbar: login, register, profile, carrito */
			.navbar-nav .login > .nav-link::before,
			.navbar-nav .register > .nav-link::before,
			.navbar-nav .profile > .nav-link::before { color: {{ $navIcon }} !important; }
			.basket-mini .menu:after { color: {{ $navIcon }} !important; }
			/* Lupa de búsqueda */
			.navbar .catalog-filter-search .input-group .btn-search { color: {{ $navIcon }} !important; }
			/* Selector idioma / moneda — texto visible */
			.locale-select ul.select-menu a,
			.locale-select ul.select-menu a:link,
			.locale-select ul.select-menu a:visited { color: {{ $navIcon }} !important; }
			/* Dropdown Cuenta — fondo y texto */
			@php $dropBg = $themeVars['--ai-nav-dropdown-bg'] ?? $themeVars['--ai-nav-bg'] ?? 'var(--ai-primary)';
			     $dropText = $themeVars['--ai-nav-dropdown-text'] ?? $themeVars['--ai-nav-icon'] ?? 'var(--ai-bg)'; @endphp
			.navbar-nav .dropdown-menu { background-color: {{ $dropBg }} !important; border-color: {{ $dropText }} !important; }
			.navbar-nav .dropdown-menu,
			.navbar-nav .dropdown-menu .nav-link,
			.navbar-nav .dropdown-item,
			.navbar-nav .dropdown-menu button { color: {{ $dropText }} !important; }
			.navbar-nav .dropdown-item:hover,
			.navbar-nav .dropdown-item:focus { background-color: {{ $themeVars['--ai-secondary'] ?? 'var(--ai-secondary)' }} !important; color: {{ $dropText }} !important; }
			/* Dropdown idioma / moneda — fondo y texto */
			.locale-select ul.select-menu ul { background-color: {{ $dropBg }} !important; border-color: {{ $dropText }} !important; }
			.locale-select ul.select-menu ul a,
			.locale-select ul.select-menu ul a:link,
			.locale-select ul.select-menu ul a:visited { color: {{ $dropText }} !important; }
			.locale-select li.select-dropdown ul.select-dropdown li:hover a { color: {{ $dropText }} !important; }
			@endif
			/* ── Categorías en el menú (catalog-filter-tree) ── */
			@php $catText = $themeVars['--ai-nav-text'] ?? '#1A1F36';
			     $catHover = $themeVars['--ai-nav-text-hover'] ?? $themeVars['--ai-tertiary'] ?? '#4A7EFF';
			     $catNavBg = $themeVars['--ai-nav-bg'] ?? '#FFFFFF'; @endphp
			.catalog-filter-tree a.cat-link,
			.catalog-filter-tree a.cat-link:link,
			.catalog-filter-tree a.cat-link:visited { color: {{ $catText }} !important; }
			.catalog-filter-tree a.cat-link:hover,
			.catalog-filter-tree a.cat-link:link:hover,
			.catalog-filter-tree a.cat-link:visited:hover { color: {{ $catHover }} !important; }
			.catalog-filter-tree .list-container,
			.catalog-filter-tree .zeynep { background-color: {{ $catNavBg }} !important; }
			.catalog-filter-tree .row.header,
			.catalog-filter-tree .row.header .name { color: {{ $catText }} !important; }
		</style>

		@if( ($page ?? '') === 'catalog-home' || request()->is('/') )
		<link rel="stylesheet" href="{{ asset('css/exihome.css?v=8') }}">
		@endif

		<link rel="icon" href="{{ asset( app( 'aimeos.context' )->get()->config()->get( 'resource/fs-media/baseurl' ) . '/' . ( app( 'aimeos.context' )->get()->locale()->getSiteItem()->getIcon() ?: '../vendor/shop/themes/default/assets/icon.png' ) ) }}">

		<link rel="preload" href="{{ asset('vendor/shop/themes/default/assets/roboto-condensed-v19-latin-regular.woff2') }}" as="font" type="font/woff2" crossorigin>
		<link rel="preload" href="{{ asset('vendor/shop/themes/default/assets/roboto-condensed-v19-latin-700.woff2') }}" as="font" type="font/woff2" crossorigin>
		<link rel="preload" href="{{ asset('vendor/shop/themes/default/assets/bootstrap-icons.woff2') }}" as="font" type="font/woff2" crossorigin>
		{{-- Iconos bootstrap (DEBE cargarse para que bi-* funcione) --}}
		<link type="text/css" rel="stylesheet" href="{{ asset('css/exinavbar.icons.css?v=1') }}">
		{{-- Search override: DEBE cargarse AL FINAL para ganar a catalog-filter.css de Aimeos --}}
		<link type="text/css" rel="stylesheet" href="{{ asset('css/exinavbar.search.css?v=3') }}">
	</head>
	<body class="{{ $page ?? '' }}">
		<div class="exi-navbar-sentinel" aria-hidden="true"></div>
		<nav class="navbar navbar-expand-md navbar-top exi-navbar-shell"
			style="--ai-logo-h: {{ $logoBase }}px; --ai-logo-h-mobile: {{ $logoMobile }}px; --ai-logo-h-tablet: {{ $logoTablet }}px; --ai-logo-h-xl: {{ $logoXl }}px; --ai-logo-max-w: {{ $logoBase * 3 }}px;"
			data-exi-shrinkable aria-label="{{ __('Navegación principal') }}">
			{{-- ═════════ Fila 1: principal (logo, search, acciones) ═════════ --}}
			<div class="exi-navbar-row exi-navbar-row--main">
				<button class="exi-navbar-toggler" type="button" data-exi-drawer-open aria-controls="exiDrawer" aria-expanded="false" aria-label="{{ __('Abrir menú') }}">
					<i class="bi bi-list" aria-hidden="true"></i>
				</button>

				<a class="navbar-brand" href="/" title="{{ __('To the home page') }}">
					<img src="{{ asset( app( 'aimeos.context' )->get()->config()->get( 'resource/fs-media/baseurl' ) . '/' . ( app( 'aimeos.context' )->get()->locale()->getSiteItem()->getLogo() ?: '../vendor/shop/themes/default/assets/logo.png' ) ) }}" class="navbar-logo" alt="{{ __('To the home page') }}">
				</a>

				<div class="exi-search-wrap" role="search">
					@yield('aimeos_head_search')
				</div>

				<div class="exi-navbar-actions d-flex align-items-center">
					<div class="exi-locale-wrap">
						@yield('aimeos_head_locale')
					</div>

					@include('shop::partials.exi-wishlist-icon')

					<ul class="navbar-nav exi-account">
						@if (Auth::guest() && config('app.shop_registration'))
							<li class="nav-item register"><a class="nav-link" href="{{ airoute( 'register' ) }}" title="{{ __( 'Register' ) }}"><span class="name">{{ __('Register') }}</span></a></li>
						@endif
						@if (Auth::guest())
							<li class="nav-item login"><a class="nav-link" href="{{ airoute( 'login' ) }}" title="{{ __( 'Login' ) }}"><span class="name">{{ __( 'Login' ) }}</span></a></li>
						@else
							<li class="nav-item login profile dropdown">
								<a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-expanded="false" title="{{ __( 'Account' ) }}"><span class="name">{{ __( 'Account' ) }}</span> <span class="caret"></span></a>
								<ul class="dropdown-menu dropdown-menu-end" role="menu">
									<li class="dropdown-item"><a class="nav-link" href="{{ airoute( 'aimeos_shop_account' ) }}"><span class="name">{{ __( 'Profile' ) }}</span></a></li>
									<li class="dropdown-item"><form id="logout" action="{{ airoute( 'logout' ) }}" method="POST">{{ csrf_field() }}<button class="nav-link"><span class="name">{{ __( 'Logout' ) }}</span></button></form></li>
								</ul>
							</li>
						@endif
					</ul>

					@yield('aimeos_head_basket')
				</div>
			</div>

			{{-- ═════════ Fila 2: categorías (solo desktop) ═════════ --}}
			<div class="exi-navbar-row exi-navbar-row--sub" id="navbar-top">
				<div class="collapse navbar-collapse exi-navbar-collapse">
					@yield('aimeos_head_nav')
				</div>
			</div>
		</nav>

		@include('shop::partials.exi-drawer')

		<div class="content">
			@yield('aimeos_stage')
			<main>
				@yield('aimeos_body')
				@yield('content')
			</main>
		</div>


		<footer class="exicom-footer" aria-label="Pie de página">
			<div class="exicom-footer__container">

				<div class="exicom-footer__grid">

					{{-- Columna 1: marca + contacto + redes --}}
					<div class="exicom-footer__brand">
						@php
							$_siteLogo = trim((string) app( 'aimeos.context' )->get()->locale()->getSiteItem()->getLogo());
							$_logoUrl  = $_siteLogo !== ''
							    ? app( 'aimeos.context' )->get()->config()->get( 'resource/fs-media/baseurl' ) . '/' . $_siteLogo
							    : 'vendor/shop/themes/default/assets/logo.png';
						@endphp
						<a href="{{ route('aimeos_home') }}" title="Ir al inicio" class="exicom-footer__logo-link" aria-label="Exicompras — ir al inicio">
							<img src="{{ asset($_logoUrl) }}" alt="Exicompras" loading="lazy" class="exicom-footer__logo">
						</a>
						<p class="exicom-footer__tagline">El marketplace colombiano para comprar productos únicos con pagos seguros y envíos trazables.</p>

						<ul class="exicom-footer__contact">
							<li>📍 <span>Carrera 1 # 2-3, Bogotá D.C., Colombia</span></li>
							<li>✉️ <a href="mailto:atencion@exicompras.com">atencion@exicompras.com</a></li>
							<li>📞 <a href="tel:+5710000000">+57 (1) 000 0000</a></li>
							<li><small>NIT 900.000.000-0 · Exicompras S.A.S.</small></li>
						</ul>

						<div class="exicom-footer__socials" aria-label="Redes sociales">
							<a href="#" class="exicom-footer__social" title="Facebook"  rel="noopener" aria-label="Facebook">
								<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M13 22v-8h2.8l.4-3H13V8.9c0-1 .2-1.6 1.6-1.6H16V4.6c-.3 0-1.4-.1-2.6-.1-2.6 0-4.4 1.6-4.4 4.5v2.5H6.5V14H9v8h4z"/></svg>
							</a>
							<a href="#" class="exicom-footer__social" title="Instagram" rel="noopener" aria-label="Instagram">
								<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/></svg>
							</a>
							<a href="#" class="exicom-footer__social" title="TikTok" rel="noopener" aria-label="TikTok">
								<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M16.5 3c.3 1.7 1.4 3.2 3 4v3a8.4 8.4 0 0 1-4-1.1v6.6a5.6 5.6 0 1 1-5.6-5.6c.3 0 .7 0 1 .1v3.1a2.6 2.6 0 1 0 1.6 2.4V3h4z"/></svg>
							</a>
							<a href="#" class="exicom-footer__social" title="YouTube" rel="noopener" aria-label="YouTube">
								<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M22 8s-.2-1.4-.8-2c-.8-.8-1.6-.8-2-.9-2.8-.2-7.2-.2-7.2-.2s-4.4 0-7.2.2c-.4 0-1.2 0-2 .9C2.2 6.6 2 8 2 8S1.8 9.6 1.8 11.2v1.6c0 1.6.2 3.2.2 3.2s.2 1.4.8 2c.8.8 1.8.8 2.3.9 1.6.2 7.1.2 7.1.2s4.4 0 7.2-.2c.4 0 1.2-.1 2-.9.6-.6.8-2 .8-2s.2-1.6.2-3.2v-1.6c0-1.6-.2-3.2-.2-3.2zM10 14V9l5 2.5L10 14z"/></svg>
							</a>
							<a href="#" class="exicom-footer__social" title="X (Twitter)" rel="noopener" aria-label="X (Twitter)">
								<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.2 3H21l-6.5 7.4L22 21h-6l-4.7-6.1L5.8 21H3l7-8L2.5 3h6.2l4.3 5.7L18.2 3z"/></svg>
							</a>
						</div>
					</div>

					{{-- Columna 2: Comprar --}}
					<div class="exicom-footer__col">
						<h4>Comprar</h4>
						<ul class="exicom-footer__list">
							<li><a href="{{ route('aimeos_shop_list') }}">Catálogo</a></li>
							<li><a href="{{ route('legal.terminos') }}">Ofertas y promociones</a></li>
							<li><a href="{{ route('legal.terminos') }}">Nuevos productos</a></li>
							<li><a href="{{ route('favorites.index') }}">Mis favoritos</a></li>
						</ul>
					</div>

					{{-- Columna 3: Atención al cliente --}}
					<div class="exicom-footer__col">
						<h4>Atención</h4>
						<ul class="exicom-footer__list">
							<li><a href="{{ route('legal.contacto') }}">Centro de ayuda</a></li>
							<li><a href="{{ route('legal.reclamaciones') }}">Libro de reclamaciones</a></li>
							<li><a href="{{ route('legal.reclamaciones') }}">Peticiones, quejas y reclamos</a></li>
							<li><a href="{{ route('legal.envios') }}">Seguimiento de envío</a></li>
						</ul>
					</div>

					{{-- Columna 4: Legal --}}
					<div class="exicom-footer__col">
						<h4>Legal</h4>
						<ul class="exicom-footer__list">
							<li><a href="{{ route('legal.terminos') }}">Términos y Condiciones</a></li>
							<li><a href="{{ route('legal.privacidad') }}">Política de Privacidad</a></li>
							<li><a href="{{ route('legal.cancelaciones') }}">Cancelaciones y retracto</a></li>
							<li><a href="{{ route('legal.envios') }}">Política de Envíos</a></li>
							<li><a href="{{ route('legal.garantias') }}">Garantía Legal</a></li>
							<li><a href="{{ route('legal.reclamaciones') }}">Libro de Reclamaciones</a></li>
						</ul>
					</div>

					{{-- Columna 5: Empresa --}}
					<div class="exicom-footer__col">
						<h4>Empresa</h4>
						<ul class="exicom-footer__list">
							<li><a href="{{ route('legal.sobre-nosotros') }}">Sobre Exicompras</a></li>
							<li><a href="{{ route('legal.contacto') }}">Contacto</a></li>
							<li><a href="mailto:vendedores@exicompras.com">Vende en Exicompras</a></li>
						</ul>

						<div class="exicom-footer__pay" aria-label="Medios de pago aceptados">
							<span class="exicom-footer__pay-chip">Visa</span>
							<span class="exicom-footer__pay-chip">Mastercard</span>
							<span class="exicom-footer__pay-chip">Amex</span>
							<span class="exicom-footer__pay-chip">PSE</span>
							<span class="exicom-footer__pay-chip">Nequi</span>
							<span class="exicom-footer__pay-chip">Daviplata</span>
						</div>
					</div>

				</div>

				<div class="exicom-footer__bottom">
					<div>
						<strong>© {{ date('Y') }} Exicompras S.A.S.</strong> · Todos los derechos reservados.
						<span class="exicom-footer__legal-line">
							Cumple con la <strong>Ley 1480 de 2011</strong> (Estatuto del Consumidor) ·
							<strong>Ley 1581 de 2012</strong> (Protección de Datos Personales) ·
							<strong>Decreto 735 de 2013</strong> (Libro de Reclamaciones Virtual).
						</span>
					</div>
					<div class="exicom-footer__bottom-links">
						<a href="{{ route('legal.terminos') }}">Términos</a>
						<a href="{{ route('legal.privacidad') }}">Privacidad</a>
						<a href="{{ route('legal.garantias') }}">Garantía</a>
						<a href="{{ route('legal.contacto') }}">Contacto</a>
					</div>
				</div>

			</div>
		</footer>



		<a id="toTop" class="back-to-top" href="#" title="{{ __( 'Back to top' ) }}">
			<div class="top-icon"></div>
		</a>

		<!-- Scripts -->
		<script src="{{ asset('vendor/shop/themes/default/app.js?v=' . $_ver ) }}"></script>
		<script src="{{ asset('vendor/shop/themes/default/aimeos.js?v=' . $_ver ) }}"></script>
		{{-- Navbar: drawer, sticky shrink, wishlist AJAX, clonado categorías --}}
		<script src="{{ asset('js/exinavbar.js?v=2') }}" defer></script>
		{{-- Page loader premium para categoria Influencers --}}
		<script src="{{ asset('js/exi-page-loader.js?v=1') }}" defer></script>
		@yield('aimeos_scripts')
	</body>
</html>
