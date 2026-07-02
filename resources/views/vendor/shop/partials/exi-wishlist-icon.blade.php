@php
    use App\Models\Favorite;
    $favCount = (int) (Cache::remember('exi_fav_count_' . md5((string) (auth()->id() ?: (session('exi_fav_id') ?: 'guest'))), 30, function () {
        return Favorite::currentCount();
    }) ?? 0);
@endphp

<a href="{{ route('favorites.index') }}"
   class="exi-wishlist"
   data-count="{{ $favCount }}"
   aria-label="@if($favCount > 0) {{ __('Tienes :n favoritos', ['n' => $favCount]) }} @else {{ __('Ver favoritos') }} @endif"
   title="{{ __('Mis favoritos') }}">
    <i class="bi bi-heart" aria-hidden="true"></i>
    <span class="badge" aria-hidden="true">{{ $favCount }}</span>
</a>
