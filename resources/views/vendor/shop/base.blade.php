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
		<link type="text/css" rel="stylesheet" href="{{ asset('css/exinavbar.css?v=2') }}">
		{{-- 2-row layout + sticky shrink + mega-menú + search prominente + wishlist --}}
		<link type="text/css" rel="stylesheet" href="{{ asset('css/exinavbar.dark.css?v=23') }}">
		{{-- Drawer móvil (off-canvas) --}}
		<link type="text/css" rel="stylesheet" href="{{ asset('css/exinavbar.drawer.css?v=6') }}">

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
		<link rel="stylesheet" href="{{ asset('css/exihome.css?v=5') }}">
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


		<footer>
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-8">
						<div class="row">
							<div class="col-sm-6 footer-left">
								<div class="footer-block">
									<h2 class="pb-3" aria-label="{{ __('Legal information') }}">{{ __( 'LEGAL' ) }}</h2>
									<p><a href="{{ airoute(config('shop.client.html.cms.page.url.target', 'aimeos_page'), ['path' => 'terms']) }}">{{ __( 'Terms & Conditions' ) }}</a></p>
									<p><a href="{{ airoute(config('shop.client.html.cms.page.url.target', 'aimeos_page'), ['path' => 'privacy']) }}">{{ __( 'Privacy Notice' ) }}</a></p>
									<p><a href="{{ airoute(config('shop.client.html.cms.page.url.target', 'aimeos_page'), ['path' => 'cancel']) }}">{{ __( 'Cancellation' ) }}</a></p>
								</div>
							</div>
							<div class="col-sm-6 footer-center">
								<div class="footer-block">
									<h2 class="pb-3" aria-label="{{ __('About the company') }}">{{ __( 'ABOUT US' ) }}</h2>
									<p><a href="{{ airoute(config('shop.client.html.cms.page.url.target', 'aimeos_page'), ['path' => 'contact']) }}">{{ __( 'Contact us' ) }}</a></p>
									<p><a href="{{ airoute(config('shop.client.html.cms.page.url.target', 'aimeos_page'), ['path' => 'about']) }}">{{ __( 'Company' ) }}</a></p>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-4 footer-right">
						<div class="footer-block">
							<a class="logo" href="/" title="{{ __('To the home page') }}">
								<img src="{{ asset( app( 'aimeos.context' )->get()->config()->get( 'resource/fs-media/baseurl' ) . '/' . ( app( 'aimeos.context' )->get()->locale()->getSiteItem()->getLogo() ?: '../vendor/shop/themes/default/assets/logo.png' ) ) }}" class="footer-logo" alt="{{ __('To the home page') }}">
							</a>
							<div class="social" aria-label="{{ __('Social media links') }}">
								<p><a href="#" class="sm facebook" title="Facebook" rel="noopener">Facebook</a></p>
								<p><a href="#" class="sm twitter" title="Twitter" rel="noopener">Twitter</a></p>
								<p><a href="#" class="sm instagram" title="Instagram" rel="noopener">Instagram</a></p>
								<p><a href="#" class="sm youtube" title="Youtube" rel="noopener">Youtube</a></p>
							</div>
						</div>
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
		@yield('aimeos_scripts')
	</body>
</html>
