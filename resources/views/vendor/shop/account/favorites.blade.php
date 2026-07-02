@extends('shop::base')

@section('aimeos_body')
<div class="container my-5" style="max-width:1100px">
    <h1 class="mb-4">{{ __('Mis favoritos') }}</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($favorites->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-heart" style="font-size:3rem; color:var(--ai-nav-text-muted,#94A3B8)"></i>
            <p class="mt-3 text-muted">{{ __('Aún no has añadido productos a tu lista de favoritos.') }}</p>
            <a href="{{ airoute('aimeos_shop_list') }}" class="btn btn-primary mt-2">
                {{ __('Explorar productos') }}
            </a>
        </div>
    @else
        <div class="row g-3">
            @foreach($favorites as $fav)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card h-100">
                        @if($fav->media_url)
                            <img src="{{ $fav->media_url }}" class="card-img-top" alt="{{ $fav->name }}" loading="lazy">
                        @endif
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title">{{ $fav->name ?? $fav->product_code ?? $fav->product_id }}</h6>
                            @if($fav->price)
                                <p class="fw-bold mb-2">${{ number_format($fav->price, 0, ',', '.') }}</p>
                            @endif
                            <form method="POST" action="{{ route('favorites.destroy', $fav->product_id) }}" class="mt-auto">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                    <i class="bi bi-trash"></i> {{ __('Quitar') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $favorites->links() }}
        </div>
    @endif
</div>
@stop
