@extends('shop::base')

@section('aimeos_header')
	@php $_exiV = app('aimeos.context')->get()->locale()->getSiteItem()->getConfigValue('theme_version') ?? config('shop.version', 1); @endphp
	<?= $aiheader['locale/select'] ?? '' ?>
	<?= $aiheader['basket/mini'] ?? '' ?>
	<?= $aiheader['catalog/search'] ?? '' ?>
	<?= $aiheader['catalog/filter'] ?? '' ?>
	<?= $aiheader['catalog/tree'] ?? '' ?>
	<?= $aiheader['catalog/stage'] ?? '' ?>
	<?= $aiheader['catalog/lists'] ?? '' ?>
	<link type="text/css" rel="stylesheet" href="{{ asset('css/exicatalog.css?v=' . $_exiV) }}">
@stop

@section('aimeos_head_basket')
	<?= $aibody['basket/mini'] ?? '' ?>
@stop

@section('aimeos_head_nav')
	<?= $aibody['catalog/tree'] ?? '' ?>
@stop

@section('aimeos_head_locale')
	<?= $aibody['locale/select'] ?? '' ?>
@stop

@section('aimeos_head_search')
	<?= $aibody['catalog/search'] ?? '' ?>
@stop

@section('aimeos_stage')
	<?= $aibody['catalog/stage'] ?? '' ?>
@stop

@section('aimeos_body')
	@php
		$isInfluencers = request()->is('*influencers*') || request()->input('f_name') === 'influencers';
		$isArtistas    = request()->is('*artistas*')    || request()->input('f_name') === 'artistas';
	@endphp
	<div class="container-fluid exicatalog-wrap @if($isInfluencers) exicatalog-wrap--influencers @elseif($isArtistas) exicatalog-wrap--artistas @endif">
		<div class="row">
			<aside class="col-lg-3 exicatalog-aside">
				<?= $aibody['catalog/filter'] ?? '' ?>
			</aside>
			<div class="col-lg-9 exicatalog-main @if($isInfluencers) exicatalog-main--influencers @elseif($isArtistas) exicatalog-main--artistas @endif">
				@if($isInfluencers)
					@include('shop::catalog.partials.exi-influencers-hero')
				@elseif($isArtistas)
					@include('shop::catalog.partials.exi-artistas-hero')
				@endif
				<?= $aibody['catalog/lists'] ?? '' ?>
				<?= $aibody['cms/page'] ?? '' ?>
			</div>
		</div>
	</div>

	@if($isInfluencers || $isArtistas)
		{{--
			Premium staggered entrance para los productos de Influencers / Artistas.
			Estrategia:
			- Marca <html class="js-ready"> para que el CSS aplique el estado pre-animacion.
			- Asigna --exi-stagger-i a cada producto (0..n).
			- Usa IntersectionObserver para disparar cuando los productos entran en viewport.
			- Fallback rAF + timeout 800ms por si el observer no dispara (productos ya en viewport al cargar).
			- Respeta prefers-reduced-motion (skip + marcar is-loaded de inmediato).
		--}}
		<script>
		(function () {
			'use strict';

			// 1. Producto no fue inicializado o navegador sin classList -> bail
			if (!document.documentElement.classList) return;
			document.documentElement.classList.add('js-ready');

			// 2. Localizar el grid de productos (Influencers o Artistas)
			var root = document.querySelector('.exicatalog-main--influencers .catalog-list-items')
				|| document.querySelector('.exicatalog-main--artistas .catalog-list-items');
			if (!root) return;

			// 3. Respetar prefers-reduced-motion
			var prefersReduced = window.matchMedia
				&& window.matchMedia('(prefers-reduced-motion: reduce)').matches;
			if (prefersReduced) {
				root.classList.add('is-loaded');
				return;
			}

			// 4. Asignar indices de stagger (0, 1, 2, ...)
			var children = Array.prototype.slice.call(root.children);
			var n = children.length;
			if (n === 0) return;
			for (var i = 0; i < n; i++) {
				children[i].style.setProperty('--exi-stagger-i', i);
			}

			// 5. Disparador
			var fired = false;
			function trigger() {
				if (fired) return;
				fired = true;
				root.classList.add('is-loaded');
				// Limpiar variables CSS tras la animacion
				setTimeout(function () {
					for (var j = 0; j < children.length; j++) {
						children[j].style.removeProperty('--exi-stagger-i');
					}
				}, 1200);
			}

			// 6. Disparar cuando el grid entre en viewport
			if ('IntersectionObserver' in window) {
				var io = new IntersectionObserver(function (entries) {
					entries.forEach(function (entry) {
						if (entry.isIntersecting) {
							trigger();
							io.disconnect();
						}
					});
				}, { threshold: 0.05, rootMargin: '0px 0px -10% 0px' });
				io.observe(root);

				// Safety: si el observer no dispara (productos ya en viewport antes
				// de que IO se conecte), forzar despues de 800ms.
				setTimeout(function () {
					if (!root.classList.contains('is-loaded')) trigger();
				}, 800);
			} else {
				// Navegadores sin IntersectionObserver
				requestAnimationFrame(function () {
					setTimeout(trigger, 60);
				});
			}
		})();
		</script>
	@endif
@stop