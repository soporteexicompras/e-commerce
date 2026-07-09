@extends('shop::base')

@section('aimeos_header')
    <link type="text/css" rel="stylesheet" href="{{ asset('css/exifooter.css?v=' . ($_ver ?? '1')) }}">
@stop

@section('aimeos_body')
    @php
        $pageTitle = 'Términos y Condiciones';
        $pageSubtitle = 'Las reglas que rigen tu relación con Exicompras y el uso de este marketplace.';
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
                        <li><a href="#identificacion">1. Identificación del prestador</a></li>
                        <li><a href="#aceptacion">2. Aceptación de los términos</a></li>
                        <li><a href="#objeto">3. Objeto</a></li>
                        <li><a href="#registro">4. Registro y cuenta</a></li>
                        <li><a href="#compras">5. Proceso de compra</a></li>
                        <li><a href="#precios">6. Precios, impuestos y moneda</a></li>
                        <li><a href="#pagos">7. Medios de pago</a></li>
                        <li><a href="#envios">8. Envío y entrega</a></li>
                        <li><a href="#retracto">9. Derecho de retracto</a></li>
                        <li><a href="#garantia">10. Garantía legal</a></li>
                        <li><a href="#reclamaciones">11. Peticiones, quejas y reclamos (PQR)</a></li>
                        <li><a href="#propiedad">12. Propiedad intelectual</a></li>
                        <li><a href="#limitacion">13. Limitación de responsabilidad</a></li>
                        <li><a href="#suspension">14. Suspensión y terminación</a></li>
                        <li><a href="#ley">15. Ley aplicable y jurisdicción</a></li>
                        <li><a href="#cambios">16. Cambios a estos términos</a></li>
                    </ol>
                </aside>

                <article class="exicom-legal__body">
                    <p>Bienvenido/a a <strong>Exicompras</strong>. Al acceder, navegar o comprar en este sitio web aceptas los presentes Términos y Condiciones. Te recomendamos leerlos atentamente y, si tienes alguna duda, contactarnos antes de realizar cualquier transacción. Este documento se rige por la <strong>Ley 1480 de 2011</strong> (Estatuto del Consumidor) y demás normas concordantes aplicables en la República de Colombia.</p>

                    <h2 id="identificacion">1. Identificación del prestador</h2>
                    <p>Exicompras es un marketplace operado por:</p>
                    <ul>
                        <li><strong>Razón social:</strong> Exicompras S.A.S. (en adelante, «Exicompras»)</li>
                        <li><strong>NIT:</strong> 900.000.000-0</li>
                        <li><strong>Domicilio:</strong> Carrera 1 # 2-3, Bogotá D.C., Colombia</li>
                        <li><strong>Teléfono:</strong> +57 (1) 000 0000</li>
                        <li><strong>Correo electrónico:</strong> <a href="mailto:legal@exicompras.com">legal@exicompras.com</a></li>
                    </ul>

                    <h2 id="aceptacion">2. Aceptación de los términos</h2>
                    <p>El uso del sitio web, la creación de una cuenta o la realización de un pedido implica la aceptación plena y sin reservas de estos Términos. Si no estás de acuerdo con alguno de los apartados, abstente de usar la plataforma.</p>

                    <h2 id="objeto">3. Objeto</h2>
                    <p>Exicompras es un marketplace que facilita la exhibición y venta de productos ofrecidos por distintos vendedores («Vendedores»), quienes son los responsables directos de la calidad, idoneidad, seguridad y entrega de los productos que publican. Exicompras actúa como intermediario tecnológico y comercial conforme al artículo 53 de la Ley 1480 de 2011.</p>

                    <h2 id="registro">4. Registro y cuenta</h2>
                    <p>Para comprar debes crear una cuenta con datos veraces, completos y actualizados. Eres responsable de custodiar tus credenciales y de toda actividad que ocurra bajo tu sesión. Exicompras no se hace responsable por accesos no autorizados derivados de un mal uso de tu contraseña.</p>

                    <h2 id="compras">5. Proceso de compra</h2>
                    <ol>
                        <li>Selección de productos y cantidades.</li>
                        <li>Configuración de la dirección de envío.</li>
                        <li>Selección del medio de pago.</li>
                        <li>Confirmación del pedido y validación del pago.</li>
                        <li>Generación de la confirmación de venta (correo electrónico y sección «Mis pedidos»).</li>
                    </ol>

                    <h2 id="precios">6. Precios, impuestos y moneda</h2>
                    <p>Todos los precios se expresan en <strong>pesos colombianos (COP)</strong> e incluyen el <strong>IVA</strong> cuando aplica, salvo que se indique lo contrario. Las ofertas y promociones son válidas dentro del período publicado o hasta agotar existencias, lo que ocurra primero.</p>

                    <h2 id="pagos">7. Medios de pago</h2>
                    <p>Exicompras admite pagos con tarjeta de crédito/débito (Visa, Mastercard, American Express), PSE, Nequi, Daviplata y Efecty. Los pagos son procesados por pasarelas certificadas PCI-DSS; Exicompras no almacena datos sensibles de tarjeta.</p>

                    <h2 id="envios">8. Envío y entrega</h2>
                    <p>Los plazos de entrega se indican antes de confirmar la compra y empiezan a contar desde la confirmación del pago. Más detalles en nuestra <a href="{{ route('legal.envios') }}">Política de Envíos</a>.</p>

                    <h2 id="retracto">9. Derecho de retracto</h2>
                    <p>Conforme al artículo 47 de la Ley 1480 de 2011, en compras realizadas por medios no presenciales (internet, teléfono, etc.) cuentas con un plazo de <strong>5 días hábiles</strong> contados a partir de la entrega del producto para ejercer el derecho de retracto, siempre que el producto no sea perecedero, personalizado, o de uso íntimo, entre las excepciones del artículo 46. Más información en nuestra <a href="{{ route('legal.cancelaciones') }}">Política de Cancelación, Devoluciones y Retracto</a>.</p>

                    <h2 id="garantia">10. Garantía legal</h2>
                    <p>Todos los productos cuentan con <strong>garantía legal</strong> sobre defectos de fabricación o calidad. El término de garantía varía según el producto y se informa en cada ficha. Puedes solicitar la efectividad de la garantía a través del procedimiento descrito en nuestra <a href="{{ route('legal.garantias') }}">Política de Garantía</a>.</p>

                    <h2 id="reclamaciones">11. Peticiones, quejas y reclamos (PQR)</h2>
                    <p>Tienes derecho a presentar peticiones, quejas, reclamos, sugerencias y felicitaciones. Puedes hacerlo a través del <a href="{{ route('legal.reclamaciones') }}">Libro de Reclamaciones Virtual</a> o escribiendo a <a href="mailto:atencion@exicompras.com">atencion@exicompras.com</a>. Damos respuesta en un plazo máximo de <strong>15 días hábiles</strong> (Art. 50 Ley 1480/2011).</p>

                    <h2 id="propiedad">12. Propiedad intelectual</h2>
                    <p>Todo el contenido del sitio (marcas, logotipos, textos, imágenes, código y diseño) es propiedad de Exicompras o de los Vendedores y está protegido por las normas de propiedad intelectual de Colombia. Queda prohibida su reproducción sin autorización previa.</p>

                    <h2 id="limitacion">13. Limitación de responsabilidad</h2>
                    <p>Exicompras no será responsable por daños indirectos, lucro cesante o pérdida de datos derivados del uso del sitio, salvo en los casos en que la ley colombiana establezca responsabilidad objetiva. La responsabilidad total por una compra no superará el valor pagado por el producto en cuestión.</p>

                    <h2 id="suspension">14. Suspensión y terminación</h2>
                    <p>Podemos suspender o cancelar tu cuenta si incumples estos Términos, la ley aplicable o si detectamos actividad fraudulenta. En tal caso, conservaremos los registros de las transacciones conforme a la normativa vigente.</p>

                    <h2 id="ley">15. Ley aplicable y jurisdicción</h2>
                    <p>Estos Términos se rigen por las leyes de la República de Colombia. Cualquier controversia será sometida a los jueces competentes de <strong>Bogotá D.C.</strong>, sin perjuicio del derecho del consumidor a acudir a la Superintendencia de Industria y Comercio o a los mecanismos alternativos de solución de conflictos.</p>

                    <h2 id="cambios">16. Cambios a estos términos</h2>
                    <p>Exicompras podrá modificar estos Términos en cualquier momento. Los cambios se publicarán en esta misma URL y, cuando sean significativos, se notificarán por correo electrónico. El uso continuado del sitio después de la publicación implica aceptación.</p>

                    <hr class="exicom-legal__sep">

                    <p class="exicom-legal__closing"><strong>¿Dudas?</strong> Escríbenos a <a href="mailto:legal@exicompras.com">legal@exicompras.com</a>.</p>
                </article>
            </div>
        </div>
    </section>
@stop
