<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Aimeos\Base\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeedSpecialProducts extends Command
{
    protected $signature = 'exi:seed-special-products';

    protected $description = 'Crea (idempotente) productos de ejemplo para Influencers, Coleccionistas y Artistas';

    private string $siteid = '1.';
    private string $now;

    /**
     * Productos "tendencia" para Influencers — items lifestyle, moda, gadgets
     * que un influencer recomendaria. Sin imagenes: el admin las sube despues.
     */
    private array $influencersProducts = [
        [
            'code'  => 'INF-001',
            'label' => 'Kit Skincare Coreano Glow',
            'name'  => 'Kit Skincare Coreano Glow — Rutina completa 8 pasos',
            'short' => 'Rutina completa de skincare coreano para una piel luminosa y saludable.',
            'long'  => 'Descubre la rutina de skincare coreano de 8 pasos con productos de alta calidad. Incluye limpiador, tonico, esencia, serum, contorno de ojos, hidratante, protector solar y mascarilla nocturna. Formulado con ingredientes naturales como ginseng, bambu y extracto de caracol para nutrir, hidratar y rejuvenecer tu piel. Ideal para todo tipo de piel, especialmente piel apagada o con signos de fatiga. Presentacion premium con caja de regalo.',
            'price' => 189000,
        ],
        [
            'code'  => 'INF-002',
            'label' => 'Auriculares Bluetooth Estilo Retro',
            'name'  => 'Auriculares Bluetooth Estilo Retro — Marshall-style',
            'short' => 'Auriculares inalambricos con diseno retro premium y 30h de bateria.',
            'long'  => 'Auriculares inalambricos con diseno retro tipo Marshall, perfectos para los amantes del estilo vintage. Hasta 30 horas de bateria con una sola carga, conexion Bluetooth 5.3 estable, diadema acolchada para maxima comodidad, microfono integrado para llamadas y controles fisicos para volumen y reproduccion. Sonido balanceado con graves profundos y agudos claros. Ideales para uso diario, oficina o viajes.',
            'price' => 249000,
        ],
        [
            'code'  => 'INF-003',
            'label' => 'Camara Instantanea Retro Polaroid',
            'name'  => 'Camara Instantanea Retro Polaroid — Edicion Limitada',
            'short' => 'Camara instantanea con diseno retro y tecnologia moderna. Fotos fisicas al instante.',
            'long'  => 'Revive la magia de la fotografia instantanea con esta camara de diseno retro. Disparos automaticos con exposicion adaptativa, flash integrado, espejo para selfies y bateria recargable por USB-C. Compatible con peliculas Polaroid i-Type y 600. Cada foto es una pieza unica que se desarrolla en minutos. Perfecta para fiestas, viajes o crear recuerdos tangibles en la era digital. Incluye correa para el cuello y 1 pack de pelicula de regalo.',
            'price' => 459000,
        ],
        [
            'code'  => 'INF-004',
            'label' => 'Lentes de Sol Polarizados Vintage',
            'name'  => 'Lentes de Sol Polarizados Vintage — Estilo Retro Unisex',
            'short' => 'Lentes de sol polarizados con montura vintage y proteccion UV400.',
            'long'  => 'Lentes de sol con diseno retro atemporal, perfectos para cualquier ocasion. Montura ligera de acetato, lentes polarizados con proteccion UV400 contra rayos dañinos, plaquetas ajustables para mayor comodidad y varillas reforzadas con bisagras metalicas. Ideales para conducir, actividades al aire libre o simplemente para complementar tu look. Incluyen estuche rigido de proteccion y gamuza de limpieza.',
            'price' => 129000,
        ],
        [
            'code'  => 'INF-005',
            'label' => 'Zapatillas Urbanas Trending Streetwear',
            'name'  => 'Zapatillas Urbanas Trending Streetwear — Edicion Limitada',
            'short' => 'Zapatillas urbanas con diseno streetwear y maxima comodidad para el dia a dia.',
            'long'  => 'Zapatillas urbanas con diseno streetwear inspirado en las tendencias de las pasarelas internacionales. Capellada de cuero sintetico premium y malla transpirable, suela de EVA ligera con amortiguacion de aire, plantilla ergonomica extraible y cordon elastico con sistema de ajuste rapido. Perfectas para combinar con jeans, joggers o shorts. Numeracion 36-44. Edicion limitada.',
            'price' => 389000,
        ],
        [
            'code'  => 'INF-006',
            'label' => 'Difusor de Aromas Premium Ceramica',
            'name'  => 'Difusor de Aromas Premium Ceramica — Diseño Nordico',
            'short' => 'Difusor ultrasónico con diseño nordico y luces LED ambientales.',
            'long'  => 'Transforma tu espacio con este difusor de aromas ultrasonico de diseño nordico minimalista. Capacidad de 500ml para 10+ horas de aromaterapia continua, tecnologia ultrasónica silenciosa, 7 colores LED con cambio gradual o fijo, apagado automatico sin agua y modo niebla fria/calida. Incluye adaptador, vaso medidor y manual. Compatible con cualquier aceite esencial. Ceramica esmaltada en blanco mate.',
            'price' => 159000,
        ],
    ];

    /**
     * Productos de coleccion variadas para Coleccionistas — varios nichos.
     */
    private array $coleccionistasProducts = [
        [
            'code'  => 'COL-001',
            'label' => 'Figura de Accion Edicion Limitada',
            'name'  => 'Figura de Accion Edicion Limitada — Numerada 1/500',
            'short' => 'Figura de accion premium numerada y certificada, edicion limitada.',
            'long'  => 'Figura de accion de coleccion escala 1/6, fabricada en resina de alta calidad con acabados pintados a mano. Edicion limitada y numerada (maximo 500 unidades en el mundo). Incluye certificado de autenticidad, base con nombre y articulacion多点 para poses personalizadas. Embalaje premium con ventana transparente. Altura aproximada 30 cm. Pieza de exhibicion para coleccionistas exigentes.',
            'price' => 450000,
        ],
        [
            'code'  => 'COL-002',
            'label' => 'Moneda Conmemorativa Plata 999',
            'name'  => 'Moneda Conmemorativa Plata 999 — Edicion Coleccionista',
            'short' => 'Moneda de plata pura 999, edicion de coleccionista con certificado.',
            'long'  => 'Moneda conmemorativa fabricada en plata de ley 999, peso 31.1g (1 onza troy). Acabado proof con detalles en relieve que resaltan el diseño historico. Edicion limitada de 1.000 unidades worldwide. Incluye certificado de autenticidad, caja de presentacion acolchada y funda protectora. Ideal para coleccionistas de numismatica o como pieza de inversion en metales preciosos. Diseño exclusivo que evoca la historia economica latinoamericana.',
            'price' => 320000,
        ],
        [
            'code'  => 'COL-003',
            'label' => 'Comic Primera Edicion Vintage',
            'name'  => 'Comic Primera Edicion Vintage — Pieza Rara',
            'short' => 'Comic original primera edicion en excelente estado de conservacion.',
            'long'  => 'Comic original de primera edicion (no reimpresion), pieza rara y buscada por coleccionistas. Estado de conservacion: muy bueno a excelente (puede tener pequenas marcas de almacenamiento propias de la epoca, pero sin paginas faltantes ni daños estructurales). Incluye funda protectora mylar y soporte rigido. Certificacion de autenticidad por tasador independiente. Cada comic es unico y viene con su ficha tecnica detallada (ano, editorial, numero, etc). Pieza de museo.',
            'price' => 890000,
        ],
        [
            'code'  => 'COL-004',
            'label' => 'Carta Coleccionable Rara Primera Edicion',
            'name'  => 'Carta Coleccionable Rara Primera Edicion — Calificada PSA',
            'short' => 'Carta coleccionable primera edicion calificada por PSA, grado mint.',
            'long'  => 'Carta coleccionable de primera edicion (TCG) en estado de conservacion mint, calificada por PSA (Professional Sports Authenticator). Grado de la calificadora adjunto, lo que garantiza su autenticidad y estado. Protector rigido y sleeve incluidos. Las cartas calificadas en alto grado son altamente buscadas por coleccionistas e inversores. Pieza unica, fotografia real del articulo disponible bajo solicitud. No se aceptan devoluciones una vez abierta la funda de sellado.',
            'price' => 89000,
        ],
        [
            'code'  => 'COL-005',
            'label' => 'Muneca Vintage Edicion Especial Anos 80',
            'name'  => 'Muñeca Vintage Edicion Especial Años 80 — Coleccionable',
            'short' => 'Muñeca vintage original de los años 80 en caja preservada.',
            'long'  => 'Muñeca coleccionable original de los años 80, completa y en muy buen estado de conservacion. Incluye caja original (puede tener signos de almacenamiento), manual, accesorios y el soporte de exposicion. Sin marcas de uso, cabello original trenzado, vestido autentico de la epoca. Pieza de nostalgia y coleccion para amantes de los recuerdos de los 80s. Certificada como original por tasador experto. Pieza unica, no se replica.',
            'price' => 1250000,
        ],
        [
            'code'  => 'COL-006',
            'label' => 'Postal Historica Antigua 1920s',
            'name'  => 'Postal Histórica Antigua Años 1920 — Pieza de Epoca',
            'short' => 'Postal autentica de los anos 1920, pieza de epoca bien conservada.',
            'long'  => 'Postal autentica de los anos 1920, enviada y recibida en su epoca (con matasellos y direccion manuscrita visibles). Estado de conservacion: bueno (puede tener bordes levemente gastados propios del tiempo, pero sin roturas ni perdidas). Imagen en blanco y negro o coloreada a mano segun disponibilidad. Cada postal es unica y cuenta una pequena historia. Ideal para coleccionistas de filatelia, ephemera historica o historia postal. Se entrega con funda protectora transparente.',
            'price' => 65000,
        ],
        [
            'code'  => 'COL-007',
            'label' => 'Miniatura Vehiculo Escala 1:18 Diecast',
            'name'  => 'Miniatura Vehículo Escala 1:18 Diecast — Colección Premium',
            'short' => 'Minuatura diecast escala 1:18 con detalles realistas, edicion coleccionista.',
            'long'  => 'Minuatura diecast (fundicion a presion) de vehiculo clasico o deportivo a escala 1:18, fabricada en metal con acabados de alta calidad. Puertas, capo y maletero funcionales (en la mayoria de modelos), direccion operable, suspension realista, detalles interiores visibles, llantas de aleacion con neumaticos de goma. Base de exhibicion con nombre del modelo incluida. Caja original con ventana transparente. Pieza premium para coleccionistas de automoviles a miniatura.',
            'price' => 275000,
        ],
    ];

    /**
     * Productos de marca propia para Artistas — cantantes, actores, etc.
     * Merchandising oficial y ediciones especiales firmadas.
     */
    private array $artistasProducts = [
        [
            'code'  => 'ART-001',
            'label' => 'Camiseta Luna Vega Tour 2025',
            'name'  => 'Camiseta Oficial Luna Vega Tour 2025 — Merch Oficial',
            'short' => 'Camiseta oficial del tour de Luna Vega 2025, edicion limitada.',
            'long'  => 'Camiseta oficial del nuevo tour de la cantante colombiana Luna Vega. Algodon 100% organico peinado de 180g, corte unisex relaxed fit, estampado en serigrafia de alta calidad con tintas al agua (ecologicas). Diseno exclusivo del tour con la tipografia caracteristica de la artista en el frente y la lista de ciudades en la espalda. Edicion limitada: solo 2.000 unidades worldwide. Cada camiseta viene con una tarjeta firmada por la artista y un codigo QR para acceder a contenido exclusivo (backstage, audio sin editar). Lavar en agua fria, del reves.',
            'price' => 159000,
        ],
        [
            'code'  => 'ART-002',
            'label' => 'Vinilo Andrés Maya — Acústico',
            'name'  => 'Vinilo Andrés Maya Acústico — Edicion Numerada 180g',
            'short' => 'Vinilo del album acustico de Andres Maya, edicion numerada 180g.',
            'long'  => 'Edicion en vinilo de 180 gramos del album acustico del cantautor colombiano Andres Maya. Grabado en directo en una sola toma en los estudios de Aimeos Records, prensado en vinilo negro de alta densidad con masterizacion analogica. Incluye 2 LPs con las 14 canciones del repertorio acustico, un booklet de 24 paginas con fotografias del making-of y las letras manuscritas por el artista. Edicion limitada y numerada (1.500 unidades worldwide) con certificado de autenticidad. Cada vinilo es una pieza unica para coleccionistas y fanaticos de la buena musica.',
            'price' => 289000,
        ],
        [
            'code'  => 'ART-003',
            'label' => 'Poster Firmado Camila Reyes',
            'name'  => 'Poster Autografiado Camila Reyes — Tour Mariposas',
            'short' => 'Poster firmado a mano por Camila Reyes, edicion limitada certificada.',
            'long'  => 'Poster oficial del Tour Mariposas de la cantante urbana Camila Reyes, firmado a mano por la artista y certificado de autenticidad por estudio independiente. Impreso en papel couche de 250g con terminacion mate, tamano 60x90 cm, incluye marco de madera natural hecho a mano. Cada poster esta numerado (1/500) y viene con un certificado holografico antifraude. Ideal para coleccionistas o para enmarcar y exhibir en tu rincon favorito. La firma se aplica en la esquina inferior derecha para no interferir con la imagen principal.',
            'price' => 195000,
        ],
        [
            'code'  => 'ART-004',
            'label' => 'Tote Bag Dante Orozco',
            'name'  => 'Tote Bag Oficial Dante Orozco — Lona Premium',
            'short' => 'Tote bag oficial del cantautor Dante Orozco, lona premium con forro interior.',
            'long'  => 'Tote bag oficial del cantautor Dante Orozco, fabricado en lona de algodon 100% de 12oz con forro interior en color crudo. Tamano generoso 38x42x12 cm con bolsillo interno con cierre, asas reforzadas en cinta de algodon de 2.5cm y remaches metalicos en los puntos de tension. Estampado serigrafico con el logo caracteristico del artista y la frase "Cancion sin tiempo" en la parte inferior. Perfecto para llevar libros, vinilos, el laptop o lo que necesites. Lavable a maquina en ciclo suave. Una pieza practica con diseno autentico.',
            'price' => 89000,
        ],
        [
            'code'  => 'ART-005',
            'label' => 'Gorra Sofía Cruz — Nueva Película',
            'name'  => 'Gorra Oficial Sofia Cruz Estreno Pelicula — Bordada',
            'short' => 'Gorra bordada edicion especial por el estreno de la nueva pelicula de Sofia Cruz.',
            'long'  => 'Gorra oficial de edicion especial lanzada por el estreno de la nueva pelicula de la actriz colombiana Sofia Cruz. Estructura trucker clasica con frente de algodon 100% y malla trasera transpirable, cierre ajustable con hebilla metalica. Logo de la pelicula bordado en 3D en el frente y la firma caracteristica de Sofia bordada en la lateral. Correa interior con la frase de la pelicula. Una pieza unica que conecta el cine con la moda, perfecta para llevar a las funciones, al dia a dia o como coleccion. Edicion numerada (1.000 unidades).',
            'price' => 119000,
        ],
        [
            'code'  => 'ART-006',
            'label' => 'Set Pulseras Luna Vega Official',
            'name'  => 'Set 3 Pulseras Luna Vega Official — Coleccion Gira',
            'short' => 'Set de 3 pulseras oficiales de la gira de Luna Vega, piedras naturales.',
            'long'  => 'Set de tres pulseras oficiales de la gira mundial de Luna Vega, cada una representa una cancion del nuevo album: "Luna Nueva" (cuarzo blanco), "Mar Adentro" (piedra luna) y "Brillo Eterno" (ojo de tigre). Cuentas de piedras naturales de 8mm ensartadas a mano en hilo elastico de alta resistencia, con dije de acero inoxidable grabado con el nombre de la cancion. Se pueden usar juntas o separadas. Caja de presentacion premium con el logo de la gira. Un regalo perfecto para cualquier fan de la artista o para coleccionar piezas con significado.',
            'price' => 145000,
        ],
    ];

    public function handle(): int
    {
        $this->now = now()->format('Y-m-d H:i:s');

        $influencersId    = $this->getCategoryId('influencers');
        $coleccionistasId = $this->getCategoryId('coleccionistas');
        $artistasId       = $this->getCategoryId('artistas');

        if (! $influencersId || ! $coleccionistasId || ! $artistasId) {
            $this->error('Faltan categorias especiales. Corre primero:');
            $this->error('  php artisan exi:seed-special-categories');
            return self::FAILURE;
        }

        $this->info('Importando productos de Influencers...');
        foreach ($this->influencersProducts as $p) {
            $this->importProduct($p, $influencersId);
        }

        $this->info('Importando productos de Coleccionistas...');
        foreach ($this->coleccionistasProducts as $p) {
            $this->importProduct($p, $coleccionistasId);
        }

        $this->info('Importando productos de Artistas...');
        foreach ($this->artistasProducts as $p) {
            $this->importProduct($p, $artistasId);
        }

        $this->info('Reconstruyendo indice de busqueda...');
        $this->call('aimeos:jobs', ['jobs' => 'index/rebuild']);

        $this->info('Productos de ejemplo listos.');

        return self::SUCCESS;
    }

    private function getCategoryId(string $code): ?int
    {
        $id = DB::table('mshop_catalog')
            ->where('siteid', $this->siteid)
            ->where('code', $code)
            ->value('id');

        return $id !== null ? (int) $id : null;
    }

    private function importProduct(array $p, int $catId): void
    {
        if (DB::table('mshop_product')->where('siteid', $this->siteid)->where('code', $p['code'])->exists()) {
            $this->warn("  Ya existe: {$p['code']} — omitiendo.");

            return;
        }

        $this->info("  Importando: {$p['label']} ({$p['code']})...");

        $productId = DB::table('mshop_product')->insertGetId([
            'siteid'  => $this->siteid,
            'dataset' => '',
            'type'    => 'default',
            'code'    => $p['code'],
            'label'   => $p['label'],
            'url'     => Str::slug($p['label']),
            'config'  => '{}',
            'start'   => null,
            'end'     => null,
            'scale'   => 1.0,
            'boost'   => 1.0,
            'rating'  => 0.00,
            'ratings' => 0,
            'instock' => 0,
            'target'  => '',
            'status'  => 1,
            'mtime'   => $this->now,
            'ctime'   => $this->now,
            'editor'  => 'cli-special-seed',
        ]);

        $nameId  = $this->createText('name',  $p['name'],  '');
        $shortId = $this->createText('short', '',          $p['short']);
        $longId  = $this->createText('long',  '',          $p['long']);

        $this->linkToProduct($productId, 'text', $nameId,  0);
        $this->linkToProduct($productId, 'text', $shortId, 1);
        $this->linkToProduct($productId, 'text', $longId,  2);

        $priceId = DB::table('mshop_price')->insertGetId([
            'siteid'     => $this->siteid,
            'type'       => 'default',
            'currencyid' => 'COP',
            'domain'     => 'product',
            'label'      => $p['label'],
            'quantity'   => 1,
            'value'      => number_format($p['price'], 2, '.', ''),
            'costs'      => '0.00',
            'rebate'     => '0.00',
            'taxrate'    => '{}',
            'status'     => 1,
            'mtime'      => $this->now,
            'ctime'      => $this->now,
            'editor'     => 'cli-special-seed',
        ]);
        $this->linkToProduct($productId, 'price', $priceId, 0);

        DB::table('mshop_stock')->insert([
            'siteid'      => $this->siteid,
            'type'        => 'default',
            'prodid'      => $productId,
            'stocklevel'  => 100,
            'backdate'    => null,
            'timeframe'   => '',
            'mtime'       => $this->now,
            'ctime'       => $this->now,
            'editor'      => 'cli-special-seed',
        ]);

        $key = "catalog|default|{$catId}";
        DB::table('mshop_product_list')->insert([
            'siteid'   => $this->siteid,
            'parentid' => $productId,
            'key'      => $key,
            'type'     => 'default',
            'domain'   => 'catalog',
            'refid'    => (string) $catId,
            'start'    => null,
            'end'      => null,
            'config'   => '{}',
            'pos'      => 0,
            'status'   => 1,
            'mtime'    => $this->now,
            'ctime'    => $this->now,
            'editor'   => 'cli-special-seed',
        ]);
    }

    private function createText(string $type, string $label, string $content): int
    {
        return DB::table('mshop_text')->insertGetId([
            'siteid'  => $this->siteid,
            'type'    => $type,
            'domain'  => 'product',
            'langid'  => 'es',
            'label'   => $label !== '' ? $label : substr($content, 0, 100),
            'content' => $content !== '' ? $content : $label,
            'status'  => 1,
            'mtime'   => $this->now,
            'ctime'   => $this->now,
            'editor'  => 'cli-special-seed',
        ]);
    }

    private function linkToProduct(int $productId, string $domain, int|string $refId, int $pos): void
    {
        $key = "{$domain}|default|{$refId}";
        DB::table('mshop_product_list')->insert([
            'siteid'   => $this->siteid,
            'parentid' => $productId,
            'key'      => $key,
            'type'     => 'default',
            'domain'   => $domain,
            'refid'    => (string) $refId,
            'start'    => null,
            'end'      => null,
            'config'   => '{}',
            'pos'      => $pos,
            'status'   => 1,
            'mtime'    => $this->now,
            'ctime'    => $this->now,
            'editor'   => 'cli-special-seed',
        ]);
    }
}
