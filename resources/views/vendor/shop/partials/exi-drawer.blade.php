@php
    $logoUrl = asset( app( 'aimeos.context' )->get()->config()->get( 'resource/fs-media/baseurl' ) . '/' . ( app( 'aimeos.context' )->get()->locale()->getSiteItem()->getLogo() ?: '../vendor/shop/themes/default/assets/logo.png' ) );
@endphp

<aside id="exiDrawer" class="exi-drawer" role="dialog" aria-modal="true" aria-labelledby="exiDrawerTitle" aria-hidden="true" tabindex="-1">
    <div class="exi-drawer-backdrop" data-exi-drawer-close aria-hidden="true"></div>
    <div class="exi-drawer-panel">
        <header class="exi-drawer-header">
            <a href="/" class="exi-drawer-brand" aria-label="{{ __('Inicio') }}">
                <img src="{{ $logoUrl }}" alt="Exicompras" class="exi-drawer-logo">
            </a>
            <h2 id="exiDrawerTitle" class="visually-hidden">{{ __('Menú principal') }}</h2>
            <button type="button" class="exi-drawer-close" data-exi-drawer-close aria-label="{{ __('Cerrar menú') }}">
                <i class="bi bi-x-lg" aria-hidden="true"></i>
            </button>
        </header>

        <div class="exi-drawer-search">
            <form method="GET" action="{{ airoute('aimeos_shop_list') }}" role="search">
                <input type="search" name="f_search" placeholder="{{ __('Buscar productos…') }}" aria-label="{{ __('Buscar productos') }}" autocomplete="off">
                <button type="submit" aria-label="{{ __('Buscar') }}">
                    <i class="bi bi-search" aria-hidden="true"></i>
                </button>
            </form>
        </div>

        <nav class="exi-drawer-cats" aria-label="{{ __('Categorías') }}"></nav>

        <div class="exi-drawer-user">
            <a href="{{ route('favorites.index') }}" class="exi-drawer-fav">
                <i class="bi bi-heart" aria-hidden="true"></i>
                <span>{{ __('Mis favoritos') }}</span>
            </a>
            @auth
                <a href="{{ airoute('aimeos_shop_account') }}">
                    <i class="bi bi-person-circle" aria-hidden="true"></i>
                    <span>{{ __('Mi cuenta') }}</span>
                </a>
                <form method="POST" action="{{ airoute('logout') }}" class="m-0">
                    @csrf
                    <button type="submit">
                        <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                        <span>{{ __('Cerrar sesión') }}</span>
                    </button>
                </form>
            @else
                <a href="{{ airoute('login') }}">
                    <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i>
                    <span>{{ __('Iniciar sesión') }}</span>
                </a>
                @if (config('app.shop_registration'))
                    <a href="{{ airoute('register') }}">
                        <i class="bi bi-person-plus" aria-hidden="true"></i>
                        <span>{{ __('Crear cuenta') }}</span>
                    </a>
                @endif
            @endauth
        </div>

        <footer class="exi-drawer-footer">
            <div class="exi-drawer-locale">
                <a href="?locale=es&amp;currency=COP" class="{{ app()->getLocale() === 'es' ? 'active' : '' }}">ES</a>
                <a href="?locale=en&amp;currency=USD" class="{{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</a>
            </div>
            <p class="exi-drawer-copy">© {{ date('Y') }} Exicompras</p>
        </footer>
    </div>
</aside>
