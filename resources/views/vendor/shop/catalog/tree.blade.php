@extends('shop::base')

@section('aimeos_header')
	@php $_exiV = app('aimeos.context')->get()->locale()->getSiteItem()->getConfigValue('theme_version') ?? config('shop.version', 1); @endphp
	<?= $aiheader['locale/select'] ?? '' ?>
	<?= $aiheader['basket/mini'] ?? '' ?>
	<?= $aiheader['catalog/search'] ?? '' ?>
	<?= $aiheader['catalog/filter'] ?? '' ?>
	<?= $aiheader['catalog/tree'] ?? '' ?>
	<?= $aiheader['catalog/session'] ?? '' ?>
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
	@endphp
	<div class="container-fluid exicatalog-wrap @if($isInfluencers) exicatalog-wrap--influencers @endif">
		<div class="row">
			<aside class="col-lg-3 exicatalog-aside">
				<?= $aibody['catalog/filter'] ?? '' ?>
				<?= $aibody['catalog/session'] ?? '' ?>
			</aside>
			<div class="col-lg-9 exicatalog-main @if($isInfluencers) exicatalog-main--influencers @endif">
				@if($isInfluencers)
					@include('shop::catalog.partials.exi-influencers-hero')
				@endif
				<?= $aibody['catalog/lists'] ?? '' ?>
				<?= $aibody['cms/page'] ?? '' ?>
			</div>
		</div>
	</div>
@stop