@extends('shop::base')

@section('aimeos_header')
    <link type="text/css" rel="stylesheet" href="{{ asset('css/exifooter.css?v=' . ($_ver ?? '1')) }}">
@stop

@section('aimeos_body')
    @php
        $pageTitle = 'Política de Garantía';
        $pageSubtitle = 'Términos y procedimiento para hacer efectiva la garantía legal sobre productos defectuosos (Ley 1480 de 2011).';
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
                        <li><a href="#alcance">1. Alcance</a></li>
                        <li><a href="#terminos">2. Términos de garantía</a></li>
                        <li><a href="#cobertura">3. ¿Qué cubre?</a></li>
                        <li><a href="#excluye">4. ¿Qué no cubre?</a></li>
                        <li><a href="#plazos">5. Plazos</a></li>
                        <li><a href="#procedimiento">6. Procedimiento</a></li>
                        <li><a href="#soluciones">7. Soluciones posibles</a></li>
                        <li><a href="#costos">8. Costos de envío por garantía</a></li>
                        <li><a href="#sancionarios">9. Incumplimiento y sanciones</a></li>
                    </ol>
                </aside>

                <article class="exicom-legal__body">
                    <h2 id="alcance">1. Alcance</h2>
                    <p>Todos los productos ofrecidos en <strong>Exicompras</strong> cuentan con <strong>garantía legal</strong> sobre defectos de fabricación o calidad, conforme al artículo 11 de la Ley 1480 de 2011. La garantía es ofrecida por cada Vendedor y respaldada por Exicompras como intermediario.</p>

                    <h2 id="terminos">2. Términos de garantía</h2>
                    <p>Cada producto puede tener condiciones específicas (en la ficha se indica el plazo y alcance). Si el producto no informa plazo, aplica el mínimo legal de <strong>90 días calendario</strong> para productos durables y según la naturaleza del bien.</p>

                    <h2 id="cobertura">3. ¿Qué cubre?</h2>
                    <ul>
                        <li>Defectos de fabricación que impiden el uso normal del producto.</li>
                        <li>Fallas reiteradas durante el período de garantía.</li>
                        <li>No conformidad entre el producto recibido y el anunciado.</li>
                    </ul>

                    <h2 id="excluye">4. ¿Qué no cubre?</h2>
                    <ul>
                        <li>Daños por uso indebido, accidente, negligencia o modificación.</li>
                        <li>Daños por exposición a líquidos, golpes, caídas o temperaturas extremas.</li>
                        <li>Desgaste normal por uso (consumibles, pilas, etc.).</li>
                        <li>Daños por reparación realizada por personal no autorizado.</li>
                        <li>Productos cuyo sello de garantía haya sido violado.</li>
                    </ul>

                    <h2 id="plazos">5. Plazos</h2>
                    <ul>
                        <li><strong>Productos durables:</strong> mínimo 90 días calendario (Art. 11 Ley 1480/2011).</li>
                        <li><strong>Productos específicos:</strong> el plazo indicado en la ficha del producto.</li>
                        <li>El plazo empieza a contar desde la <strong>entrega</strong> del producto.</li>
                    </ul>

                    <h2 id="procedimiento">6. Procedimiento</h2>
                    <ol>
                        <li>Escríbenos a <a href="mailto:garantia@exicompras.com">garantia@exicompras.com</a> con tu número de pedido, fotos del defecto y descripción del problema.</li>
                        <li>Evaluamos el caso y, de proceder, te enviaremos una guía de transporte para devolver el producto (sin costo).</li>
                        <li>Recibimos el producto y validamos el defecto en un plazo máximo de <strong>15 días hábiles</strong>.</li>
                        <li>Te notificamos el resultado y las opciones de solución.</li>
                    </ol>

                    <h2 id="soluciones">7. Soluciones posibles</h2>
                    <ol>
                        <li><strong>Reparación</strong> del producto, en caso de ser posible.</li>
                        <li><strong>Reposición</strong> por un producto idéntico nuevo.</li>
                        <li><strong>Cambio por producto equivalente</strong> de igual o mayor valor.</li>
                        <li><strong>Devolución íntegra del dinero</strong> cuando la reparación o reposición no sea posible o cuando se trate de defectos reiterados.</li>
                    </ol>

                    <h2 id="costos">8. Costos de envío por garantía</h2>
                    <p>Cuando la garantía proceda, todos los costos de transporte (envío original, retorno y reenvío) corren por cuenta del Vendedor, sin ningún cobro para ti.</p>

                    <h2 id="sancionarios">9. Incumplimiento y sanciones</h2>
                    <p>El incumplimiento del procedimiento o de los plazos aquí indicados puede dar lugar a sanciones por parte de la <strong>Superintendencia de Industria y Comercio</strong>, conforme a la Ley 1480 de 2011.</p>

                    <hr class="exicom-legal__sep">

                    <p class="exicom-legal__closing">¿Quieres iniciar una garantía? Escríbenos a <a href="mailto:garantia@exicompras.com">garantia@exicompras.com</a>.</p>
                </article>
            </div>
        </div>
    </section>
@stop
