<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $localeDir }}">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="csrf-token" content="{{ csrf_token() }}" />

		@if( config('app.debug') !== true )
			<meta http-equiv="Content-Security-Policy" content="default-src 'self' data: blob:; {{ config( 'shop.csp.backend', 'style-src \'unsafe-inline\' \'self\' https://cdnjs.cloudflare.com; script-src \'unsafe-eval\' \'self\'; connect-src \'self\' https://*.deepl.com https://api.openai.com; img-src \'self\' data: blob: https://*.tile.openstreetmap.org https://aimeos.org; frame-src https://www.youtube.com https://player.vimeo.com' ) }}">
		@endif

		<title>Exicompras - Administración</title>
		<link rel="icon" type="image/jpeg" href="{{ asset('images/exicompras.jpg') }}">

		<link rel="stylesheet" href="<?= airoute( 'aimeos_shop_jqadm_file', ['site' => $site, 'locale' => 'en', 'name' => 'vendor-css'] ) ?>">
		@if( $localeDir == 'rtl' )
			<link rel="stylesheet" href="<?= airoute( 'aimeos_shop_jqadm_file', ['site' => $site, 'locale' => 'en', 'name' => 'index-rtl-css'] ) ?>">
		@else
			<link rel="stylesheet" href="<?= airoute( 'aimeos_shop_jqadm_file', ['site' => $site, 'locale' => 'en', 'name' => 'index-ltr-css'] ) ?>">
		@endif
		<link rel="stylesheet" href="<?= airoute( 'aimeos_shop_jqadm_file', ['site' => $site, 'locale' => 'en', 'name' => 'index-css'] ) ?>">

		<style nonce="{{ app( 'aimeos.context' )->get( false )->nonce() }}">
			/* ── Exicompras Admin Theme ─────────────────────────────────── */

			/* Import Inter font */
			@import url('https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap');

			/* ── Theme tokens (light) ──────────────────────────────────── */
			body {
				--bs-bg: #f8fafc;
				--bs-bg-dark: #f1f5f9;
				--bs-bg-light: #ffffff;
				--bs-line: #e2e8f0;
				--bs-line-light: #f1f5f9;
				--bs-menu: #ffffff;
				--bs-menu-bg: #003D8F;
				--bs-menu-alt: #94a3b8;
				--bs-menu-alt-bg: #002F80;
				--bs-primary: #003D8F;
				--bs-primary-dark: #002060;
				--bs-primary-light: #0064D2;
				--bs-primary-alt: #003D8F;
				--bs-primary-alt-dark: #002060;
				--bs-primary-alt-light: #002F80;
				--bs-secondary: #475569;
				--bs-secondary-dark: #003D8F;
				--bs-secondary-light: #94a3b8;
				--bs-danger: #dc2626;
				--bs-warning: #FF6B00;
				--bs-success: #00A650;
				--bs-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px -1px rgba(0, 0, 0, 0.05);
				font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
			}

			/* ── Theme tokens (dark) ───────────────────────────────────── */
			body.dark {
				--bs-bg: #003D8F;
				--bs-bg-dark: #002060;
				--bs-bg-light: #002F80;
				--bs-line: #0064D2;
				--bs-line-light: #475569;
				--bs-menu: #f1f5f9;
				--bs-menu-bg: #002060;
				--bs-menu-alt: #94a3b8;
				--bs-menu-alt-bg: #003D8F;
				--bs-primary: #FF6B00;
				--bs-primary-light: #FF9040;
				--bs-primary-dark: #CC5500;
				--bs-primary-alt: #FF6B00;
				--bs-primary-alt-light: #FF9040;
				--bs-primary-alt-dark: #CC5500;
				--bs-secondary: #cbd5e1;
				--bs-secondary-dark: #f1f5f9;
				--bs-secondary-light: #64748b;
				--bs-danger: #ef4444;
				--bs-warning: #FF9040;
				--bs-success: #00CF63;
				--bs-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.3);
			}

			/* ── Global typography ─────────────────────────────────────── */
			body, .aimeos, .aimeos *, .dashboard,
			.btn, input, select, textarea, table {
				font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
			}

			/* ── Top bar ───────────────────────────────────────────────── */
			.app-menu {
				background: #003D8F !important;
				height: 3.25rem !important;
				border-bottom: 1px solid rgba(255,255,255,0.06);
			}

			@media (min-width: 992px) {
				.app-menu {
					background: #003D8F !important;
				}
			}

			/* Top bar icons (sun, moon, logout) */
			.app-menu .icon {
				cursor: pointer;
				padding: 0.875rem;
				height: 3.25rem;
				width: 3.25rem;
				margin: 0 0.125rem;
				color: rgba(255,255,255,0.7) !important;
				fill: rgba(255,255,255,0.7) !important;
				transition: color 0.2s ease, fill 0.2s ease, opacity 0.2s ease;
			}

			.app-menu .icon:hover {
				color: #FF6B00 !important;
				fill: #FF6B00 !important;
				opacity: 1;
			}

			.app-menu .app-menu-end {
				display: flex;
				align-items: center;
				gap: 0.25rem;
				padding-right: 0.5rem;
			}

			.app-menu button {
				border: none;
				color: rgba(255,255,255,0.7) !important;
				background-color: transparent;
				padding: 0;
				transition: color 0.2s ease;
			}

			.app-menu button:hover {
				color: #FF6B00 !important;
			}

			.app-menu button svg {
				fill: currentColor;
			}

			.app-menu .menu {
				padding: 0.75rem 1rem;
				color: rgba(255,255,255,0.8);
			}

			.app-menu .menu:after {
				background-color: rgba(255,255,255,0.8) !important;
			}

			body.dark .btn-theme.dark-mode { display: none }
			body.light .btn-theme.light-mode { display: none }
			#logout-form { display: inline-block; display: flex; align-items: center; gap: 0.125rem; }

			/* Ocultar logo de Aimeos en sidebar */
			.main-sidebar a.logo { display: none !important; }

			/* Logo + nombre en barra superior */
			.app-menu-logo {
				display: flex;
				align-items: center;
				gap: 0.625rem;
				padding: 0 0.75rem;
			}

			.app-menu-logo img {
				height: 2rem;
				width: 2rem;
				border-radius: 0.375rem;
				object-fit: cover;
				flex-shrink: 0;
			}

			.app-menu-logo .exicompras-bar-name {
				font-size: 1.0625rem;
				font-weight: 700;
				color: #ffffff;
				white-space: nowrap;
				letter-spacing: -0.015em;
			}

			/* ── Sidebar ───────────────────────────────────────────────── */
			.aimeos .main-sidebar {
				background-color: #003D8F !important;
				top: 3.25rem !important;
				border-right: 1px solid rgba(255,255,255,0.06);
			}

			@media (min-width: 992px) {
				.aimeos .main-sidebar {
					background: #003D8F !important;
				}
			}

			.aimeos .sidebar-wrapper {
				top: 3.25rem !important;
			}

			.aimeos .sidebar-menu {
				background-color: transparent !important;
			}

			/* Sidebar icons — identical in light & dark (sidebar is always dark) */
			.aimeos .sidebar-menu a,
			.aimeos .sidebar-menu > li > span,
			body:not(.dark) .aimeos .sidebar-menu a,
			body:not(.dark) .aimeos .sidebar-menu > li > span,
			body.dark .aimeos .sidebar-menu a,
			body.dark .aimeos .sidebar-menu > li > span {
				color: rgba(255,255,255,0.7) !important;
				font-size: 0.875rem;
				font-weight: 500;
				transition: all 0.15s ease;
				border-radius: 0.5rem;
				margin: 0.125rem 0.5rem;
				padding: 0.5rem 0.75rem !important;
			}

			.aimeos .sidebar-menu a:hover,
			.aimeos .sidebar-menu a:focus,
			body:not(.dark) .aimeos .sidebar-menu a:hover,
			body:not(.dark) .aimeos .sidebar-menu a:focus {
				color: #ffffff !important;
				background-color: rgba(255,255,255,0.08) !important;
			}

			/* Sidebar icon SVG masks — always inherit text color */
			.aimeos .main-sidebar .icon:after,
			body:not(.dark) .aimeos .main-sidebar .icon:after,
			body.dark .aimeos .main-sidebar .icon:after {
				background-color: currentColor !important;
				opacity: 1;
			}

			/* Active sidebar item — always amber */
			.aimeos .sidebar-menu > li.active > a,
			.aimeos .sidebar-menu > li.active > span,
			body:not(.dark) .aimeos .sidebar-menu > li.active > a,
			body:not(.dark) .aimeos .sidebar-menu > li.active > span,
			body:not(.dark) .aimeos .main-sidebar .sidebar-menu > li.active > a,
			body:not(.dark) .aimeos .main-sidebar .sidebar-menu > li.active > span {
				color: #FF6B00 !important;
				background-color: rgba(255,107,0,0.1) !important;
				border-radius: 0.5rem;
				margin: 0.125rem 0.5rem;
			}

			/* Hover sidebar item — always subtle white bg */
			body:not(.dark) .aimeos .main-sidebar .sidebar-menu > li:hover > a,
			body:not(.dark) .aimeos .main-sidebar .sidebar-menu > li:hover > span {
				color: #ffffff !important;
				background-color: rgba(255,255,255,0.08) !important;
			}

			.aimeos .main-sidebar .title,
			body:not(.dark) .aimeos .main-sidebar .title {
				color: rgba(255,255,255,0.4) !important;
				font-size: 0.6875rem;
				font-weight: 600;
				text-transform: uppercase;
				letter-spacing: 0.06em;
				padding: 0.75rem 0.75rem 0.25rem !important;
			}

			/* Sidebar tree-menu — el panel tiene fondo claro, texto oscuro */
			.aimeos .sidebar-menu .tree-menu-wrapper,
			body:not(.dark) .aimeos .sidebar-menu .tree-menu-wrapper {
				background-color: #ffffff !important;
			}

			.aimeos .sidebar-menu .tree-menu a,
			.aimeos .sidebar-menu li .tree-menu-wrapper .tree-menu a,
			body:not(.dark) .aimeos .sidebar-menu .tree-menu a,
			body:not(.dark) .aimeos .sidebar-menu li .tree-menu-wrapper .tree-menu a {
				color: #374151 !important;
				font-size: 0.8125rem;
			}

			.aimeos .sidebar-menu .tree-menu a:hover,
			.aimeos .sidebar-menu li .tree-menu-wrapper .tree-menu a:hover,
			body:not(.dark) .aimeos .sidebar-menu .tree-menu a:hover,
			body:not(.dark) .aimeos .sidebar-menu li .tree-menu-wrapper .tree-menu a:hover {
				color: #003D8F !important;
			}

			/* tree-menu en modo oscuro */
			body.dark .aimeos .sidebar-menu .tree-menu-wrapper {
				background-color: #002F80 !important;
			}

			body.dark .aimeos .sidebar-menu .tree-menu a,
			body.dark .aimeos .sidebar-menu li .tree-menu-wrapper .tree-menu a {
				color: rgba(255,255,255,0.75) !important;
			}

			body.dark .aimeos .sidebar-menu .tree-menu a:hover,
			body.dark .aimeos .sidebar-menu li .tree-menu-wrapper .tree-menu a:hover {
				color: #FF6B00 !important;
			}

			/* Ítems del tree-menu */
			.aimeos .sidebar-menu li .tree-menu li,
			body:not(.dark) .aimeos .sidebar-menu li .tree-menu li {
				background-color: #ffffff !important;
				color: #374151 !important;
			}

			body.dark .aimeos .sidebar-menu li .tree-menu li {
				background-color: #002F80 !important;
				color: rgba(255,255,255,0.75) !important;
			}

			/* ── Logo area fix ─────────────────────────────────────────── */
			.aimeos .logo {
				background-color: #003D8F !important;
				height: 3.25rem !important;
			}

			@media (min-width: 992px) {
				.aimeos .logo {
					top: 0 !important;
					height: 3.25rem !important;
				}
			}

			/* ── Main content area ─────────────────────────────────────── */
			.main-content {
				background-color: var(--bs-bg) !important;
				color: var(--bs-secondary) !important;
			}

			/* Texto base en área de contenido solo para modo claro */
			body:not(.dark) .main-content {
				color: #22292f !important;
			}

			body:not(.dark) .card,
			body:not(.dark) .aimeos .card,
			body:not(.dark) .card-header,
			body:not(.dark) .dashboard .order-latest table,
			body:not(.dark) .dashboard .order-latest table td,
			body:not(.dark) .dashboard .order-latest table th,
			body:not(.dark) .aimeos .list-items .table,
			body:not(.dark) .aimeos .list-items .table td,
			body:not(.dark) .aimeos .list-items .table th {
				color: #22292f !important;
			}

			/* Botones y sidebar siguen con texto claro */
			.btn-primary, .aimeos .btn-primary, .app-menu .icon, .app-menu .menu, .aimeos .sidebar-menu a, .aimeos .sidebar-menu > li > span {
				color: #fff !important;
			}

			.main-navbar {
				background-color: var(--bs-bg-light) !important;
				border-bottom: 1px solid var(--bs-line) !important;
				font-weight: 500;
				color: var(--bs-secondary-dark) !important;
			}

			/* ── Cards ─────────────────────────────────────────────────── */
			.card, .aimeos .card {
				border: 1px solid var(--bs-line) !important;
				border-radius: 0.75rem !important;
				box-shadow: var(--bs-shadow) !important;
				overflow: hidden;
				background-color: var(--bs-bg-light) !important;
				color: var(--bs-secondary) !important;
			}

			.card-header {
				background-color: var(--bs-bg-light) !important;
				border-bottom: 1px solid var(--bs-line) !important;
				font-weight: 600;
				color: var(--bs-secondary-dark) !important;
			}

			/* ── Dashboard ─────────────────────────────────────────────── */

			/* Quick stat cards */
			.dashboard .order-quick .card {
				border: 1px solid var(--bs-line) !important;
				border-radius: 0.75rem !important;
				transition: box-shadow 0.2s ease, transform 0.2s ease;
			}

			.dashboard .order-quick .card:hover {
				box-shadow: 0 4px 12px rgba(0,0,0,0.08) !important;
				transform: translateY(-1px);
			}

			body.dark .dashboard .order-quick .card:hover {
				box-shadow: 0 4px 12px rgba(0,0,0,0.25) !important;
			}

			.dashboard .quick-header {
				font-size: 0.8125rem !important;
				font-weight: 500;
				color: var(--bs-secondary-light) !important;
				text-transform: uppercase;
				letter-spacing: 0.03em;
			}

			.dashboard .quick-number {
				font-size: 2rem !important;
				font-weight: 700;
				letter-spacing: -0.025em;
				color: var(--bs-secondary-dark) !important;
			}

			/* Progress bar colors — adapt for dark mode */
			.dashboard .order-quick-counttotal .quick-length {
				background-color: #003D8F !important;
			}
			body.dark .dashboard .order-quick-counttotal .quick-length {
				background-color: #FF6B00 !important;
			}

			.dashboard .order-quick-countcompleted .quick-length {
				background-color: #00A650 !important;
			}
			body.dark .dashboard .order-quick-countcompleted .quick-length {
				background-color: #00CF63 !important;
			}

			.dashboard .order-quick-countunfinished .quick-length {
				background-color: #dc2626 !important;
			}
			body.dark .dashboard .order-quick-countunfinished .quick-length {
				background-color: #f87171 !important;
			}

			.dashboard .order-quick-countcustomer .quick-length {
				background-color: #FF6B00 !important;
			}
			body.dark .dashboard .order-quick-countcustomer .quick-length {
				background-color: #FF9040 !important;
			}

			/* Quick percent badges */
			.dashboard .quick-percent {
				border-radius: 2rem;
				font-size: 0.75rem !important;
				font-weight: 600;
			}

			.dashboard .quick-percent.positive {
				background-color: #D1FAE5 !important;
				color: #007A3B !important;
			}

			.dashboard .quick-percent.neutral {
				background-color: #f1f5f9 !important;
				color: #475569 !important;
			}

			.dashboard .quick-percent.negative {
				background-color: #fef2f2 !important;
				color: #dc2626 !important;
			}

			body.dark .dashboard .quick-percent.positive {
				background-color: rgba(22,163,74,0.2) !important;
				color: #00CF63 !important;
			}

			body.dark .dashboard .quick-percent.neutral {
				background-color: rgba(71,85,105,0.3) !important;
				color: #94a3b8 !important;
			}

			body.dark .dashboard .quick-percent.negative {
				background-color: rgba(220,38,38,0.2) !important;
				color: #f87171 !important;
			}

			/* Quick progress background */
			.dashboard .quick-progress {
				background-color: var(--bs-line) !important;
			}

			/* Latest orders table */
			.dashboard h3 {
				font-size: 1rem !important;
				font-weight: 600;
				letter-spacing: -0.01em;
				color: var(--bs-secondary-dark) !important;
			}

			.dashboard .order-latest table {
				font-size: 0.875rem;
				color: var(--bs-secondary) !important;
			}

			.dashboard .order-latest table td {
				padding: 0.625rem 0.75rem !important;
				border-bottom: 1px solid var(--bs-line) !important;
				vertical-align: middle;
				color: var(--bs-secondary) !important;
			}

			.dashboard .order-latest table tr:hover td {
				background-color: var(--bs-bg) !important;
			}

			/* Chart cards */
			.dashboard .chart .card {
				border-radius: 0.75rem !important;
			}

			.dashboard .chart h3 {
				padding: 1rem 1.25rem 0 !important;
			}

			.dashboard .chart .legend {
				background-color: var(--bs-bg-light) !important;
			}

			.dashboard .chart .legend span {
				font-size: 0.8125rem !important;
				color: var(--bs-secondary) !important;
			}

			/* ── Tables (general) ──────────────────────────────────────── */
			.aimeos .list-items .table {
				font-size: 0.875rem;
				color: var(--bs-secondary) !important;
			}

			.aimeos .list-items .table th {
				font-weight: 600;
				font-size: 0.75rem;
				text-transform: uppercase;
				letter-spacing: 0.04em;
				color: var(--bs-secondary-light) !important;
				background-color: var(--bs-bg) !important;
				border-bottom: 2px solid var(--bs-line) !important;
				padding: 0.625rem 0.75rem !important;
			}

			.aimeos .list-items .table td {
				padding: 0.625rem 0.75rem !important;
				vertical-align: middle;
				border-bottom: 1px solid var(--bs-line) !important;
				color: var(--bs-secondary) !important;
			}

			.aimeos .list-items .table tbody tr:hover td {
				background-color: var(--bs-bg) !important;
			}

			/* ── Buttons ───────────────────────────────────────────────── */
			/* Primary: dark in light mode, amber in dark mode */
			.btn-primary, .aimeos .btn-primary {
				background-color: var(--bs-primary-alt) !important;
				border-color: var(--bs-primary-alt) !important;
				color: var(--bs-menu) !important;
				border-radius: 0.5rem !important;
				font-weight: 600;
				font-size: 0.875rem;
				padding: 0.5rem 1rem !important;
				transition: all 0.2s ease;
			}

			.btn-primary:hover, .aimeos .btn-primary:hover {
				background-color: var(--bs-primary-alt-light) !important;
				border-color: var(--bs-primary-alt-light) !important;
				transform: translateY(-1px);
				box-shadow: 0 4px 8px rgba(0,0,0,0.15) !important;
			}

			/* Ensure text on primary buttons is always readable */
			body.dark .btn-primary, body.dark .aimeos .btn-primary {
				color: #003D8F !important;
			}

			body.dark .btn-primary:hover, body.dark .aimeos .btn-primary:hover {
				color: #003D8F !important;
			}

			/* Secondary button */
			.btn-secondary, .aimeos .btn-secondary {
				background-color: var(--bs-bg-light) !important;
				border: 1px solid var(--bs-line) !important;
				color: var(--bs-secondary-dark) !important;
				border-radius: 0.5rem !important;
				font-weight: 600;
				font-size: 0.875rem;
				transition: all 0.2s ease;
			}

			.btn-secondary:hover, .aimeos .btn-secondary:hover {
				background-color: var(--bs-bg) !important;
				border-color: var(--bs-secondary-light) !important;
			}

			/* Danger button */
			.btn-danger, .aimeos .btn-danger {
				background-color: var(--bs-danger) !important;
				border-color: var(--bs-danger) !important;
				color: #fff !important;
				border-radius: 0.5rem !important;
			}

			/* Outline/ghost buttons (icons, action btns) */
			.aimeos .btn:not(.btn-primary):not(.btn-secondary):not(.btn-danger) {
				color: var(--bs-secondary) !important;
			}

			.aimeos .btn:not(.btn-primary):not(.btn-secondary):not(.btn-danger):hover {
				color: var(--bs-secondary-dark) !important;
				background-color: var(--bs-bg) !important;
			}

			/* Action icon buttons (add, delete, edit, etc.) */
			.aimeos .btn.act-add,
			.aimeos .btn.act-copy,
			.aimeos .btn.act-import {
				color: var(--bs-primary-alt) !important;
			}

			.aimeos .btn.act-add:hover,
			.aimeos .btn.act-copy:hover,
			.aimeos .btn.act-import:hover {
				color: var(--bs-primary-alt-light) !important;
				background-color: transparent !important;
			}

			.aimeos .btn.act-delete {
				color: var(--bs-danger) !important;
			}

			.aimeos .btn.act-delete:hover {
				color: #b91c1c !important;
				background-color: transparent !important;
			}

			body.dark .aimeos .btn.act-delete:hover {
				color: #fca5a5 !important;
			}

			/* Save button icon */
			.aimeos .btn.act-save {
				color: var(--bs-primary-alt) !important;
			}

			/* ── Form inputs ───────────────────────────────────────────── */
			.form-control, .aimeos .form-control {
				border: 1px solid var(--bs-line) !important;
				border-radius: 0.5rem !important;
				font-size: 0.875rem;
				padding: 0.5rem 0.75rem !important;
				background-color: var(--bs-bg-light) !important;
				color: var(--bs-secondary-dark) !important;
				transition: border-color 0.2s ease, box-shadow 0.2s ease;
			}

			.form-control:focus, .aimeos .form-control:focus {
				border-color: #FF6B00 !important;
				box-shadow: 0 0 0 3px rgba(255,107,0,0.1) !important;
				outline: none !important;
			}

			.form-control::placeholder {
				color: var(--bs-secondary-light) !important;
			}

			.form-select {
				border: 1px solid var(--bs-line) !important;
				border-radius: 0.5rem !important;
				font-size: 0.875rem;
				background-color: var(--bs-bg-light) !important;
				color: var(--bs-secondary-dark) !important;
			}

			.form-select:focus {
				border-color: #FF6B00 !important;
				box-shadow: 0 0 0 3px rgba(255,107,0,0.1) !important;
			}

			/* ── Nav tabs ──────────────────────────────────────────────── */
			.nav-tabs .nav-link {
				font-weight: 500;
				font-size: 0.875rem;
				color: var(--bs-secondary) !important;
				border-radius: 0.5rem 0.5rem 0 0 !important;
			}

			.nav-tabs .nav-link.active {
				color: var(--bs-secondary-dark) !important;
				font-weight: 600;
				background-color: var(--bs-bg-light) !important;
				border-color: var(--bs-line) var(--bs-line) transparent !important;
			}

			body.dark .nav-tabs .nav-link.active {
				color: #FF6B00 !important;
			}

			/* ── Modal dialogs ─────────────────────────────────────────── */
			.modal-content {
				background-color: var(--bs-bg-light) !important;
				border: 1px solid var(--bs-line) !important;
				border-radius: 0.75rem !important;
				color: var(--bs-secondary) !important;
			}

			.modal-header {
				border-bottom: 1px solid var(--bs-line) !important;
				color: var(--bs-secondary-dark) !important;
			}

			.modal-footer {
				border-top: 1px solid var(--bs-line) !important;
			}

			.modal-backdrop, .modal {
				background-color: rgba(15,23,42,0.5) !important;
			}

			/* ── Pagination ────────────────────────────────────────────── */
			.pagination .page-link {
				border-radius: 0.375rem !important;
				font-size: 0.875rem;
				margin: 0 0.125rem;
				color: var(--bs-secondary) !important;
				background-color: var(--bs-bg-light) !important;
				border-color: var(--bs-line) !important;
			}

			.pagination .page-link:hover {
				background-color: var(--bs-bg) !important;
				color: var(--bs-secondary-dark) !important;
			}

			.pagination .active .page-link {
				background-color: var(--bs-primary-alt) !important;
				border-color: var(--bs-primary-alt) !important;
				color: var(--bs-menu) !important;
			}

			body.dark .pagination .active .page-link {
				color: #003D8F !important;
			}

			/* ── Badges ────────────────────────────────────────────────── */
			.badge {
				border-radius: 2rem !important;
				font-weight: 600;
				font-size: 0.75rem;
				padding: 0.25rem 0.625rem !important;
			}

			/* ── Item detail panels ────────────────────────────────────── */
			.aimeos .item-content {
				background-color: var(--bs-bg-light) !important;
				color: var(--bs-secondary) !important;
			}

			.aimeos .item-header {
				color: var(--bs-secondary-dark) !important;
			}

			.aimeos .item-media .media-preview {
				border: 2px dashed var(--bs-line) !important;
				border-radius: 0.5rem;
			}

			/* ── Labels and text ───────────────────────────────────────── */
			.aimeos label,
			.aimeos .box label {
				color: var(--bs-secondary) !important;
				font-weight: 500;
				font-size: 0.8125rem;
			}

			/* ── Links ─────────────────────────────────────────────────── */
			.aimeos a:not(.btn):not(.nav-link):not(.sidebar-menu a) {
				color: var(--bs-primary-alt) !important;
			}

			.aimeos a:not(.btn):not(.nav-link):not(.sidebar-menu a):hover {
				color: var(--bs-primary-alt-light) !important;
			}

			/* ── Accordion/collapse toggle icons ───────────────────────── */
			.dashboard .btn.act-show.icon::after,
			.aimeos .btn.act-show.icon::after {
				background-color: var(--bs-secondary) !important;
			}

			/* ── Scrollbar ─────────────────────────────────────────────── */
			::-webkit-scrollbar {
				width: 0.35rem;
				background-color: var(--bs-bg);
			}

			::-webkit-scrollbar-thumb {
				background-color: var(--bs-line);
				border-radius: 0.25rem;
			}

			/* ── Smooth transitions ────────────────────────────────────── */
			.card, .btn, .form-control, .form-select, .nav-link, table td {
				transition: all 0.15s ease;
			}

			/* ── Selection ─────────────────────────────────────────────── */
			::selection {
				background-color: rgba(255,107,0,0.2);
				color: var(--bs-secondary-dark);
			}

			/* ── Footer link ───────────────────────────────────────────── */
			.aimeos .footer a {
				color: var(--bs-secondary-light) !important;
				font-size: 0.8125rem;
			}

			.aimeos .footer a:hover {
				color: var(--bs-secondary) !important;
			}

			/* ══ LIGHT MODE FIXES ══════════════════════════════════════ */

			/* ── Table headers: vendor sets white text (--bs-menu) ───── */
			.aimeos .list-items th {
				background-color: var(--bs-primary) !important;
				color: var(--bs-menu) !important;
			}

			.aimeos .list-items th.actions {
				background-color: var(--bs-primary-light) !important;
			}

			/* Links & buttons INSIDE table headers — vendor: color: var(--bs-menu, #fff) */
			.aimeos .list-items th a,
			.aimeos .list-items th button,
			.aimeos .list-items th .icon::after {
				color: var(--bs-menu) !important;
				background-color: currentColor;
			}

			.aimeos .list-items th .btn.icon,
			.aimeos .list-items th .btn.icon::after,
			.aimeos .list-items th .icon-menu::after {
				color: var(--bs-menu) !important;
				background-color: currentColor !important;
			}

			.aimeos .list-items th a:hover,
			.aimeos .list-items th button:hover {
				color: #FF6B00 !important;
			}

			/* ── New-row action buttons (white icons on white bg) ───── */
			.aimeos .table .list-item-new .actions .btn,
			.aimeos table .list-item-new .actions .btn {
				color: var(--bs-primary-alt) !important;
			}

			.aimeos table .list-item-new .actions .btn.act-close {
				color: var(--bs-danger) !important;
			}

			.aimeos table .list-item-new .actions .btn:hover {
				color: #FF6B00 !important;
			}

			/* ── Btn-secondary hover/active/focus — vendor: color: var(--bs-bg-light) = white */
			.btn-secondary:hover,
			.btn-secondary:active,
			.btn-secondary:focus,
			.aimeos .btn-secondary:hover,
			.aimeos .btn-secondary:active,
			.aimeos .btn-secondary:focus {
				color: var(--bs-secondary-dark) !important;
				background-color: var(--bs-bg) !important;
				border-color: var(--bs-secondary-light) !important;
			}

			/* ── Pagination hover — vendor: color: var(--bs-bg-light) = white */
			.aimeos .list-page .page-item:not(.disabled) .page-link:hover {
				color: var(--bs-menu) !important;
				background-color: var(--bs-primary-alt) !important;
				border-color: var(--bs-primary-alt) !important;
			}

			body.dark .aimeos .list-page .page-item:not(.disabled) .page-link:hover {
				color: #003D8F !important;
			}

			/* Page-limit dropdown active state — vendor: color: var(--bs-menu) = white */
			.aimeos .page-limit .dropdown-link:active,
			.aimeos .page-limit .dropdown-link:focus {
				color: var(--bs-secondary-dark) !important;
			}

			/* ── Sidebar active .title/.icon children — always amber (both modes) */
			.aimeos .sidebar-menu .active > a .title,
			.aimeos .sidebar-menu .active > span .title,
			.aimeos .sidebar-menu .active > a .icon,
			.aimeos .sidebar-menu .active > span .icon,
			.aimeos .sidebar-menu li.active > :not(.tree-menu-wrapper) > .title,
			.aimeos .sidebar-menu li.active > :not(.tree-menu-wrapper) > .icon,
			body:not(.dark) .aimeos .sidebar-menu .active > a .title,
			body:not(.dark) .aimeos .sidebar-menu .active > span .title,
			body:not(.dark) .aimeos .sidebar-menu .active > a .icon,
			body:not(.dark) .aimeos .sidebar-menu .active > span .icon,
			body:not(.dark) .aimeos .sidebar-menu li.active > :not(.tree-menu-wrapper) > .title,
			body:not(.dark) .aimeos .sidebar-menu li.active > :not(.tree-menu-wrapper) > .icon {
				color: #FF6B00 !important;
			}

			.aimeos .sidebar-menu li.active > :not(.tree-menu-wrapper) > .icon::after,
			body:not(.dark) .aimeos .sidebar-menu li.active > :not(.tree-menu-wrapper) > .icon::after {
				background-color: #FF6B00 !important;
			}

			/* Sidebar non-active icons — always white (both modes), excepto dentro del tree-menu-wrapper */
			.aimeos .sidebar-menu li:not(.active) > a:not(.tree-menu-wrapper a) .icon,
			.aimeos .sidebar-menu li:not(.active) > span:not(.tree-menu-wrapper span) .icon,
			body:not(.dark) .aimeos .sidebar-menu li:not(.active) > a:not(.tree-menu-wrapper a) .icon,
			body:not(.dark) .aimeos .sidebar-menu li:not(.active) > span:not(.tree-menu-wrapper span) .icon {
				color: rgba(255,255,255,0.7) !important;
			}

			.aimeos .sidebar-menu li:not(.active) > a .icon::after,
			.aimeos .sidebar-menu li:not(.active) > span .icon::after,
			body:not(.dark) .aimeos .sidebar-menu li:not(.active) > a .icon::after,
			body:not(.dark) .aimeos .sidebar-menu li:not(.active) > span .icon::after {
				background-color: rgba(255,255,255,0.7) !important;
			}

			/* Iconos dentro del tree-menu-wrapper (panel lateral) — oscuros en modo claro */
			/* Todo el texto dentro del tree-menu-wrapper — modo claro */
			body:not(.dark) .aimeos .sidebar-menu li .tree-menu-wrapper,
			body:not(.dark) .aimeos .sidebar-menu li .tree-menu-wrapper * {
				color: #374151 !important;
			}

			/* Excepciones: el header del panel conserva fondo azul con texto blanco */
			body:not(.dark) .aimeos .sidebar-menu li .tree-menu-wrapper .menu-header,
			body:not(.dark) .aimeos .sidebar-menu li .tree-menu-wrapper .menu-header *,
			body:not(.dark) .aimeos .sidebar-menu li .tree-menu-wrapper .menu-header a {
				color: #ffffff !important;
			}

			/* Iconos dentro del tree-menu-wrapper (panel lateral) — oscuros en modo claro */
			body:not(.dark) .aimeos .sidebar-menu li .tree-menu-wrapper .icon,
			body:not(.dark) .aimeos .sidebar-menu li .tree-menu-wrapper a .icon,
			body:not(.dark) .aimeos .sidebar-menu li .tree-menu-wrapper .icon-open,
			body:not(.dark) .aimeos .sidebar-menu li .tree-menu-wrapper .icon-close,
			body:not(.dark) .aimeos .sidebar-menu li .tree-menu-wrapper .icon-loading {
				color: #374151 !important;
			}

			body:not(.dark) .aimeos .sidebar-menu li .tree-menu-wrapper .icon::after,
			body:not(.dark) .aimeos .sidebar-menu li .tree-menu-wrapper a .icon::after,
			body:not(.dark) .aimeos .sidebar-menu li .tree-menu-wrapper .icon-open:after,
			body:not(.dark) .aimeos .sidebar-menu li .tree-menu-wrapper .icon-close:after,
			body:not(.dark) .aimeos .sidebar-menu li .tree-menu-wrapper .icon-loading:after {
				background-color: #374151 !important;
			}

			/* Iconos dentro del tree-menu-wrapper en modo oscuro — blancos */
			body.dark .aimeos .sidebar-menu li .tree-menu-wrapper .icon,
			body.dark .aimeos .sidebar-menu li .tree-menu-wrapper a .icon,
			body.dark .aimeos .sidebar-menu li .tree-menu-wrapper .icon-open,
			body.dark .aimeos .sidebar-menu li .tree-menu-wrapper .icon-close,
			body.dark .aimeos .sidebar-menu li .tree-menu-wrapper .icon-loading {
				color: rgba(255,255,255,0.75) !important;
			}

			body.dark .aimeos .sidebar-menu li .tree-menu-wrapper .icon::after,
			body.dark .aimeos .sidebar-menu li .tree-menu-wrapper a .icon::after,
			body.dark .aimeos .sidebar-menu li .tree-menu-wrapper .icon-open:after,
			body.dark .aimeos .sidebar-menu li .tree-menu-wrapper .icon-close:after,
			body.dark .aimeos .sidebar-menu li .tree-menu-wrapper .icon-loading:after {
				background-color: rgba(255,255,255,0.75) !important;
			}

			/* Input y texto dentro del tree-menu-wrapper — modo claro */
			body:not(.dark) .aimeos .sidebar-menu li .tree-menu-wrapper .filter input,
			body:not(.dark) .aimeos .sidebar-menu li .tree-menu-wrapper input {
				color: #374151 !important;
				background-color: #f9fafb !important;
				border: 1px solid #d1d5db !important;
			}

			body:not(.dark) .aimeos .sidebar-menu li .tree-menu-wrapper .filter input::placeholder {
				color: #9ca3af !important;
			}

			/* Input y texto dentro del tree-menu-wrapper — modo oscuro */
			body.dark .aimeos .sidebar-menu li .tree-menu-wrapper .filter input,
			body.dark .aimeos .sidebar-menu li .tree-menu-wrapper input {
				color: rgba(255,255,255,0.85) !important;
				background-color: rgba(255,255,255,0.08) !important;
				border: 1px solid rgba(255,255,255,0.15) !important;
			}

			body.dark .aimeos .sidebar-menu li .tree-menu-wrapper .filter input::placeholder {
				color: rgba(255,255,255,0.45) !important;
			}

			/* ── Content area icons — ensure visible in light mode ───── */
			.aimeos .main-content .icon::after,
			.aimeos .item-content .icon::after {
				background-color: currentColor !important;
			}

			/* ── List search bar ───────────────────────────────────────── */
			.aimeos .list-search {
				background-color: var(--bs-bg-light) !important;
				color: var(--bs-secondary) !important;
			}

			.aimeos .list-search .btn,
			.aimeos .list-search .icon::after {
				color: var(--bs-secondary) !important;
			}

			.aimeos .list-search .btn:hover {
				color: var(--bs-primary-alt) !important;
			}

			/* ── Navbar inside content (tabs bar) ──────────────────────── */
			.aimeos .main-navbar,
			.aimeos .item-navbar {
				background-color: var(--bs-bg-light) !important;
				color: var(--bs-secondary-dark) !important;
			}

			.aimeos .main-navbar a,
			.aimeos .item-navbar a,
			.aimeos .item-navbar .nav-link {
				color: var(--bs-secondary) !important;
			}

			.aimeos .main-navbar a:hover,
			.aimeos .item-navbar a:hover,
			.aimeos .item-navbar .nav-link:hover {
				color: var(--bs-secondary-dark) !important;
			}

			.aimeos .main-navbar a.active,
			.aimeos .item-navbar a.active,
			.aimeos .item-navbar .nav-link.active {
				color: var(--bs-primary-alt) !important;
				font-weight: 600;
			}

			body.dark .aimeos .main-navbar a.active,
			body.dark .aimeos .item-navbar a.active,
			body.dark .aimeos .item-navbar .nav-link.active {
				color: #FF6B00 !important;
			}

			/* ── Column config dropdown & popover ──────────────────────── */
			.aimeos .vue-columns .dropdown-menu,
			.aimeos .popover {
				background-color: var(--bs-bg-light) !important;
				border: 1px solid var(--bs-line) !important;
				color: var(--bs-secondary) !important;
			}

			.aimeos .vue-columns .dropdown-menu label,
			.aimeos .vue-columns .dropdown-menu input {
				color: var(--bs-secondary-dark) !important;
			}

			/* ── Coupon upload icon — vendor: color: var(--bs-menu) ──── */
			.aimeos .item-coupon .actions .icon-upload::before {
				color: var(--bs-menu) !important;
			}

			/* ── Breadcrumb / content header text ──────────────────────── */
			.aimeos .main-content h1,
			.aimeos .main-content h2,
			.aimeos .main-content h3,
			.aimeos .main-content h4 {
				color: var(--bs-secondary-dark) !important;
			}

			/* ── Tree menu header — ensure correct colors ──────────────── */
			.aimeos .sidebar-menu li .tree-menu-wrapper .menu-header {
				background-color: var(--bs-menu-bg) !important;
				color: var(--bs-menu) !important;
			}

			/* ── Action icons in table rows — ensure visible ──────────── */
			.aimeos .list-items td .btn .icon::after,
			.aimeos .list-items td a .icon::after {
				background-color: currentColor !important;
			}

			.aimeos .list-items td .btn,
			.aimeos .list-items td a:not(.btn) {
				color: var(--bs-secondary) !important;
			}

			.aimeos .list-items td .btn:hover,
			.aimeos .list-items td a:not(.btn):hover {
				color: var(--bs-primary-alt) !important;
			}

			.aimeos .list-items td .btn.act-delete {
				color: var(--bs-danger) !important;
			}

			/* Contraste de iconos/acciones en filas de listas (CMS y similares) */
			body:not(.dark) .aimeos .list-items td.actions .btn,
			body:not(.dark) .aimeos .list-items tr.list-search td .btn {
				color: #475569 !important;
			}

			body:not(.dark) .aimeos .list-items td.actions .btn.act-delete {
				color: #dc2626 !important;
			}

			body:not(.dark) .aimeos .list-items td.actions .btn:hover,
			body:not(.dark) .aimeos .list-items tr.list-search td .btn:hover {
				color: #003D8F !important;
			}

			body.dark .aimeos .list-items td.actions .btn,
			body.dark .aimeos .list-items tr.list-search td .btn {
				color: #cbd5e1 !important;
			}

			body.dark .aimeos .list-items td.actions .btn.act-delete {
				color: #f87171 !important;
			}

			body.dark .aimeos .list-items td.actions .btn:hover,
			body.dark .aimeos .list-items tr.list-search td .btn:hover {
				color: #FF9040 !important;
			}
		</style>
	</head>
	<body class="{{ $theme }}">
		<div class="app-menu">
			<span class="menu"></span>
			<div class="app-menu-logo">
				<img src="/images/exicompras.jpg" alt="Exicompras">
				<span class="exicompras-bar-name">Exicompras</span>
			</div>
			<div class="app-menu-end">
				<form id="logout-form" action="{{ airoute( 'logout', ['locale' => Request::get( 'locale', app()->getLocale() )] ) }}" method="POST">
					{{ csrf_field() }}
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-sun icon btn-theme light-mode" viewBox="0 0 16 16"><path d="M8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6m0 1a4 4 0 1 0 0-8 4 4 0 0 0 0 8M8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0m0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13m8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5M3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8m10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0m-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0m9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707M4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708"/></svg>
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-moon icon btn-theme dark-mode" viewBox="0 0 16 16"><path d="M6 .278a.77.77 0 0 1 .08.858 7.2 7.2 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277q.792-.001 1.533-.16a.79.79 0 0 1 .81.316.73.73 0 0 1-.031.893A8.35 8.35 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.75.75 0 0 1 6 .278M4.858 1.311A7.27 7.27 0 0 0 1.025 7.71c0 4.02 3.279 7.276 7.319 7.276a7.32 7.32 0 0 0 5.205-2.162q-.506.063-1.029.063c-4.61 0-8.343-3.714-8.343-8.29 0-1.167.242-2.278.681-3.286"/></svg>
					<button><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right icon logout" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/><path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/></svg></button>
				</form>
			</div>
		</div>

<?= $content ?>

		<script src="<?= airoute( 'aimeos_shop_jqadm_file', array( 'site' => $site, 'locale' => 'en', 'name' => 'vendor-js' ) ) ?>"></script>
		<script src="<?= airoute( 'aimeos_shop_jqadm_file', array( 'site' => $site, 'locale' => 'en', 'name' => 'index-js' ) ) ?>"></script>

	</body>
</html>

