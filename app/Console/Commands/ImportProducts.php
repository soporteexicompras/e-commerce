<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportProducts extends Command
{
    protected $signature = 'exi:import-products {--fix-bee-venom : Only fix Bee Venom texts/links}';
    protected $description = 'Import real products from public/Productos de prueba/ into Aimeos';

    private $context;
    private $siteid = '1.';
    private $now;

    // ── Category IDs (filled during run) ──────────────────────────────────
    private int $catBelleza   = 6;
    private int $catSalud     = 0;  // created at runtime

    // ── Product data ───────────────────────────────────────────────────────
    private array $products = [];

    public function handle(): int
    {
        $this->now = now()->format('Y-m-d H:i:s');

        // 1. Bootstrap Aimeos context
        $this->context = app('aimeos.context')->get(false);
        $localeManager = \Aimeos\MShop::create($this->context, 'locale');
        $localeItem    = $localeManager->bootstrap('default', 'es', 'COP', false);
        $this->context->setLocale($localeItem);

        // 2. Ensure "Salud y Suplementos" category exists
        $this->catSalud = $this->ensureSaludCategory();
        $this->info("Categoría Salud y Suplementos id={$this->catSalud}");

        // 3. Define product catalogue
        $this->buildProductData();

        // 4. Fix Bee Venom (already in DB as BELL-003, id=15)
        $this->fixBeeVenom();

        if ($this->option('fix-bee-venom')) {
            $this->info('--fix-bee-venom solo. Terminando.');
            return 0;
        }

        // 5. Import each new product
        foreach ($this->products as $p) {
            $this->importProduct($p);
        }

        // 6. Rebuild search index
        $this->info('Reconstruyendo índice de búsqueda...');
        $this->call('aimeos:jobs', ['jobs' => 'index/rebuild']);

        $this->info('¡Importación completada!');
        return 0;
    }

    // ──────────────────────────────────────────────────────────────────────
    //  Category helpers
    // ──────────────────────────────────────────────────────────────────────

    private function ensureSaludCategory(): int
    {
        $existing = DB::table('mshop_catalog')
            ->where('siteid', $this->siteid)
            ->where('code', 'salud')
            ->value('id');

        if ($existing) {
            return (int) $existing;
        }

        $catalogManager = \Aimeos\MShop::create($this->context, 'catalog');

        $item = $catalogManager->create();
        $item->setCode('salud')
             ->setLabel('Salud y Suplementos')
             ->setUrl('salud-y-suplementos')
             ->setStatus(1);

        // Insert as child of Home (id=1)
        $item = $catalogManager->insert($item, '1');

        $this->info("Categoría 'Salud y Suplementos' creada con id={$item->getId()}");
        return (int) $item->getId();
    }

    // ──────────────────────────────────────────────────────────────────────
    //  Fix Bee Venom (product id=15, code=BELL-003)
    // ──────────────────────────────────────────────────────────────────────

    private function fixBeeVenom(): void
    {
        $productId = 15;

        // Check existing text types
        $existingTextTypes = DB::table('mshop_text as t')
            ->join('mshop_product_list as pl', function ($j) use ($productId) {
                $j->on('pl.refid', '=', 't.id')
                  ->where('pl.domain', '=', 'text')
                  ->where('pl.parentid', '=', $productId);
            })
            ->pluck('t.type')
            ->toArray();

        if (!in_array('name', $existingTextTypes)) {
            $nameId = $this->createText('name', 'Bee Venom Crema Facial Reafirmante Antiedad', '');
            $this->linkToProduct($productId, 'text', $nameId, 0);
            $this->info('Bee Venom: texto name añadido');
        }

        if (!in_array('short', $existingTextTypes)) {
            $shortId = $this->createText('short', '', 'Crema antiedad con veneno de abeja cosmético para piel firme, hidratada y luminosa');
            $this->linkToProduct($productId, 'text', $shortId, 1);
            $this->info('Bee Venom: texto short añadido');
        }

        // Ensure catalog_list entry (belleza, id=6)
        $clExists = DB::table('mshop_catalog_list')
            ->where('siteid', $this->siteid)
            ->where('parentid', $this->catBelleza)
            ->where('domain', 'product')
            ->where('refid', $productId)
            ->exists();

        if (!$clExists) {
            $this->insertCatalogList($this->catBelleza, $productId);
            $this->info('Bee Venom: mshop_catalog_list (belleza) añadido');
        }
    }

    // ──────────────────────────────────────────────────────────────────────
    //  Single product import
    // ──────────────────────────────────────────────────────────────────────

    private function importProduct(array $p): void
    {
        // Skip if already exists
        if (DB::table('mshop_product')->where('siteid', $this->siteid)->where('code', $p['code'])->exists()) {
            $this->warn("Ya existe: {$p['code']} — omitiendo.");
            return;
        }

        $this->info("Importando: {$p['label']} ({$p['code']})...");

        // 1. Product
        $urlSlug = \Aimeos\Base\Str::slug($p['label']);
        $productId = DB::table('mshop_product')->insertGetId([
            'siteid'  => $this->siteid,
            'dataset' => '',
            'type'    => 'default',
            'code'    => $p['code'],
            'label'   => $p['label'],
            'url'     => $urlSlug,
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
            'editor'  => 'cli-import',
        ]);

        // 2. Texts
        $nameId  = $this->createText('name',  $p['name'],  '');
        $shortId = $this->createText('short', '',          $p['short']);
        $longId  = $this->createText('long',  '',          $p['long']);

        $this->linkToProduct($productId, 'text', $nameId,  0);
        $this->linkToProduct($productId, 'text', $shortId, 1);
        $this->linkToProduct($productId, 'text', $longId,  2);

        // 3. Price
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
            'editor'     => 'cli-import',
        ]);
        $this->linkToProduct($productId, 'price', $priceId, 0);

        // 4. Media (images)
        $pos = 0;
        foreach ($p['images'] as $srcPath) {
            if (!file_exists($srcPath)) {
                $this->warn("  Imagen no encontrada: $srcPath");
                continue;
            }
            $mediaId = $this->importMediaFile($srcPath);
            if ($mediaId) {
                $this->linkToProduct($productId, 'media', $mediaId, $pos++);
            }
        }

        // 5. Stock
        DB::table('mshop_stock')->insert([
            'siteid'      => $this->siteid,
            'type'        => 'default',
            'prodid'      => $productId,
            'stocklevel'  => 100,
            'backdate'    => null,
            'timeframe'   => '',
            'mtime'       => $this->now,
            'ctime'       => $this->now,
            'editor'      => 'cli-import',
        ]);

        // 6. Catalog links (both directions)
        $catId = $p['catalog'];
        $this->linkToProduct($productId, 'catalog', $catId, 0);
        $this->insertCatalogList($catId, $productId);

        $this->info("  ✓ {$p['code']} importado (id=$productId, {$pos} imágenes)");
    }

    // ──────────────────────────────────────────────────────────────────────
    //  Media import with Aimeos thumbnail generation
    // ──────────────────────────────────────────────────────────────────────

    private function importMediaFile(string $srcPath): ?int
    {
        $mediaManager = \Aimeos\MShop::create($this->context, 'media');
        $fs           = $this->context->fs('fs-media');
        $basename     = basename($srcPath);

        // Generate path using Aimeos algorithm
        $slug    = \Aimeos\Base\Str::slug(substr($basename, 0, strrpos($basename, '.') ?: null));
        $hash    = substr(md5($slug . getmypid() . microtime(true)), -8);
        $fname   = $hash . '_' . $slug;
        $destPath = "{$this->siteid}d/product/{$fname[0]}/{$fname[1]}/{$fname}.webp";

        // Ensure directory exists
        $dir = dirname($destPath);
        if (method_exists($fs, 'mkdir') && !$fs->has($dir)) {
            try { $fs->mkdir($dir); } catch (\Exception $e) {}
        }

        // Write file to filesystem
        $fs->write($destPath, file_get_contents($srcPath));

        // Create media item
        $mediaItem = $mediaManager->create([
            'media.domain'   => 'product',
            'media.type'     => 'default',
            'media.label'    => $basename,
            'media.mimetype' => 'image/webp',
            'media.fsname'   => 'fs-media',
            'media.url'      => $destPath,
            'media.status'   => 1,
            'media.langid'   => null,
        ]);

        // Generate thumbnail previews
        try {
            $mediaItem = $mediaManager->scale($mediaItem, true);
        } catch (\Exception $e) {
            $this->warn("  Preview error para $basename: " . $e->getMessage());
        }

        // Save media
        $mediaItem = $mediaManager->save($mediaItem);

        return (int) $mediaItem->getId();
    }

    // ──────────────────────────────────────────────────────────────────────
    //  DB helpers
    // ──────────────────────────────────────────────────────────────────────

    private function createText(string $type, string $label, string $content): int
    {
        return DB::table('mshop_text')->insertGetId([
            'siteid'  => $this->siteid,
            'type'    => $type,
            'domain'  => 'product',
            'langid'  => 'es',
            'label'   => $label ?: substr($content, 0, 100),
            'content' => $content ?: $label,
            'status'  => 1,
            'mtime'   => $this->now,
            'ctime'   => $this->now,
            'editor'  => 'cli-import',
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
            'editor'   => 'cli-import',
        ]);
    }

    private function insertCatalogList(int $catId, int $productId): void
    {
        // Get next pos for this catalog
        $pos = DB::table('mshop_catalog_list')
            ->where('parentid', $catId)
            ->where('domain', 'product')
            ->count();

        $key = "product|default|{$productId}";

        DB::table('mshop_catalog_list')->insert([
            'siteid'   => $this->siteid,
            'parentid' => $catId,
            'key'      => $key,
            'type'     => 'default',
            'domain'   => 'product',
            'refid'    => (string) $productId,
            'start'    => null,
            'end'      => null,
            'config'   => '{}',
            'pos'      => $pos,
            'status'   => 1,
            'mtime'    => $this->now,
            'ctime'    => $this->now,
            'editor'   => 'cli-import',
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────
    //  Product catalogue definition
    // ──────────────────────────────────────────────────────────────────────

    private function buildProductData(): void
    {
        $base = public_path('Productos de prueba');

        $this->products = [

            // ── Salud y Suplementos ─────────────────────────────────────

            [
                'code'    => 'SALUD-001',
                'label'   => 'BiQ FEL Bebida Energizante Natural',
                'name'    => 'BiQ FEL Bebida Energizante Natural',
                'short'   => 'Bebida energizante con ingredientes naturales para rendimiento, vitalidad y bienestar.',
                'long'    => 'BiQ FEL es una innovadora bebida energizante formulada con ingredientes de origen natural seleccionados por su eficacia y seguridad. A diferencia de las bebidas energéticas convencionales, BiQ FEL no contiene estimulantes artificiales en exceso. Proporciona energía sostenida para el rendimiento físico e intelectual, ayudando a combatir la fatiga y mejorar la concentración. Ideal para deportistas, profesionales y personas activas que buscan una fuente de energía natural y equilibrada.',
                'price'   => 95000,
                'catalog' => 0, // will be set to catSalud below
                'images'  => $this->globImages($base . '/BiQ FEL - Bebida Energizante', 'BiQ FEL - Bebida Energizante *.webp'),
            ],

            [
                'code'    => 'SALUD-002',
                'label'   => 'Calmify Joyspring Suplemento Bienestar',
                'name'    => 'Calmify Joyspring Suplemento para el Bienestar',
                'short'   => 'Suplemento natural para el manejo del estrés, calma mental y bienestar emocional.',
                'long'    => 'Calmify Joyspring es un suplemento nutricional formulado para apoyar el equilibrio emocional y la calma mental. Con una combinación de ingredientes naturales respaldados por la ciencia, este producto ayuda a reducir los niveles de estrés percibido, mejorar el estado de ánimo y promover una sensación general de bienestar. Está especialmente recomendado para personas que experimentan estrés cotidiano, ansiedad leve o dificultades para relajarse. Su fórmula suave y natural lo hace adecuado para uso diario.',
                'price'   => 120000,
                'catalog' => 0,
                'images'  => $this->globImages($base . '/Calmify Joyspring', 'Calmify Joyspring *.webp'),
            ],

            [
                'code'    => 'SALUD-003',
                'label'   => 'Calostro Bovino Softgel con INVIMA',
                'name'    => 'Calostro Bovino Softgel con INVIMA',
                'short'   => 'Suplemento inmunológico de calostro bovino en cápsulas softgel, aprobado por INVIMA.',
                'long'    => 'El Calostro Bovino Softgel es un suplemento de alta calidad elaborado a partir del calostro bovino, la primera leche producida por las vacas inmediatamente después del parto. Rico en inmunoglobulinas, factores de crecimiento y componentes bioactivos, el calostro bovino ha sido ampliamente estudiado por sus beneficios en la salud inmunológica, digestiva y de recuperación física. Este producto cuenta con registro INVIMA, garantizando su calidad y seguridad. Cada cápsula softgel proporciona una dosis precisa y estandarizada de los componentes activos del calostro.',
                'price'   => 150000,
                'catalog' => 0,
                'images'  => $this->globImages($base . '/Calostro Bovino Softgel Con Invima', 'Calostro Bovino Softgel Con Invima *.webp'),
            ],

            [
                'code'    => 'SALUD-004',
                'label'   => 'DEOS Body Deodorizing Supplement',
                'name'    => 'DEOS Body Deodorizing Supplement',
                'short'   => 'Suplemento oral para control del olor corporal desde adentro hacia afuera.',
                'long'    => 'DEOS es un suplemento innovador que actúa desde el interior del organismo para neutralizar los compuestos causantes del olor corporal. A diferencia de los desodorantes convencionales de aplicación externa, DEOS actúa a nivel metabólico para reducir la producción de compuestos odoríferos a través del sudor, el aliento y otras secreciones corporales. Formulado con ingredientes naturales y activos específicos, proporciona una solución integral y duradera para el control del olor corporal. Ideal para personas activas, deportistas y quienes buscan una solución más efectiva y duradera que los desodorantes tradicionales.',
                'price'   => 80000,
                'catalog' => 0,
                'images'  => array_merge(
                    $this->globImages($base . '/Deos Deodorizing Supplement', 'Deos Deodorizing Supplement *.webp'),
                    $this->globImages($base . "/DEOS \xe2\x80\x93 BODY DEODORIZING SUPPLEMENT", "DEOS \xe2\x80\x93 BODY DEODORIZING SUPPLEMENT *.webp")
                ),
            ],

            [
                'code'    => 'SALUD-005',
                'label'   => 'Fumarex Spray Ayuda a Dejar de Fumar',
                'name'    => 'Fumarex Spray — Ayuda a Dejar de Fumar',
                'short'   => 'Spray oral para reducir el deseo de fumar y apoyar la cesación tabáquica.',
                'long'    => 'Fumarex Spray es un spray oral formulado con ingredientes naturales que ayuda a las personas a reducir el deseo de fumar y a manejar los síntomas del síndrome de abstinencia de nicotina. Su modo de acción consiste en aliviar la sensación de ansiedad asociada al deseo de fumar, facilitar la desintoxicación gradual y apoyar el proceso de cesación tabáquica sin los efectos secundarios de los parches de nicotina o medicamentos de prescripción. De fácil uso, se aplica directamente en la boca cuando surge el deseo de fumar. Complemento ideal para programas de desintoxicación del tabaco.',
                'price'   => 130000,
                'catalog' => 0,
                'images'  => $this->globImages($base . '/Fumarex Spray', 'Fumarex Spray *.webp'),
            ],

            [
                'code'    => 'SALUD-006',
                'label'   => 'Magnesium Complex Suplemento de Magnesio',
                'name'    => 'Magnesium Complex — Suplemento de Magnesio',
                'short'   => 'Complejo de magnesio de alta absorción para músculos, nervios y huesos fuertes.',
                'long'    => 'El Magnesium Complex combina múltiples formas queladas de magnesio para garantizar la máxima biodisponibilidad y absorción. El magnesio es un mineral esencial involucrado en más de 300 reacciones bioquímicas del organismo: regula la función muscular y nerviosa, apoya la salud ósea, controla los niveles de azúcar en sangre y contribuye a la producción de energía. Este complejo es especialmente beneficioso para personas con calambres musculares, insomnio, estrés o déficit de magnesio. Su fórmula optimizada garantiza tolerancia digestiva y absorción eficiente.',
                'price'   => 75000,
                'catalog' => 0,
                'images'  => $this->globImages($base . '/Magnesium Complex', 'Magnesium Complex *.webp'),
            ],

            [
                'code'    => 'SALUD-007',
                'label'   => 'NAD+ Resveratrol Antienvejecimiento',
                'name'    => 'NAD+ Resveratrol — Suplemento Antienvejecimiento',
                'short'   => 'Suplemento premium con NAD+ y Resveratrol para longevidad y energía celular.',
                'long'    => 'La combinación de NAD+ (Nicotinamida Adenina Dinucleótido) y Resveratrol representa uno de los avances más prometedores en la ciencia del antienvejecimiento. El NAD+ es un cofactor esencial para la producción de energía celular y la activación de las sirtuinas, proteínas asociadas a la longevidad. El Resveratrol, un polifenol presente en la uva roja, amplifica los beneficios del NAD+ y aporta poderosas propiedades antioxidantes y antiinflamatorias. Juntos, estos compuestos apoyan la regeneración celular, mejoran la energía, la cognición y el bienestar general, siendo considerados pioneros en la medicina antienvejecimiento.',
                'price'   => 200000,
                'catalog' => 0,
                'images'  => $this->globImages($base . '/NAD + RESVERATROL', 'NAD + RESVERATROL *.webp'),
            ],

            [
                'code'    => 'SALUD-008',
                'label'   => 'Neocell Collagen Suplemento de Colágeno',
                'name'    => 'Neocell Collagen — Suplemento de Colágeno Hidrolizado',
                'short'   => 'Colágeno hidrolizado premium para articulaciones, piel y bienestar general.',
                'long'    => 'Neocell Collagen es un suplemento de colágeno hidrolizado de alta calidad que proporciona los aminoácidos esenciales para la salud de articulaciones, piel, cabello y uñas. El colágeno hidrolizado de Neocell ha sido procesado mediante hidrólisis enzimática para obtener péptidos de bajo peso molecular que el organismo puede absorber y utilizar con mayor eficiencia. Estudios clínicos respaldan su capacidad para mejorar la elasticidad de la piel, reducir las líneas de expresión, fortalecer las articulaciones y apoyar la movilidad. Ideal para personas mayores de 30 años o cualquiera que desee proteger y regenerar su tejido conectivo.',
                'price'   => 180000,
                'catalog' => 0,
                'images'  => $this->globImages($base . '/Neocell Collagen', 'Neocell Collagen *.webp'),
            ],

            // ── Belleza y Cuidado ───────────────────────────────────────

            [
                'code'    => 'BELL-004',
                'label'   => 'Mercilen Crema Contorno de Ojos',
                'name'    => 'Mercilen — Crema Contorno de Ojos',
                'short'   => 'Crema especializada para el contorno de ojos, reduce ojeras y líneas finas.',
                'long'    => 'La Crema Contorno de Ojos Mercilen está especialmente formulada para la delicada piel del área periocular. Con activos humectantes, antioxidantes y tensores de última generación, esta crema ayuda a reducir la apariencia de ojeras, bolsas y líneas de expresión. Su textura ligera y no grasa facilita la absorción inmediata sin obstruir los poros. Formulada dermatológicamente para la sensibilidad del área ocular, es apta para todo tipo de piel. Con uso regular, proporciona una mirada más descansada, luminosa y rejuvenecida.',
                'price'   => 85000,
                'catalog' => $this->catBelleza,
                'images'  => $this->globImages($base . '/Crema Contorno de Ojos Mercilen', 'Crema Contorno de Ojos Mercilen *.webp'),
            ],

            [
                'code'    => 'BELL-005',
                'label'   => 'Mercilen Crema Facial Reafirmante',
                'name'    => 'Mercilen — Crema Facial Reafirmante',
                'short'   => 'Crema facial reafirmante con activos naturales para piel firme y luminosa.',
                'long'    => 'La Crema Facial Reafirmante Mercilen combina activos naturales de alta eficacia para mejorar la firmeza, elasticidad y luminosidad de la piel. Su fórmula enriquecida con péptidos bioactivos, ácido hialurónico y extractos vegetales estimula la producción natural de colágeno y elastina, contribuyendo a una piel más firme y joven. La textura sedosa se absorbe rápidamente dejando la piel suave, hidratada y radiante. Indicada para pieles maduras o con pérdida de tonicidad, puede usarse en el rostro, cuello y escote tanto en la rutina de la mañana como en la de noche.',
                'price'   => 110000,
                'catalog' => $this->catBelleza,
                'images'  => $this->globImages($base . '/Crema Facial Reafirmante Mercilen', 'Crema Facial Reafirmante Mercilen *.webp'),
            ],

            [
                'code'    => 'BELL-006',
                'label'   => 'Pasta Dental Nano-Hidroxiapatita 7.5%',
                'name'    => 'Pasta Dental Nano-Hidroxiapatita 7.5%',
                'short'   => 'Pasta dental con nano-hidroxiapatita para remineralización y blanqueamiento natural.',
                'long'    => 'La Pasta Dental con Nano-Hidroxiapatita al 7.5% es una innovadora fórmula dental que utiliza partículas de hidroxiapatita a escala nanométrica, idénticas al mineral que compone el esmalte dental, para remineralizar y fortalecer los dientes de forma natural. A diferencia del flúor, la nano-hidroxiapatita no es tóxica y puede usarse incluso en niños pequeños. Sella los túbulos dentinales expuestos, reduciendo la sensibilidad dental. Proporciona un suave efecto blanqueador al rellenar micro-imperfecciones del esmalte. Libre de flúor, SLS y parabenos, es la elección de quienes buscan una higiene bucal natural y efectiva.',
                'price'   => 65000,
                'catalog' => $this->catBelleza,
                'images'  => $this->globImages($base . '/Nano -Hidroxiapatita 7.5 Pasta Dental', 'Nano -Hidroxiapatita 7.5 Pasta Dental *.webp'),
            ],
        ];

        // Assign catSalud to products with catalog=0
        foreach ($this->products as &$p) {
            if ($p['catalog'] === 0) {
                $p['catalog'] = $this->catSalud;
            }
        }
        unset($p);
    }

    // ──────────────────────────────────────────────────────────────────────
    //  Helpers
    // ──────────────────────────────────────────────────────────────────────

    private function globImages(string $dir, string $pattern): array
    {
        if (!is_dir($dir)) {
            return [];
        }
        $files = glob($dir . DIRECTORY_SEPARATOR . $pattern);
        sort($files);
        return $files ?: [];
    }
}
