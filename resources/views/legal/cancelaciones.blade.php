@extends('shop::base')

@section('aimeos_header')
    <link type="text/css" rel="stylesheet" href="{{ asset('css/exifooter.css?v=' . ($_ver ?? '1')) }}">
@stop

@section('aimeos_body')
    @php
        $pageTitle = 'Política de Cancelación, Devoluciones y Retracto';
        $pageSubtitle = 'Cómo cancelar un pedido, devolver un producto o ejercer tu derecho de retracto, conforme a la Ley 1480 de 2011.';
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
                        <li><a href="#antes-de-comprar">1. Antes de comprar</a></li>
                        <li><a href="#cancelar-pedido">2. Cancelar un pedido antes del envío</a></li>
                        <li><a href="#retracto">3. Derecho de retracto</a></li>
                        <li><a href="#devolucion">4. Devolución por defecto o insatisfacción</a></li>
                        <li><a href="#estado">5. Estado del producto para devolución</a></li>
                        <li><a href="#plazos">6. Plazos</a></li>
                        <li><a href="#reembolso">7. Reembolso</a></li>
                        <li><a href="#excepciones">8. Excepciones</a></li>
                        <li><a href="#garantia">9. Garantía legal</a></li>
                        <li><a href="#contacto">10. Cómo iniciar el proceso</a></li>
                    </ol>
                </aside>

                <article class="exicom-legal__body">
                    <p>Esta política aplica a todas las compras realizadas en <strong>Exicompras</strong> y se rige por la <strong>Ley 1480 de 2011</strong> (Estatuto del Consumidor), especialmente sus artículos 46, 47 y 51. Detalla tres escenarios frecuentes: <em>cancelación previa al envío</em>, <em>retracto post-entrega</em> y <em>devolución por defectos o insatisfacción</em>.</p>

                    <h2 id="antes-de-comprar">1. Antes de comprar</h2>
                    <p>Verifica con atención las características de cada producto (color, talla, dimensiones, voltage, compatibilidad). Si tienes dudas, contáctanos antes de pagar para evitar cancelaciones o devoluciones innecesarias. La información y fotografías son orientativas; pueden existir variaciones menores propias del proveedor.</p>

                    <h2 id="cancelar-pedido">2. Cancelar un pedido antes del envío</h2>
                    <p>Si tu pedido aún no ha sido despachado, puedes cancelarlo sin costo desde tu cuenta (sección <em>Mis pedidos</em>) o escribiendo a <a href="mailto:pedidos@exicompras.com">pedidos@exicompras.com</a> con el número de orden. El reembolso se gestiona de inmediato por el mismo medio de pago y se ve reflejado en los plazos del procesador.</p>

                    <h2 id="retracto">3. Derecho de retracto</h2>
                    <p>Conforme al artículo 47 de la Ley 1480 de 2011, en compras realizadas por medios no presenciales (internet, teléfono, domicilio) tienes el derecho de <strong>retracto dentro de los 5 días hábiles siguientes a la entrega del producto</strong>, siempre que:</p>
                    <ul>
                        <li>El producto se devuelva en las mismas condiciones en que lo recibiste.</li>
                        <li>Conserves el empaque original, etiquetas, manuales y accesorios.</li>
                        <li>No haya sido usado, dañado o alterado.</li>
                        <li>El producto no esté en la lista de excepciones del artículo 46 (ver más abajo).</li>
                    </ul>
                    <p>Para ejercer el retracto, basta con manifestar tu voluntad dentro del plazo, preferiblemente por escrito a <a href="mailto:retracto@exicompras.com">retracto@exicompras.com</a>. Puedes usar también el mecanismo que ponemos a tu disposición en <em>Mis pedidos &raquo; Retracto</em>.</p>

                    <h2 id="devolucion">4. Devolución por defecto o insatisfacción</h2>
                    <p>Si recibes un producto con defectos de fabricación, daños en el transporte o no corresponde al publicado, <strong>Exicompras y el Vendedor asumen los costos de retorno</strong> y te ofrecemos una de estas opciones a elección:</p>
                    <ol>
                        <li>Cambio por un producto idéntico nuevo (cuando haya stock).</li>
                        <li>Cambio por un producto diferente de igual o mayor valor (pagando la diferencia).</li>
                        <li>Devolución íntegra del dinero (incluyendo el costo de envío).</li>
                    </ol>

                    <h2 id="estado">5. Estado del producto para devolución</h2>
                    <ul>
                        <li>El producto debe devolverse sin usar, con todos sus sellos, etiquetas, manuales, accesorios y empaque original.</li>
                        <li>Productos defectuosos: el cliente puede devolverlos aunque hayan sido usados, siempre que la devolución sea por una causa atribuible al Vendedor.</li>
                        <li>Productos personalizados, de higiene íntima o perecederos no son elegibles para devolución salvo defecto.</li>
                    </ul>

                    <h2 id="plazos">6. Plazos</h2>
                    <table class="exicom-legal__table">
                        <thead><tr><th>Acción</th><th>Plazo (hábiles)</th></tr></thead>
                        <tbody>
                            <tr><td>Cancelación antes del envío</td><td>Cualquier momento previo al despacho</td></tr>
                            <tr><td>Retracto post-entrega</td><td><strong>5 días</strong> desde la entrega</td></tr>
                            <tr><td>Devolución por defecto</td><td><strong>30 días</strong> desde la entrega</td></tr>
                            <tr><td>Reclamo por garantía legal</td><td>Según los términos informados en la ficha del producto</td></tr>
                        </tbody>
                    </table>

                    <h2 id="reembolso">7. Reembolso</h2>
                    <ul>
                        <li><strong>Tarjeta de crédito/débito:</strong> hasta 30 días calendario por gestión de la pasarela, dependiendo del banco emisor.</li>
                        <li><strong>PSE / Nequi / Daviplata:</strong> entre 3 y 10 días hábiles.</li>
                        <li><strong>Efecty / pagos en efectivo:</strong> consignación o transferencia en un máximo de 8 días hábiles.</li>
                        <li>Los costos de envío no se reembolsan salvo que la devolución sea por causa atribuible al Vendedor.</li>
                    </ul>

                    <h2 id="excepciones">8. Excepciones al derecho de retracto</h2>
                    <p>Conforme al artículo 46 de la Ley 1480/2011, no procede retracto en:</p>
                    <ul>
                        <li>Productos perecederos (alimentos frescos, flores, etc.).</li>
                        <li>Productos personalizados o elaborados bajo pedido.</li>
                        <li>Productos de uso íntimo (ropa interior, cosméticos, etc.) una vez abierto el empaque original.</li>
                        <li>Productos audiovisuales o software descargado digitalmente.</li>
                        <li>Servicios ya ejecutados.</li>
                        <li>Productos que, por su naturaleza, no puedan devolverse o puedan deteriorarse (baterías, celulares abiertos, etc.).</li>
                    </ul>

                    <h2 id="garantia">9. Garantía legal</h2>
                    <p>Adicionalmente al retracto, todos los productos cuentan con <strong>garantía legal</strong> por defectos de calidad o idoneidad. Consulta los detalles y el procedimiento en nuestra <a href="{{ route('legal.garantias') }}">Política de Garantía</a>.</p>

                    <h2 id="contacto">10. Cómo iniciar el proceso</h2>
                    <p>Para iniciar una cancelación, devolución o retracto:</p>
                    <ol>
                        <li>Entra a tu cuenta &raquo; <strong>Mis pedidos</strong>.</li>
                        <li>Selecciona el pedido y la opción correspondiente.</li>
                        <li>O escribe a <a href="mailto:retracto@exicompras.com">retracto@exicompras.com</a> con tu número de orden, producto y motivo.</li>
                    </ol>

                    <hr class="exicom-legal__sep">

                    <p class="exicom-legal__closing">Si la respuesta a tu solicitud no es satisfactoria, puedes acudir a la <strong>Superintendencia de Industria y Comercio</strong> a través del portal <a href="https://www.sic.gov.co" rel="noopener">www.sic.gov.co</a> o del <a href="{{ route('legal.reclamaciones') }}">Libro de Reclamaciones</a> de Exicompras.</p>
                </article>
            </div>
        </div>
    </section>
@stop
