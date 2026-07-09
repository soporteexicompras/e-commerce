@extends('shop::base')

@section('aimeos_header')
    <link type="text/css" rel="stylesheet" href="{{ asset('css/exifooter.css?v=' . ($_ver ?? '1')) }}">
@stop

@section('aimeos_body')
    @php
        $pageTitle = 'Libro de Reclamaciones';
        $pageSubtitle = 'Registra tu PQR (Petición, Queja, Reclamo o Sugerencia) conforme al Decreto 735 de 2013.';
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
                        <li><a href="#marco">1. Marco legal</a></li>
                        <li><a href="#que-puedes">2. ¿Qué puedes registrar?</a></li>
                        <li><a href="#formulario">3. Formulario virtual</a></li>
                        <li><a href="#plazo">4. Plazo de respuesta</a></li>
                        <li><a href="#siguientes">5. Pasos siguientes</a></li>
                        <li><a href="#contactos">6. Otros canales</a></li>
                    </ol>
                </aside>

                <article class="exicom-legal__body">
                    <h2 id="marco">1. Marco legal</h2>
                    <p>En cumplimiento del artículo 17 de la Ley 1480 de 2011 y del <strong>Decreto 735 de 2013</strong>, Exicompras pone a tu disposición un <strong>Libro de Reclamaciones Virtual</strong> donde puedes registrar Peticiones, Quejas, Reclamos, Sugerencias y Felicitaciones.</p>

                    <h2 id="que-puedes">2. ¿Qué puedes registrar?</h2>
                    <ul>
                        <li><strong>Petición:</strong> solicitud de información o acción.</li>
                        <li><strong>Queja:</strong> inconformidad por la atención recibida.</li>
                        <li><strong>Reclamo:</strong> incumplimiento contractual o insatisfacción con un producto o servicio.</li>
                        <li><strong>Sugerencia:</strong> idea para mejorar el servicio.</li>
                        <li><strong>Felicitación:</strong> reconocimiento a un miembro del equipo.</li>
                    </ul>

                    <h2 id="formulario">3. Formulario virtual</h2>
                    <form class="exicom-legal__form" onsubmit="event.preventDefault(); document.getElementById('exicom-legal-form-feedback').hidden = false; this.reset();">
                        <div class="exicom-legal__form-grid">
                            <label>
                                <span class="exicom-legal__form-label">Nombre completo <em>*</em></span>
                                <input type="text" name="nombre" required autocomplete="name">
                            </label>
                            <label>
                                <span class="exicom-legal__form-label">Documento de identidad <em>*</em></span>
                                <input type="text" name="documento" required autocomplete="off">
                            </label>
                            <label>
                                <span class="exicom-legal__form-label">Correo electrónico <em>*</em></span>
                                <input type="email" name="email" required autocomplete="email">
                            </label>
                            <label>
                                <span class="exicom-legal__form-label">Teléfono de contacto</span>
                                <input type="tel" name="telefono" autocomplete="tel">
                            </label>
                            <label class="exicom-legal__form-full">
                                <span class="exicom-legal__form-label">Tipo <em>*</em></span>
                                <select name="tipo" required>
                                    <option value="">Selecciona una opción</option>
                                    <option>Petición</option>
                                    <option>Queja</option>
                                    <option>Reclamo</option>
                                    <option>Sugerencia</option>
                                    <option>Felicitación</option>
                                </select>
                            </label>
                            <label>
                                <span class="exicom-legal__form-label">Número de pedido (opcional)</span>
                                <input type="text" name="pedido">
                            </label>
                            <label>
                                <span class="exicom-legal__form-label">Producto / servicio</span>
                                <input type="text" name="producto">
                            </label>
                            <label class="exicom-legal__form-full">
                                <span class="exicom-legal__form-label">Descripción detallada <em>*</em></span>
                                <textarea name="descripcion" rows="6" required placeholder="Cuéntanos con detalle qué ocurrió, qué solicitas y cómo podemos ayudarte."></textarea>
                            </label>
                            <label class="exicom-legal__form-full exicom-legal__form-check">
                                <input type="checkbox" name="acepta" required>
                                <span>Autorizo el tratamiento de mis datos personales conforme a la <a href="{{ route('legal.privacidad') }}">Política de Privacidad</a> para gestionar esta solicitud.</span>
                            </label>
                        </div>
                        <p class="exicom-legal__form-actions">
                            <button type="submit" class="exicom-legal__form-submit">Enviar reclamo</button>
                        </p>
                        <p id="exicom-legal-form-feedback" class="exicom-legal__form-feedback" hidden>
                            <strong>Hemos recibido tu solicitud.</strong> Te asignaremos un número de caso y te responderemos al correo electrónico que indicaste en un plazo máximo de 15 días hábiles.
                        </p>
                    </form>

                    <h2 id="plazo">4. Plazo de respuesta</h2>
                    <ul>
                        <li>Peticiones: <strong>15 días hábiles</strong>.</li>
                        <li>Quejas y reclamos: <strong>15 días hábiles</strong> (Art. 50 Ley 1480/2011).</li>
                        <li>Si el caso requiere información técnica adicional, te informaremos el plazo ampliado de común acuerdo.</li>
                    </ul>

                    <h2 id="siguientes">5. Pasos siguientes</h2>
                    <ol>
                        <li>Registramos tu solicitud y le asignamos un <strong>número de caso</strong>.</li>
                        <li>Investigamos internamente con el Vendedor y/o áreas involucradas.</li>
                        <li>Te enviamos una respuesta con la solución o las acciones tomadas.</li>
                        <li>Si no estás conforme, puedes escalar a la <strong>Superintendencia de Industria y Comercio</strong> (<a href="https://www.sic.gov.co" rel="noopener">www.sic.gov.co</a>) o a la <strong>Delegatura de la Asociación de Consumidores y Protección al Consumidor</strong>.</li>
                    </ol>

                    <h2 id="contactos">6. Otros canales</h2>
                    <ul>
                        <li><strong>Email:</strong> <a href="mailto:atencion@exicompras.com">atencion@exicompras.com</a></li>
                        <li><strong>Teléfono:</strong> +57 (1) 000 0000 — lunes a viernes de 8:00 a.m. a 6:00 p.m.</li>
                        <li><strong>Presencial (no aplica):</strong> Exicompras solo opera digitalmente.</li>
                    </ul>

                    <hr class="exicom-legal__sep">

                    <p class="exicom-legal__closing">El <strong>Libro de Reclamaciones</strong> es un derecho irrenunciable del consumidor. Este sitio cumple con todas las obligaciones formales exigidas por la Superintendencia de Industria y Comercio.</p>
                </article>
            </div>
        </div>
    </section>
@stop
