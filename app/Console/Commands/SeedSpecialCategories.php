<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Aimeos\MShop\ContextIface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeedSpecialCategories extends Command
{
    protected $signature = 'exi:seed-special-categories {--bump-theme : Bump theme_version after seeding}';

    protected $description = 'Crea (idempotente) las categorias especiales: Influencers, Coleccionistas y Artistas';

    private string $siteid = '1.';

    public function handle(): int
    {
        $context = app('aimeos.context')->get(false);
        $localeManager = \Aimeos\MShop::create($context, 'locale');
        $localeItem = $localeManager->bootstrap('default', 'es', 'COP', false);
        $context->setLocale($localeItem);

        $this->ensureCategory(
            $context,
            code: 'influencers',
            label: 'Influencers',
            url: 'influencers',
        );

        $this->ensureCategory(
            $context,
            code: 'coleccionistas',
            label: 'Coleccionistas',
            url: 'coleccionistas',
        );

        $this->ensureCategory(
            $context,
            code: 'artistas',
            label: 'Artistas',
            url: 'artistas',
        );

        if ($this->option('bump-theme')) {
            $site = $context->locale()->getSiteItem();
            $site->setConfigValue('theme_version', time());
            $siteManager = \Aimeos\MShop::create($context, 'locale/site');
            $siteManager->save($site);
            $this->info('theme_version bumped.');
        }

        $this->info('Categorias especiales listas.');

        return self::SUCCESS;
    }

    private function ensureCategory(ContextIface $context, string $code, string $label, string $url): void
    {
        $existing = DB::table('mshop_catalog')
            ->where('siteid', $this->siteid)
            ->where('code', $code)
            ->first();

        if ($existing) {
            $this->line("  '$label' ya existe (id={$existing->id}).");

            return;
        }

        $catalogManager = \Aimeos\MShop::create($context, 'catalog');
        $item = $catalogManager->create();
        $item->setCode($code)
             ->setLabel($label)
             ->setUrl($url)
             ->setStatus(1);

        $item = $catalogManager->insert($item, '1');

        $this->info("  '$label' creada (id={$item->getId()}, parent=Home).");
    }
}
