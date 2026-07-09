@extends('shop::base')

@section('aimeos_header')
    <link type="text/css" rel="stylesheet" href="{{ asset('css/exifooter.css?v=' . ($_ver ?? '1')) }}">
@stop

@section('aimeos_body')
    @php
        $pageTitle = 'Política de Envíos';
        $pageSubtitle = 'Plazos, costos y cobertura de entregas a nivel nacional.';
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
                        <li><a href="#cobertura">1. Cobertura</a></li>
                        <li><a href="#costos">2. Costos de envío</a></li>
                        <li><a href="#plazos">3. Tiempos de entrega</a></li>
                        <li><a href="#recibir">4. ¿Cómo recibo mi pedido?</a></li>
                        <li><a href="#seguimiento">5. Seguimiento</a></li>
                        <li><a href="#novedad">6. Novedades en la entrega</a></li>
                        <li><a href="#restricciones">7. Restricciones</a></li>
                        <li><a href="#devoluciones">8. Envío por devoluciones</a></li>
                    </ol>
                </aside>

                <article class="exicom-legal__body">
                    <h2 id="cobertura">1. Cobertura</h2>
                    <p>Realizamos entregas en toda Colombia a través de transportadoras aliadas. Algunas zonas rurales o de difícil acceso pueden tener restricciones especiales; en tal caso, te informaremos antes de confirmar el pago.</p>

                    <h2 id="costos">2. Costos de envío</h2>
                    <ul>
                        <li>El costo se calcula al momento del checkout, según destino, peso y volumen del paquete.</li>
                        <li><strong>Envío gratis</strong> para pedidos superiores a <strong>$150.000 COP</strong> a ciudades capitales de departamento, excepto zonas especiales.</li>
                        <li>Cuando apliques un cupón de envío gratis, este se reflejará en el resumen del pedido.</li>
                    </ul>

                    <h2 id="plazos">3. Tiempos de entrega</h2>
                    <table class="exicom-legal__table">
                        <thead><tr><th>Tipo de destino</th><th>Plazo estimado (hábiles)</th></tr></thead>
                        <tbody>
                            <tr><td>Bogotá D.C. y ciudades principales</td><td>2 – 4 días</td></tr>
                            <tr><td>Otras capitales de departamento</td><td>3 – 6 días</td></tr>
                            <tr><td>Municipios y zonas rurales</td><td>5 – 10 días</td></tr>
                            <tr><td>San Andrés, Providencia y zonas especiales</td><td>7 – 15 días</td></tr>
                        </tbody>
                    </table>
                    <p>Los plazos empiezan a contar desde la <strong>confirmación del pago</strong>. Compras realizadas después de las 4:00 p.m. se gestionan al siguiente día hábil.</p>

                    <h2 id="recibir">4. ¿Cómo recibo mi pedido?</h2>
                    <ol>
                        <li>Tu pedido se entrega en la dirección indicada durante el checkout.</li>
                        <li>La transportadora realiza hasta <strong>dos intentos de entrega</strong>.</li>
                        <li>Si no se logra entregar, el paquete regresa a nuestro centro de distribución y se inicia el proceso de reembolso (ver <a href="{{ route('legal.cancelaciones') }}">Política de Cancelaciones</a>).</li>
                        <li>Para retirar en oficina de la transportadora, escoge ese modo de entrega en el checkout.</li>
                    </ol>

                    <h2 id="seguimiento">5. Seguimiento</h2>
                    <p>Cuando despachemos tu pedido recibirás un correo con el número de guía y un enlace para seguirlo en tiempo real. También puedes consultarlo en tu cuenta, sección <em>Mis pedidos</em>.</p>

                    <h2 id="novedad">6. Novedades en la entrega</h2>
                    <p>Si ocurre algún retraso o novedad con tu envío (dirección incorrecta, destinatario ausente, dirección inaccesible, fenómenos climáticos, etc.), te notificaremos por correo electrónico. Casos de fuerza mayor pueden afectar los plazos.</p>

                    <h2 id="restricciones">7. Restricciones</h2>
                    <ul>
                        <li>No se realizan entregas a apartados aéreos ni a países fuera de Colombia.</li>
                        <li>Para entregas en conjuntos residenciales puede requerirse autorización de portería.</li>
                        <li>Productos con contenido frágil o voluminoso pueden requerir coordinación especial.</li>
                    </ul>

                    <h2 id="devoluciones">8. Envío por devoluciones</h2>
                    <p>Cuando la devolución sea atribuible a ti, los costos de envío de retorno corren por tu cuenta. Cuando sea atribuible al Vendedor (defecto, error de envío), Exicompras asume el costo.</p>

                    <hr class="exicom-legal__sep">

                    <p class="exicom-legal__closing"><strong>¿Dudas con un envío?</strong> Escríbenos a <a href="mailto:envios@exicompras.com">envios@exicompras.com</a> indicando el número de pedido.</p>
                </article>
            </div>
        </div>
    </section>
@stop
