@extends('shop::base')

@section('aimeos_header')
	<?= $aiheader['locale/select'] ?? '' ?>
	<?= $aiheader['basket/mini'] ?? '' ?>
	<?= $aiheader['catalog/search'] ?? '' ?>
	<?= $aiheader['catalog/tree'] ?? '' ?>
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

@section('aimeos_body')
@php
use Illuminate\Support\Facades\DB;

$_site       = app('aimeos.context')->get()->locale()->getSiteItem();
$_presets    = config('shop.client.html.theme-presets.default', []);
$_overrides  = $_site->getConfigValue('theme/default', []);
$themeVars   = array_merge($_presets ?? [], $_overrides ?? []);
$primary   = $themeVars['--ai-primary']   ?? '#1A1F36';
$secondary = $themeVars['--ai-secondary'] ?? '#FF6B35';
$tertiary  = $themeVars['--ai-tertiary']  ?? '#4A7EFF';
$bg        = $themeVars['--ai-bg']        ?? '#E3E7EB';
$bgAlt     = $themeVars['--ai-bg-alt']    ?? '#FFFFFF';

// Fetch products with price and category
$products = DB::table('mshop_product as p')
    ->join('mshop_product_list as pl_p', function($j) {
        $j->on('pl_p.parentid','=','p.id')->where('pl_p.domain','price');
    })
    ->join('mshop_price as pr', 'pr.id', '=', 'pl_p.refid')
    ->join('mshop_product_list as pl_c', function($j) {
        $j->on('pl_c.parentid','=','p.id')->where('pl_c.domain','catalog');
    })
    ->join('mshop_catalog as c', 'c.id', '=', 'pl_c.refid')
    ->leftJoin('mshop_product_list as pl_t', function($j) {
        $j->on('pl_t.parentid','=','p.id')->where('pl_t.domain','text');
    })
    ->leftJoin('mshop_text as t', function($j) {
        $j->on('t.id','=','pl_t.refid')->where('t.type','short');
    })
    ->where('p.status', 1)
    ->select('p.id','p.code','p.label','pr.value as price','c.id as cat_id','c.label as cat_label','c.code as cat_code','t.content as short_text')
    ->groupBy('p.id','p.code','p.label','pr.value','c.id','c.label','c.code','t.content')
    ->orderBy('c.id')->orderBy('p.id')
    ->get();

$byCategory = $products->groupBy('cat_id');

// Resolver IDs reales de las categorias especiales (pueden no ser 7/8 si ya hay otras)
$_specialIds = DB::table('mshop_catalog')
    ->where('siteid', '1.')
    ->whereIn('code', ['influencers', 'coleccionistas', 'artistas'])
    ->pluck('id', 'code');
$_influencersId     = $_specialIds['influencers']     ?? null;
$_coleccionistasId  = $_specialIds['coleccionistas']  ?? null;
$_artistasId        = $_specialIds['artistas']        ?? null;

$categories = [];
if ($_influencersId) {
    $categories[$_influencersId] = ['label'=>'Influencers', 'code'=>'influencers', 'icon'=>'⭐', 'emoji'=>'🌟', 'color'=>'#FFB400', 'special'=>'influencer'];
}
$categories[2] = ['label'=>'Electrónica',        'code'=>'electronica',  'icon'=>'🖥️',  'emoji'=>'⚡', 'color'=>'#4a7eff'];
$categories[3] = ['label'=>'Ropa y Accesorios',  'code'=>'ropa-y-accesorios','icon'=>'👗','emoji'=>'✨', 'color'=>'#ff6b35'];
$categories[4] = ['label'=>'Hogar y Decoración', 'code'=>'hogar-y-decoracion','icon'=>'🏠','emoji'=>'🏡','color'=>'#38a169'];
$categories[5] = ['label'=>'Deportes y Fitness', 'code'=>'deportes-y-fitness','icon'=>'🏋️','emoji'=>'💪','color'=>'#d69e2e'];
$categories[6] = ['label'=>'Belleza y Cuidado',  'code'=>'belleza-y-cuidado','icon'=>'💄','emoji'=>'🌸','color'=>'#e53e3e'];
if ($_artistasId) {
    $categories[$_artistasId] = ['label'=>'Artistas', 'code'=>'artistas', 'icon'=>'🎤', 'emoji'=>'🎶', 'color'=>'#E91E63', 'special'=>'artista'];
}
if ($_coleccionistasId) {
    $categories[$_coleccionistasId] = ['label'=>'Coleccionistas', 'code'=>'coleccionistas', 'icon'=>'🏆', 'emoji'=>'🎖️', 'color'=>'#9B59B6'];
}

