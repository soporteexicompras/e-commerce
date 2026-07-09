@extends('shop::base')

@section('aimeos_header')
    <link type="text/css" rel="stylesheet" href="{{ asset('css/exifooter.css?v=' . ($_ver ?? '1')) }}">
@stop

@section('aimeos_body')
    @php
        $pageTitle = 'Sobre nosotros';
        $pageSubtitle = 'Quiénes somos, qué nos mueve y cómo construimos Exicompras.';
    @endphp

    <section class="exicom-legal">
        <div class="exicom-legal__container">
            <nav class="exicom-legal__breadcrumb" aria-label="Navegación">
                <a href="{{ route('aimeos_home') }}" class="exicom-legal__crumb">Inicio</a>
                <span class="exicom-legal__crumb-sep" aria-hidden="true">›</span>
                <span class="exicom-legal__crumb is-current">{{ $pageTitle }}</span>
            </nav>

            <header class="exicom-legal__hero">
                <span class="exicom-legal__kicker">Información legal</span>
                <h1 class="exicom-legal__title">{{ $pageTitle }}</h1>
                <p class="exicom-legal__subtitle">{{ $pageSubtitle }}</p>
                <p class="exicom-legal__updated">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    <span>Última actualización: 8 de julio de 2026</span>
                </p>
            </header>

            <div class="exicom-legal__layout">
                <aside class="exicom-legal__sidebar" aria-label="Índice">
                    <p class="exicom-legal__sidebar-title">En esta página</p>
                    <ol class="exicom-legal__toc">
                        <li><a href="#historia">1. Nuestra historia</a></li>
                        <li><a href="#mision">2. Misión</a></li>
                        <li><a href="#vision">3. Visión</a></li>
                        <li><a href="#valores">4. Valores</a></li>
                        <li><a href="#modelo">5. Modelo marketplace</a></li>
                        <li><a href="#compromisos">6. Nuestros compromisos</a></li>
                        <li><a href="#legal">7. Datos de la compañía</a></li>
                    </ol>
                </aside>

                <article class="exicom-legal__body">
                    <h2 id="historia">1. Nuestra historia</h2>
                    <p><strong>Exicompras</strong> nació con la convicción de que comprar productos únicos en Colombia no tiene por qué ser complicado, caro o inseguro. Reunimos vendedores independientes y tiendas seleccionadas en un único marketplace que ofrece pagos seguros, envíos trazables y atención al cliente de verdad.</p>

                    <h2 id="mision">2. Misión</h2>
                    <p>Conectar a compradores colombianos con vendedores que ofrecen productos auténticos, a través de una plataforma confiable, transparente y fácil de usar.</p>

                    <h2 id="vision">3. Visión</h2>
                    <p>Ser el marketplace de referencia en Colombia para descubrir y comprar productos únicos, apoyando el crecimiento de vendedores locales en toda la geografía nacional.</p>

                    <h2 id="valores">4. Valores</h2>
                    <ul>
                        <li><strong>Transparencia:</strong> precios claros, sin sorpresas, condiciones visibles antes de pagar.</li>
                        <li><strong>Confianza:</strong> vendedores verificados, pagos seguros, devoluciones garantizadas.</li>
                        <li><strong>Cercanía:</strong> atención humana, respuesta rápida, escucha activa a clientes y vendedores.</li>
                        <li><strong>Equidad:</strong> sin favoritismos ni sesgos por tamaño del vendedor.</li>
                        <li><strong>Compromiso local:</strong> preferimos vendedores y productores colombianos.</li>
                    </ul>

                    <h2 id="modelo">5. Modelo marketplace</h2>
                    <p>Exicompras es un marketplace <em>multi-vendor</em>. Cada Vendedor es responsable directo de su producto (precio, stock, descripción, calidad, envío). Exicompras aporta:</p>
                    <ul>
                        <li>Plataforma de publicación y descubrimiento.</li>
                        <li>Pasarela de pagos con conciliación periódica.</li>
                        <li>Servicio al cliente y mediación de PQR.</li>
                        <li>Logística cuando aplica.</li>
                        <li>Protección al consumidor bajo el Estatuto del Consumidor.</li>
                    </ul>

                    <h2 id="compromisos">6. Nuestros compromisos</h2>
                    <ol>
                        <li><strong>Pago seguro:</strong> todas las transacciones se procesan por pasarelas certificadas PCI-DSS.</li>
                        <li><strong>Derecho de retracto:</strong> cumplimiento estricto del artículo 47 de la Ley 1480 de 2011.</li>
                        <li><strong>Protección de datos:</strong> cumplimiento de la Ley 1581 de 2012.</li>
                        <li><strong>Transparencia legal:</strong> este sitio publica sus términos, política de privacidad y políticas operativas.</li>
                        <li><strong>Libro de Reclamaciones Virtual</strong> conforme al Decreto 735 de 2013.</li>
                    </ol>

                    <h2 id="legal">7. Datos de la compañía</h2>
                    <table class="exicom-legal__table">
                        <tbody>
                            <tr><th>Razón social</th><td>Exicompras S.A.S.</td></tr>
                            <tr><th>NIT</th><td>900.000.000-0</td></tr>
                            <tr><th>Tipo de sociedad</th><td>Sociedad por Acciones Simplificada</td></tr>
                            <tr><th>Domicilio principal</th><td>Carrera 1 # 2-3, Bogotá D.C., Colombia</td></tr>
                            <tr><th>Matrícula mercantil</th><td>Registrada en la Cámara de Comercio de Bogotá D.C.</td></tr>
                            <tr><th>Representante legal</th><td>(Se actualizará en el registro mercantil)</td></tr>
                            <tr><th>Fecha de constitución</th><td>(Se actualizará en el registro mercantil)</td></tr>
                        </tbody>
                    </table>

                    <hr class="exicom-legal__sep">

                    <p class="exicom-legal__closing">¿Quieres ser vendedor en Exicompras? Escríbenos a <a href="mailto:vendedores@exicompras.com">vendedores@exicompras.com</a>.</p>
                </article>
            </div>
        </div>
    </section>
@stop
