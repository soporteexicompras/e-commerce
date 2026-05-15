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

$themeVars = app('aimeos.context')->get()->locale()->getSiteItem()->getConfigValue('theme/default', []);
$primary   = $themeVars['--ai-primary']   ?? '#f0f2ff';
$secondary = $themeVars['--ai-secondary'] ?? '#ff6b35';
$tertiary  = $themeVars['--ai-tertiary']  ?? '#4a7eff';
$bg        = $themeVars['--ai-bg']        ?? '#1a1f36';
$bgAlt     = $themeVars['--ai-bg-alt']    ?? '#141828';

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

$categories = [
    2 => ['label'=>'Electrónica',        'code'=>'electronica',  'icon'=>'🖥️',  'emoji'=>'⚡', 'color'=>'#4a7eff'],
    3 => ['label'=>'Ropa y Accesorios',  'code'=>'ropa-y-accesorios','icon'=>'👗','emoji'=>'✨', 'color'=>'#ff6b35'],
    4 => ['label'=>'Hogar y Decoración', 'code'=>'hogar-y-decoracion','icon'=>'🏠','emoji'=>'🏡','color'=>'#38a169'],
    5 => ['label'=>'Deportes y Fitness', 'code'=>'deportes-y-fitness','icon'=>'🏋️','emoji'=>'💪','color'=>'#d69e2e'],
    6 => ['label'=>'Belleza y Cuidado',  'code'=>'belleza-y-cuidado','icon'=>'💄','emoji'=>'🌸','color'=>'#e53e3e'],
];

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

        {{-- Slide 1 --}}
        <div class="exihero-slide active" style="background: linear-gradient(135deg, #0f1629 0%, #1a2744 50%, #0d1f4a 100%);">
            <div class="exihero-content">
                <div class="exihero-text">
                    <span class="exihero-badge">🔥 Oferta del día</span>
                    <h1>Tecnología<br><span style="color:{{ $tertiary }}">al mejor precio</span></h1>
                    <p>Smartphones, tablets y accesorios con envío rápido a toda Colombia</p>
                    <a href="{{ route('aimeos_shop_tree', ['f_name'=>'electronica', 'f_catid'=>2]) }}" class="exihero-btn">
                        Ver Electrónica →
                    </a>
                </div>
                <div class="exihero-visual">
                    <div class="exihero-mockup tech">
                        <div class="mock-phone">
                            <div class="mock-screen">📱</div>
                        </div>
                        <div class="mock-badge" style="background:{{ $secondary }}">-30% OFF</div>
                    </div>
                </div>
            </div>
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
    <div class="exihero-dots" role="tablist" aria-label="Slides">
        <span class="exidot active" onclick="heroGoTo(0)" role="tab" aria-label="Slide 1" tabindex="0"></span>
        <span class="exidot" onclick="heroGoTo(1)" role="tab" aria-label="Slide 2" tabindex="0"></span>
        <span class="exidot" onclick="heroGoTo(2)" role="tab" aria-label="Slide 3" tabindex="0"></span>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     BARRA DE CONFIANZA — tipo Amazon
════════════════════════════════════════════════════════════════ --}}
<section class="exihome-trust">
    <div class="exitrust-grid">
        <div class="exitrust-item">
            <span class="exitrust-icon">🚚</span>
            <div>
                <strong>Envío Gratis</strong>
                <small>En compras +$150.000</small>
            </div>
        </div>
        <div class="exitrust-item">
            <span class="exitrust-icon">🔒</span>
            <div>
                <strong>Pago Seguro</strong>
                <small>Tus datos protegidos</small>
            </div>
        </div>
        <div class="exitrust-item">
            <span class="exitrust-icon">↩️</span>
            <div>
                <strong>Devoluciones</strong>
                <small>30 días sin preguntas</small>
            </div>
        </div>
        <div class="exitrust-item">
            <span class="exitrust-icon">🎧</span>
            <div>
                <strong>Soporte 24/7</strong>
                <small>Estamos para ayudarte</small>
            </div>
        </div>
        <div class="exitrust-item">
            <span class="exitrust-icon">⭐</span>
            <div>
                <strong>+10.000 Clientes</strong>
                <small>Satisfechos en Colombia</small>
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
        <a href="{{ route('aimeos_shop_tree', ['f_name'=>$cat['code'], 'f_catid'=>$catId]) }}" class="exicat-card">
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
                        $emojis = ['🖥️'=>[2],'📱'=>[2],'🎧'=>[2],'👗'=>[3],'👖'=>[3],'👜'=>[3],'☕'=>[4],'💡'=>[4],'👟'=>[5],'🧘'=>[5],'💆'=>[6],'💇'=>[6]];
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
    <a href="{{ route('aimeos_shop_tree', ['f_name'=>'deportes-y-fitness', 'f_catid'=>5]) }}" class="exibanner-card" style="background: linear-gradient(135deg, #1a2a0a 0%, #2d4a14 100%);">
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
                <span style="font-size:2.5rem">{{ $cat['emoji'] }}</span>
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
    var touchDiff   = 0;

    function goTo(n) {
        slides[current].classList.remove('active');
        dots[current].classList.remove('active');
        current = (n + slides.length) % slides.length;
        slides[current].classList.add('active');
        dots[current].classList.add('active');
    }

    window.heroSlide = function(dir) { clearInterval(timer); goTo(current + dir); startAuto(); };
    window.heroGoTo  = function(n)   { clearInterval(timer); goTo(n); startAuto(); };

    /* Soporte swipe táctil */
    slider.addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });
    slider.addEventListener('touchend', function(e) {
        touchDiff = e.changedTouches[0].screenX - touchStartX;
        if (Math.abs(touchDiff) > 40) {
            clearInterval(timer);
            goTo(current + (touchDiff < 0 ? 1 : -1));
            startAuto();
        }
    }, { passive: true });

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