function fmtCOP($val) {
    return '$' . number_format((float)$val, 0, ',', '.');
}

// Fake discount % for marketing effect (20-40%)
function fakeOldPrice($price) {
    $pct = [20,25,30,35,40][crc32($price) % 5];
    return (float)$price * (100 / (100 - $pct));
}
@endphp

{{-- ═══════════════════════════════════════════════════════════
     HERO BANNER — slider estilo MercadoLibre
════════════════════════════════════════════════════════════════ --}}
<section class="exihome-hero">
    <div class="exihero-slider" id="heroSlider">

        {{-- Slide 1 — imagen banner-1.webp --}}
        <div class="exihero-slide active exihero-slide--image" style="padding: 0;">
            <img
                src="{{ asset('images/banner-1.webp') }}"
                srcset="{{ asset('images/banner-1.webp') }} 1584w"
                sizes="(max-width: 768px) 100vw, (max-width: 1400px) 100vw, 1400px"
                alt="Repara y renueva tu piel con nuestra crema premium"
                width="1584"
                height="672"
                loading="eager"
                fetchpriority="high"
                decoding="async"
                class="exihero-img"
            >
        </div>

        {{-- Slide 2 --}}
        <div class="exihero-slide" style="background: linear-gradient(135deg, #1a0a2e 0%, #2d1456 50%, #1a0a2e 100%);">
            <div class="exihero-content">
                <div class="exihero-text">
                    <span class="exihero-badge" style="background:{{ $secondary }}">🌸 Nueva temporada</span>
                    <h1>Moda &amp;<br><span style="color:#ff8fab">Accesorios</span></h1>
                    <p>Descubre las últimas tendencias en ropa y accesorios de moda</p>
                    <a href="{{ route('aimeos_shop_tree', ['f_name'=>'ropa-y-accesorios', 'f_catid'=>3]) }}" class="exihero-btn" style="background:{{ $secondary }}">
                        Ver Moda →
                    </a>
                </div>
                <div class="exihero-visual">
                    <div class="exihero-mockup fashion">
                        <div class="mock-garment">👗</div>
                        <div class="mock-badge" style="background:#ff8fab; color:#1a0a2e">NUEVO</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Slide 3 --}}
        <div class="exihero-slide" style="background: linear-gradient(135deg, #0a1f0f 0%, #163d1c 50%, #0a2812 100%);">
            <div class="exihero-content">
                <div class="exihero-text">
                    <span class="exihero-badge" style="background:#38a169">🏠 Hogar ideal</span>
                    <h1>Tu hogar,<br><span style="color:#68d391">tu estilo</span></h1>
                    <p>Decora y equipa cada rincón de tu hogar con los mejores productos</p>
                    <a href="{{ route('aimeos_shop_tree', ['f_name'=>'hogar-y-decoracion', 'f_catid'=>4]) }}" class="exihero-btn" style="background:#38a169">
                        Ver Hogar →
                    </a>
                </div>
                <div class="exihero-visual">
                    <div class="exihero-mockup home">
                        <div class="mock-house">🏡</div>
                        <div class="mock-badge" style="background:#38a169">HOT</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Controles del slider --}}
    <button class="exihero-prev" onclick="heroSlide(-1)" aria-label="Anterior slide">&#8592;</button>
    <button class="exihero-next" onclick="heroSlide(1)" aria-label="Siguiente slide">&#8594;</button>
    <div class="exihero-dots" role="group" aria-label="Navegación de slides">
        <button class="exidot active" onclick="heroGoTo(0)" aria-label="Slide 1" aria-pressed="true" type="button"></button>
        <button class="exidot" onclick="heroGoTo(1)" aria-label="Slide 2" aria-pressed="false" type="button"></button>
        <button class="exidot" onclick="heroGoTo(2)" aria-label="Slide 3" aria-pressed="false" type="button"></button>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     BARRA DE CONFIANZA — reinterpretación premium minimalista
