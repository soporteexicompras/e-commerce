<!DOCTYPE html>
<html class="no-js" lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), ['ar', 'az', 'dv', 'fa', 'he', 'ku', 'ur']) ? 'rtl' : 'ltr' }}">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<meta name="theme-color" content="#141828">

		@if( config('app.debug') !== true )
			<meta http-equiv="Content-Security-Policy" content="base-uri 'self'; default-src 'self' 'nonce-{{ app( 'aimeos.context' )->get()->nonce() }}'; {{ config( 'shop.csp.frontend', 'style-src \'unsafe-inline\' \'self\'; img-src \'self\' data: https://aimeos.org; frame-src https://www.youtube.com https://player.vimeo.com' ) }}">
		@endif

		@if( in_array(app()->getLocale(), ['ar', 'az', 'dv', 'fa', 'he', 'ku', 'ur']) )
			<link type="text/css" rel="stylesheet" href="{{ asset('vendor/shop/themes/default/app.rtl.css?v=' . config( 'shop.version', 1 ) ) }}">
		@else
			<link type="text/css" rel="stylesheet" href="{{ asset('vendor/shop/themes/default/app.css?v=' . config( 'shop.version', 1 ) ) }}">
		@endif
		<link type="text/css" rel="stylesheet" href="{{ asset('vendor/shop/themes/default/aimeos.css?v=' . config( 'shop.version', 1 ) ) }}">

		@yield('aimeos_header')

		@php
			$themeVars  = app( 'aimeos.context' )->get()->locale()->getSiteItem()->getConfigValue( 'theme/default', [] );
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
			/* Logo navbar — responsive basado en el tamaño configurado en el panel */
			.navbar-logo { height: {{ $logoMobile }}px; width: auto; max-width: 100%; }
			@media (min-width: 576px)  { .navbar-logo { height: {{ $logoTablet }}px; } }
			@media (min-width: 992px)  { .navbar-logo { height: {{ $logoBase }}px; } }
			@media (min-width: 1400px) { .navbar-logo { height: {{ $logoXl }}px; } }
		/* ── Responsive global ── */
		*, *::before, *::after { box-sizing: border-box; }
		html { scroll-behavior: smooth; }
		body { overflow-x: hidden; }
		img { max-width: 100%; height: auto; }
		/* Navbar: altura mínima consistente en móvil */
		.navbar { min-height: 56px; }
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
			@php $catText = $themeVars['--ai-nav-text'] ?? '#f0f2ff';
			     $catHover = $themeVars['--ai-nav-text-hover'] ?? $themeVars['--ai-tertiary'] ?? '#4a7eff';
			     $catNavBg = $themeVars['--ai-nav-bg'] ?? '#141828'; @endphp
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
		<link rel="stylesheet" href="{{ asset('css/exihome.css?v=1') }}">
		@endif

		<link rel="icon" href="{{ asset( app( 'aimeos.context' )->get()->config()->get( 'resource/fs-media/baseurl' ) . '/' . ( app( 'aimeos.context' )->get()->locale()->getSiteItem()->getIcon() ?: '../vendor/shop/themes/default/assets/icon.png' ) ) }}">

		<link rel="preload" href="{{ asset('vendor/shop/themes/default/assets/roboto-condensed-v19-latin-regular.woff2') }}" as="font" type="font/woff2" crossorigin>
		<link rel="preload" href="{{ asset('vendor/shop/themes/default/assets/roboto-condensed-v19-latin-700.woff2') }}" as="font" type="font/woff2" crossorigin>
		<link rel="preload" href="{{ asset('vendor/shop/themes/default/assets/bootstrap-icons.woff2') }}" as="font" type="font/woff2" crossorigin>
	</head>
	<body class="{{ $page ?? '' }}">
		<nav class="navbar navbar-expand-md navbar-top">
			<a class="navbar-brand" href="/" title="{{ __('To the home page') }}">
				<img src="{{ asset( app( 'aimeos.context' )->get()->config()->get( 'resource/fs-media/baseurl' ) . '/' . ( app( 'aimeos.context' )->get()->locale()->getSiteItem()->getLogo() ?: '../vendor/shop/themes/default/assets/logo.png' ) ) }}" class="navbar-logo" alt="{{ __('To the home page') }}">
			</a>

			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-top" aria-controls="navbar-top" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbar-top">
				@yield('aimeos_head_nav')
			</div>

			@yield('aimeos_head_locale')
			@yield('aimeos_head_search')

			<ul class="navbar-nav">
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
		</nav>

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
			<img src="{{ asset( app( 'aimeos.context' )->get()->config()->get( 'resource/fs-media/baseurl' ) . '/' . ( app( 'aimeos.context' )->get()->locale()->getSiteItem()->getLogo() ?: '../vendor/shop/themes/default/assets/logo.png' ) ) }}" class="navbar-logo" alt="{{ __('To the home page') }}">
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
		<script src="{{ asset('vendor/shop/themes/default/app.js?v=' . config( 'shop.version', 1 ) ) }}"></script>
		<script src="{{ asset('vendor/shop/themes/default/aimeos.js?v=' . config( 'shop.version', 1 ) ) }}"></script>
		@yield('aimeos_scripts')
	</body>
</html>
