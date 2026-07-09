@extends('shop::base')

@section('aimeos_header')
    <link type="text/css" rel="stylesheet" href="{{ asset('css/exifooter.css?v=' . ($_ver ?? '1')) }}">
@stop

@section('aimeos_body')
    @php
        $pageTitle = 'Política de Privacidad';
        $pageSubtitle = 'Cómo Exicompras trata tus datos personales conforme a la Ley 1581 de 2012 y la Ley 1266 de 2008.';
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
                        <li><a href="#identificacion">1. Identificación del responsable</a></li>
                        <li><a href="#definiciones">2. Definiciones</a></li>
                        <li><a href="#tratamiento">3. Datos que recopilamos</a></li>
                        <li><a href="#finalidad">4. Finalidad del tratamiento</a></li>
                        <li><a href="#consentimiento">5. Consentimiento</a></li>
                        <li><a href="#comparticion">6. ¿Compartimos tus datos?</a></li>
                        <li><a href="#transferencia">7. Transferencia internacional</a></li>
                        <li><a href="#seguridad">8. Seguridad</a></li>
                        <li><a href="#derechos">9. Derechos que tienes como titular</a></li>
                        <li><a href="#menores">10. Tratamiento de datos de menores</a></li>
                        <li><a href="#cookies">11. Cookies</a></li>
                        <li><a href="#cambios">12. Cambios a esta política</a></li>
                        <li><a href="#contacto">13. Contacto del área de datos</a></li>
                    </ol>
                </aside>

                <article class="exicom-legal__body">
                    <p>En <strong>Exicompras</strong> respetamos tu derecho a la autodeterminación informativa. Esta Política describe qué datos recopilamos, con qué fines, cómo los protegemos y cómo puedes ejercer tus derechos conforme a la <strong>Ley Estatutaria 1581 de 2012</strong>, el <strong>Decreto 1377 de 2013</strong>, la <strong>Ley 1266 de 2008</strong> y demás normas concordantes sobre protección de datos personales en Colombia.</p>

                    <h2 id="identificacion">1. Identificación del responsable</h2>
                    <ul>
                        <li><strong>Razón social:</strong> Exicompras S.A.S.</li>
                        <li><strong>NIT:</strong> 900.000.000-0</li>
                        <li><strong>Dirección:</strong> Carrera 1 # 2-3, Bogotá D.C., Colombia</li>
                        <li><strong>Correo del responsable del tratamiento:</strong> <a href="mailto:datospersonales@exicompras.com">datospersonales@exicompras.com</a></li>
                    </ul>

                    <h2 id="definiciones">2. Definiciones</h2>
                    <ul>
                        <li><strong>Dato personal:</strong> cualquier información vinculada o que pueda asociarse a una persona natural identificada o identificable.</li>
                        <li><strong>Dato sensible:</strong> dato que afecta la intimidad (origen racial, salud, orientación sexual, etc.). Exicompras no solicita datos sensibles en este sitio.</li>
                        <li><strong>Tratamiento:</strong> cualquier operación sobre datos personales (recolección, almacenamiento, uso, circulación o supresión).</li>
                        <li><strong>Titular:</strong> persona natural cuyos datos son tratados.</li>
                    </ul>

                    <h2 id="tratamiento">3. Datos que recopilamos</h2>
                    <p>Recopilamos:</p>
                    <ul>
                        <li><strong>Identificación:</strong> nombres, apellidos, tipo y número de documento, fecha de nacimiento (cuando aplique).</li>
                        <li><strong>Contacto:</strong> correo electrónico, teléfono, direcciones de envío y facturación.</li>
                        <li><strong>Cuenta y preferencias:</strong> nombre de usuario, contraseña (hash), historial de pedidos, lista de favoritos.</li>
                        <li><strong>Pago:</strong> token devuelto por la pasarela (no almacenamos PAN/CVV).</li>
                        <li><strong>Dispositivo y navegación:</strong> dirección IP, tipo de dispositivo, navegador, páginas visitadas (ver <a href="#cookies">Cookies</a>).</li>
                    </ul>

                    <h2 id="finalidad">4. Finalidad del tratamiento</h2>
                    <p>Tus datos serán tratados para:</p>
                    <ol>
                        <li>Gestionar tu registro, cuenta y autenticación.</li>
                        <li>Crear, procesar y entregar tus pedidos.</li>
                        <li>Prevenir fraude y garantizar la seguridad de las transacciones.</li>
                        <li>Atender PQR, retractos y garantías.</li>
                        <li>Enviar comunicaciones de tu pedido, seguridad de la cuenta y cambios importantes.</li>
                        <li>Enviarte información comercial sobre productos y ofertas (solo si nos autorizas — ver punto 5).</li>
                        <li>Elaborar reportes, estadísticas y mejorar el servicio.</li>
                        <li>Cumplir obligaciones legales y contables.</li>
                    </ol>

                    <h2 id="consentimiento">5. Consentimiento</h2>
                    <p>Al registrarte y/o comprar en el sitio, aceptas esta Política. Para finalidades que requieran autorización expresa (por ejemplo, recibir marketing por correo electrónico o SMS), te pediremos un consentimiento separado que podrás retirar en cualquier momento.</p>

                    <h2 id="comparticion">6. ¿Compartimos tus datos?</h2>
                    <p>Compartimos datos con:</p>
                    <ul>
                        <li><strong>Pasarelas de pago:</strong> para procesar el pago de forma segura.</li>
                        <li><strong>Transportadoras:</strong> para entregar tu pedido.</li>
                        <li><strong>Proveedores de hosting y analítica:</strong> con obligaciones de confidencialidad.</li>
                        <li><strong>Autoridades:</strong> cuando exista requerimiento legal o para prevenir fraude.</li>
                    </ul>
                    <p>Los Vendedores del marketplace reciben únicamente la información necesaria para preparar y entregar tu pedido.</p>

                    <h2 id="transferencia">7. Transferencia internacional</h2>
                    <p>Algunos de nuestros proveedores de hosting o analítica pueden tratar datos en servidores ubicados fuera de Colombia. En cualquier caso, exigimos niveles adecuados de protección y cláusulas tipo conforme a la normativa colombiana.</p>

                    <h2 id="seguridad">8. Seguridad</h2>
                    <p>Aplicamos medidas técnicas y administrativas razonables (HTTPS/TLS, cifrado de contraseñas, control de accesos por roles, registro de auditoría) para proteger tus datos. Aun así, ningún sistema es 100% infalible; te recomendamos también mantener tus contraseñas seguras.</p>

                    <h2 id="derechos">9. Derechos que tienes como titular</h2>
                    <p>Como titular de los datos puedes:</p>
                    <ul>
                        <li><strong>Conocer, acceder y consultar</strong> tus datos.</li>
                        <li><strong>Actualizar y rectificar</strong> datos incompletos o erróneos.</li>
                        <li><strong>Solicitar la supresión</strong> de tus datos cuando sean innecesarios o no deseados.</li>
                        <li><strong>Oponerte</strong> al tratamiento para finalidades distintas a las necesarias para la relación contractual.</li>
                        <li><strong>Revocar</strong> tu consentimiento.</li>
                        <li><strong>Presentar quejas</strong> ante la Superintendencia de Industria y Comercio.</li>
                    </ul>
                    <p>Para ejercer cualquiera de estos derechos (<strong>consulta, rectificación, supresión o revocación</strong>), escríbenos a <a href="mailto:datospersonales@exicompras.com">datospersonales@exicompras.com</a> con tu nombre completo y el derecho que deseas ejercer. Responderemos en un plazo máximo de <strong>15 días hábiles</strong>.</p>

                    <h2 id="menores">10. Tratamiento de datos de menores</h2>
                    <p>No tratamos datos de menores de 18 años. Si eres padre/madre o representante legal y crees que un menor ha proporcionado datos personales en este sitio, contáctanos para proceder a su supresión.</p>

                    <h2 id="cookies">11. Cookies</h2>
                    <p>Usamos cookies técnicas para que el sitio funcione y cookies de analítica (Google Analytics u otras) para entender cómo lo usas y mejorarlo. Puedes rechazar las cookies no esenciales desde el banner de consentimiento o configurando tu navegador. Las cookies técnicas y de autenticación son necesarias para que el sitio funcione y no pueden desactivarse.</p>

                    <h2 id="cambios">12. Cambios a esta política</h2>
                    <p>Podremos modificar esta Política para reflejar cambios legales, técnicos o de operación. Publicaremos la versión actualizada en esta misma URL e indicaremos la fecha de última actualización.</p>

                    <h2 id="contacto">13. Contacto del área de datos</h2>
                    <p>Para cualquier asunto relacionado con tus datos personales contáctanos en <a href="mailto:datospersonales@exicompras.com">datospersonales@exicompras.com</a> o al +57 (1) 000 0000.</p>

                    <hr class="exicom-legal__sep">

                    <p class="exicom-legal__closing"><strong>Marco normativo aplicable:</strong> Ley Estatutaria 1581 de 2012 · Decreto 1377 de 2013 · Ley 1266 de 2008 · Circular Externa 002 de 2015 (SIC) · Ley 1480 de 2011 (Estatuto del Consumidor).</p>
                </article>
            </div>
        </div>
    </section>
@stop