════════════════════════════════════════════════════════════════ --}}
<section class="exihome-trust">
    <div class="exitrust-rail">
        <div class="exitrust-item">
            <div class="exitrust-mark">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 7h11v8H3z"/><path d="M14 10h4l3 3v2h-7"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/>
                </svg>
            </div>
            <div class="exitrust-text">
                <strong>Envío gratis desde $150.000</strong>
                <small>Cobertura nacional 2 a 5 días hábiles</small>
            </div>
        </div>

        <div class="exitrust-divider" aria-hidden="true"></div>

        <div class="exitrust-item">
            <div class="exitrust-mark">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="3" y="6" width="18" height="13" rx="2"/><path d="M3 10h18"/><path d="M7 15h4"/>
                </svg>
            </div>
            <div class="exitrust-text">
                <strong>Paga como prefieras</strong>
                <small>PSE, Nequi, Daviplata y tarjetas</small>
            </div>
        </div>

        <div class="exitrust-divider" aria-hidden="true"></div>

        <div class="exitrust-item">
            <div class="exitrust-mark">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M21 12a9 9 0 1 1-3-6.7"/><path d="M21 4v5h-5"/>
                </svg>
            </div>
            <div class="exitrust-text">
                <strong>Cambios sin complicaciones</strong>
                <small>30 días para arrepentirte, sin preguntas</small>
            </div>
        </div>

        <div class="exitrust-divider" aria-hidden="true"></div>

        <div class="exitrust-item">
            <div class="exitrust-mark">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 3l8 4v5c0 5-3.5 8-8 9-4.5-1-8-4-8-9V7l8-4z"/><path d="M9 12l2 2 4-4"/>
                </svg>
            </div>
            <div class="exitrust-text">
                <strong>Compra siempre protegida</strong>
                <small>Productos originales con garantía oficial</small>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     CATEGORÍAS — grid tipo MercadoLibre
════════════════════════════════════════════════════════════════ --}}
<section class="exihome-section">
    <div class="exisection-header">
        <h2>Explorar por categoría</h2>
        <a href="{{ route('aimeos_shop_list') }}" class="exisee-all">Ver todo →</a>
    </div>
    <div class="exicat-grid">
        @foreach($categories as $catId => $cat)
        <a href="{{ route('aimeos_shop_tree', ['f_name'=>$cat['code'], 'f_catid'=>$catId]) }}"
           @class([
               'exicat-card',
               'exicat-card--' . ($cat['special'] ?? '') => !empty($cat['special']),
           ])>
            @if(($cat['special'] ?? null) === 'influencer')
                <span class="exicat-special-badge" aria-label="Categoria especial Influencers">INFLUENCERS</span>
            @elseif(($cat['special'] ?? null) === 'artista')
                <span class="exicat-special-badge exicat-special-badge--artista" aria-label="Categoria especial Artistas">ARTISTAS</span>
            @endif
            <div class="exicat-icon" style="background: {{ $cat['color'] }}22; border-color: {{ $cat['color'] }}44;">
                <span class="exicat-emoji">{{ $cat['icon'] }}</span>
            </div>
            <span class="exicat-name">{{ $cat['label'] }}</span>
            <span class="exicat-count">{{ $byCategory->get($catId, collect())->count() }} productos</span>
        </a>
        @endforeach
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     OFERTAS DESTACADAS — grid de productos tipo Amazon
════════════════════════════════════════════════════════════════ --}}
<section class="exihome-section">
    <div class="exisection-header">
        <h2>🔥 Ofertas destacadas</h2>
        <a href="{{ route('aimeos_shop_list') }}" class="exisee-all">Ver todas →</a>
    </div>
    <div class="exiprod-grid">
        @foreach($products->take(8) as $prod)
        @php
            $oldPrice = fakeOldPrice($prod->price);
            $discount = round(100 - ($prod->price / $oldPrice * 100));
            $prodName = urlencode(strtolower(str_replace(' ', '-', $prod->label)));
        @endphp
        <a href="{{ route('aimeos_shop_detail', ['d_name' => $prodName, 'd_pos' => 0, 'd_prodid' => $prod->id]) }}" class="exiprod-card">
            <div class="exiprod-img-wrap">
                <div class="exiprod-img-placeholder">
                    @php
                        $emojis = ['🖥️'=>[2],'📱'=>[2],'🎧'=>[2],'👗'=>[3],'👖'=>[3],'👜'=>[3],'☕'=>[4],'💡'=>[4],'👟'=>[5],'🧘'=>[5],'💆'=>[6],'💇'=>[6],'⭐'=>[$_influencersId],'🌟'=>[$_influencersId],'🏆'=>[$_coleccionistasId],'🎖️'=>[$_coleccionistasId],'🎤'=>[$_artistasId],'🎶'=>[$_artistasId],'🎸'=>[$_artistasId],'🎬'=>[$_artistasId]];
                        $catEmoji = '🛒';
                        foreach($emojis as $em => $cats) { if(in_array($prod->cat_id, $cats)) { $catEmoji = $em; break; } }
                    @endphp
                    <span class="exiprod-emoji">{{ $catEmoji }}</span>
                </div>
                <span class="exiprod-badge">-{{ $discount }}%</span>
            </div>
            <div class="exiprod-info">
                <span class="exiprod-cat">{{ $prod->cat_label }}</span>
                <h3 class="exiprod-name">{{ $prod->label }}</h3>
                @if($prod->short_text)
                <p class="exiprod-short">{{ Str::limit($prod->short_text, 60) }}</p>
                @endif
                <div class="exiprod-pricing">
                    <span class="exiprod-price">{{ fmtCOP($prod->price) }}</span>
                    <span class="exiprod-old">{{ fmtCOP($oldPrice) }}</span>
                </div>
                <div class="exiprod-stars">⭐⭐⭐⭐⭐ <small>({{ rand(12,98) }})</small></div>
                <div class="exiprod-ship">🚚 Envío gratis</div>
            </div>
        </a>
        @endforeach
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     BANNER DOBLE — tipo MercadoLibre mid-page
════════════════════════════════════════════════════════════════ --}}
<section class="exihome-banners">
    <a href="{{ route('aimeos_shop_tree', ['f_name'=>'deportes-y-fitness', 'f_catid'=>5]) }}" class="exibanner-card" style="background: linear-gradient(135deg, #2a0a1a 0%, #4a1430 100%);">
        <div class="exibanner-text">
            <span class="exibanner-tag">💪 Actívate</span>
            <h3>Deportes<br>&amp; Fitness</h3>
            <p>Equipate para alcanzar tus metas</p>
            <span class="exibanner-cta">Comprar ahora →</span>
        </div>
        <div class="exibanner-emoji">🏋️</div>
    </a>
    <a href="{{ route('aimeos_shop_tree', ['f_name'=>'belleza-y-cuidado', 'f_catid'=>6]) }}" class="exibanner-card" style="background: linear-gradient(135deg, #2a0a1a 0%, #4a1430 100%);">
        <div class="exibanner-text">
            <span class="exibanner-tag" style="background:#e53e3e">🌸 Cuídate</span>
            <h3>Belleza<br>&amp; Cuidado</h3>
            <p>Los mejores productos para tu rutina</p>
            <span class="exibanner-cta" style="color:#ff8fab">Explorar →</span>
        </div>
        <div class="exibanner-emoji">💄</div>
    </a>
