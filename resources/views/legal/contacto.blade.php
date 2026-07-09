@extends('shop::base')

@section('aimeos_header')
    <link type="text/css" rel="stylesheet" href="{{ asset('css/exifooter.css?v=' . ($_ver ?? '1')) }}">
@stop

@section('aimeos_body')
    @php
        $pageTitle = 'Contáctanos';
        $pageSubtitle = 'Estamos para ayudarte. Elige el canal que prefieras y te responderemos lo antes posible.';
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
                        <li><a href="#canales">1. Canales de atención</a></li>
                        <li><a href="#horarios">2. Horarios</a></li>
                        <li><a href="#empresa">3. Sobre Exicompras</a></li>
                        <li><a href="#formulario">4. Formulario de contacto</a></li>
                    </ol>
                </aside>

                <article class="exicom-legal__body">
                    <h2 id="canales">1. Canales de atención</h2>
                    <div class="exicom-legal__cards">
                        <div class="exicom-legal__card">
                            <span class="exicom-legal__card-icon" aria-hidden="true">✉️</span>
                            <h3>Atención general</h3>
                            <p><a href="mailto:atencion@exicompras.com">atencion@exicompras.com</a></p>
                        </div>
                        <div class="exicom-legal__card">
                            <span class="exicom-legal__card-icon" aria-hidden="true">📦</span>
                            <h3>Pedidos y envíos</h3>
                            <p><a href="mailto:pedidos@exicompras.com">pedidos@exicompras.com</a></p>
                        </div>
                        <div class="exicom-legal__card">
                            <span class="exicom-legal__card-icon" aria-hidden="true">↩️</span>
                            <h3>Retracto y devoluciones</h3>
                            <p><a href="mailto:retracto@exicompras.com">retracto@exicompras.com</a></p>
                        </div>
                        <div class="exicom-legal__card">
                            <span class="exicom-legal__card-icon" aria-hidden="true">🛡️</span>
                            <h3>Garantía legal</h3>
                            <p><a href="mailto:garantia@exicompras.com">garantia@exicompras.com</a></p>
                        </div>
                        <div class="exicom-legal__card">
                            <span class="exicom-legal__card-icon" aria-hidden="true">🔐</span>
                            <h3>Datos personales</h3>
                            <p><a href="mailto:datospersonales@exicompras.com">datospersonales@exicompras.com</a></p>
                        </div>
                        <div class="exicom-legal__card">
                            <span class="exicom-legal__card-icon" aria-hidden="true">⚖️</span>
                            <h3>Legal y vendedores</h3>
                            <p><a href="mailto:legal@exicompras.com">legal@exicompras.com</a></p>
                        </div>
                    </div>

                    <h2 id="horarios">2. Horarios</h2>
                    <ul>
                        <li><strong>Atención al cliente:</strong> lunes a viernes, 8:00 a.m. a 6:00 p.m. (hora Colombia).</li>
                        <li><strong>Pedidos en línea:</strong> 24/7 — los pagos y pedidos se procesan automáticamente.</li>
                        <li><strong>Tiempo de respuesta esperado:</strong> entre 1 y 24 horas hábiles dependiendo del canal.</li>
                    </ul>

                    <h2 id="empresa">3. Sobre Exicompras</h2>
                    <ul>
                        <li><strong>Razón social:</strong> Exicompras S.A.S.</li>
                        <li><strong>NIT:</strong> 900.000.000-0</li>
                        <li><strong>Domicilio:</strong> Carrera 1 # 2-3, Bogotá D.C., Colombia</li>
                        <li><strong>Teléfono principal:</strong> +57 (1) 000 0000</li>
                        <li><strong>Cámara de comercio:</strong> Bogotá D.C. — matrícula mercantil vigente</li>
                    </ul>

                    <h2 id="formulario">4. Formulario de contacto</h2>
                    <form class="exicom-legal__form" onsubmit="event.preventDefault(); document.getElementById('exicom-contact-feedback').hidden = false; this.reset();">
                        <div class="exicom-legal__form-grid">
                            <label>
                                <span class="exicom-legal__form-label">Nombre <em>*</em></span>
                                <input type="text" name="nombre" required autocomplete="name">
                            </label>
                            <label>
                                <span class="exicom-legal__form-label">Correo electrónico <em>*</em></span>
                                <input type="email" name="email" required autocomplete="email">
                            </label>
                            <label class="exicom-legal__form-full">
                                <span class="exicom-legal__form-label">Asunto <em>*</em></span>
                                <select name="asunto" required>
                                    <option value="">Selecciona un tema</option>
                                    <option>Consulta general</option>
                                    <option>Ayuda con un pedido</option>
                                    <option>Devolución o retracto</option>
                                    <option>Garantía</option>
                                    <option>Ser vendedor en Exicompras</option>
                                    <option>Prensa / relaciones públicas</option>
                                    <option>Otro</option>
                                </select>
                            </label>
                            <label class="exicom-legal__form-full">
                                <span class="exicom-legal__form-label">Mensaje <em>*</em></span>
                                <textarea name="mensaje" rows="6" required placeholder="Escribe aquí tu mensaje..."></textarea>
                            </label>
                            <label class="exicom-legal__form-full exicom-legal__form-check">
                                <input type="checkbox" name="acepta" required>
                                <span>He leído y acepto la <a href="{{ route('legal.privacidad') }}">Política de Privacidad</a>.</span>
                            </label>
                        </div>
                        <p class="exicom-legal__form-actions">
                            <button type="submit" class="exicom-legal__form-submit">Enviar mensaje</button>
                        </p>
                        <p id="exicom-contact-feedback" class="exicom-legal__form-feedback" hidden>
                            <strong>Mensaje recibido.</strong> Te responderemos al correo electrónico que indicaste.
                        </p>
                    </form>

                    <hr class="exicom-legal__sep">

                    <p class="exicom-legal__closing"><strong>Si tu solicitud es urgente</strong> (por ejemplo, disputa activa con un pedido), te recomendamos también abrir un caso en nuestro <a href="{{ route('legal.reclamaciones') }}">Libro de Reclamaciones</a>.</p>
                </article>
            </div>
        </div>
    </section>
@stop