</section>

{{-- ═══════════════════════════════════════════════════════════
     TODOS LOS PRODUCTOS — por categoría
════════════════════════════════════════════════════════════════ --}}
@foreach($categories as $catId => $cat)
@if($byCategory->has($catId))
<section class="exihome-section">
    <div class="exisection-header">
        <h2><span style="color:{{ $cat['color'] }}">{{ $cat['icon'] }}</span> {{ $cat['label'] }}</h2>
        <a href="{{ route('aimeos_shop_tree', ['f_name'=>$cat['code'], 'f_catid'=>$catId]) }}" class="exisee-all">Ver todo →</a>
    </div>
    <div class="exiprod-row">
        @foreach($byCategory->get($catId) as $prod)
        @php
            $oldPrice = fakeOldPrice($prod->price);
            $discount = round(100 - ($prod->price / $oldPrice * 100));
            $prodName = urlencode(strtolower(str_replace(' ', '-', $prod->label)));
        @endphp
        <a href="{{ route('aimeos_shop_detail', ['d_name' => $prodName, 'd_pos' => 0, 'd_prodid' => $prod->id]) }}" class="exiprod-card exiprod-card--sm">
            <div class="exiprod-img-wrap">
                <div class="exiprod-img-placeholder">
                    <span class="exiprod-emoji">{{ $cat['icon'] }}</span>
                </div>
                <span class="exiprod-badge">-{{ $discount }}%</span>
            </div>
            <div class="exiprod-info">
                <h3 class="exiprod-name">{{ $prod->label }}</h3>
                <div class="exiprod-pricing">
                    <span class="exiprod-price">{{ fmtCOP($prod->price) }}</span>
                    <span class="exiprod-old">{{ fmtCOP($oldPrice) }}</span>
                </div>
                <div class="exiprod-stars">⭐⭐⭐⭐⭐</div>
                <div class="exiprod-ship">🚚 Envío gratis</div>
            </div>
        </a>
        @endforeach

        {{-- Ver más de esta categoría --}}
        <a href="{{ route('aimeos_shop_tree', ['f_name'=>$cat['code'], 'f_catid'=>$catId]) }}" class="exiprod-card exiprod-card--more">
            <div class="eximore-inner">
                <span style="font-size:2.5rem">{{ $cat['emoji'] ?? $cat['icon'] }}</span>
                <span>Ver más<br>{{ $cat['label'] }}</span>
                <span style="font-size:1.5rem">→</span>
            </div>
        </a>
    </div>
</section>
@endif
@endforeach

{{-- ═══════════════════════════════════════════════════════════
     BANNER FINAL CTA — Newsletter / App
════════════════════════════════════════════════════════════════ --}}
<section class="exihome-cta">
    <div class="exicta-inner">
        <div class="exicta-text">
            <h2>🎁 ¿Primera compra?</h2>
            <p>Regístrate y obtén <strong style="color:{{ $secondary }}">10% de descuento</strong> en tu primer pedido. ¡Más de 10.000 colombianos ya confían en Exicompras!</p>
            @guest
            <div class="exicta-btns">
                <a href="{{ route('register') }}" class="exicta-btn-primary">Crear cuenta gratis</a>
                <a href="{{ route('login') }}" class="exicta-btn-secondary">Ya tengo cuenta</a>
            </div>
            @else
            <a href="{{ route('aimeos_shop_list') }}" class="exicta-btn-primary">Explorar tienda →</a>
            @endguest
        </div>
        <div class="exicta-badges">
            <div class="exicta-stat"><span>10K+</span><small>Clientes</small></div>
            <div class="exicta-stat"><span>5★</span><small>Calificación</small></div>
            <div class="exicta-stat"><span>24h</span><small>Despacho</small></div>
            <div class="exicta-stat"><span>🔒</span><small>Seguro</small></div>
        </div>
    </div>
</section>

{{-- Scripts del hero slider con soporte táctil (swipe) --}}
<script>
(function() {
    var slider  = document.getElementById('heroSlider');
    var slides  = document.querySelectorAll('.exihero-slide');
    var dots    = document.querySelectorAll('.exidot');
    var current = 0;
    var timer;
    var touchStartX = 0;
    var touchStartY = 0;
    var tracking    = false;

    function goTo(n) {
        slides[current].classList.remove('active');
        dots[current].classList.remove('active');
        dots[current].setAttribute('aria-pressed', 'false');
        current = (n + slides.length) % slides.length;
        slides[current].classList.add('active');
        dots[current].classList.add('active');
        dots[current].setAttribute('aria-pressed', 'true');
    }

    window.heroSlide = function(dir) { clearInterval(timer); goTo(current + dir); startAuto(); };
    window.heroGoTo  = function(n)   { clearInterval(timer); goTo(n); startAuto(); };

    /* Soporte swipe táctil.
       touch-action: pan-y en el CSS deja el scroll vertical al navegador,
       así que aquí solo procesamos movimiento horizontal. */
    slider.addEventListener('touchstart', function(e) {
        var t = e.changedTouches[0];
        touchStartX = t.screenX;
        touchStartY = t.screenY;
        tracking    = true;
    }, { passive: true });

    slider.addEventListener('touchmove', function(e) {
        if (!tracking) return;
        var t = e.changedTouches[0];
        var dx = Math.abs(t.screenX - touchStartX);
        var dy = Math.abs(t.screenY - touchStartY);
        /* Si el usuario se mueve claramente en vertical, soltamos el tracking
           para que el scroll de la pagina fluya sin interference */
        if (dy > dx * 1.5) {
            tracking = false;
        }
    }, { passive: true });

    slider.addEventListener('touchend', function(e) {
        if (!tracking) return;
        tracking = false;
        var dx = e.changedTouches[0].screenX - touchStartX;
        /* Threshold 30px: suficientemente permisivo en móvil sin disparar falsos positivos */
        if (Math.abs(dx) > 30) {
            clearInterval(timer);
            goTo(current + (dx < 0 ? 1 : -1));
            startAuto();
        }
    }, { passive: true });

    slider.addEventListener('touchcancel', function() { tracking = false; }, { passive: true });

    /* Pausa al hover (desktop) */
    slider.addEventListener('mouseenter', function() { clearInterval(timer); });
    slider.addEventListener('mouseleave', startAuto);

    function startAuto() {
        timer = setInterval(function() { goTo(current + 1); }, 5000);
    }
    startAuto();
})();
</script>
@stop
