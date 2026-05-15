<?php

return [

	'apc_enabled' => false,
	'apc_prefix' => 'laravel:',
	'num_formatter' => 'Locale',
	'pcntl_max' => 4,
	'version' => env( 'APP_VERSION', 1 ),
	'roles' => ['admin', 'editor'],
	'panel' => 'dashboard',
	'multishop' => env( 'SHOP_MULTISHOP', false ),
	'multilocale' => env( 'SHOP_MULTILOCALE', false ),

	'routes' => [
		// 'admin' => ['prefix' => 'admin', 'middleware' => ['web']],
		// 'jqadm' => ['prefix' => 'admin/{site}/jqadm', 'middleware' => ['web', 'auth']],
		// 'graphql' => ['prefix' => 'admin/{site}/graphql', 'middleware' => ['web', 'auth']],
		// 'jsonadm' => ['prefix' => 'admin/{site}/jsonadm', 'middleware' => ['web', 'auth']],
		// 'jsonapi' => ['prefix' => 'jsonapi', 'middleware' => ['web', 'api']],
		// 'account' => ['prefix' => 'profile', 'middleware' => ['web', 'auth']],
		// 'default' => ['prefix' => 'shop', 'middleware' => ['web']],
		// 'basket' => ['prefix' => 'shop', 'middleware' => ['web']],
		// 'checkout' => ['prefix' => 'shop', 'middleware' => ['web']],
		// 'confirm' => ['prefix' => 'shop', 'middleware' => ['web']],
		// 'supplier' => ['prefix' => 's', 'middleware' => ['web']],
		// 'page' => ['prefix' => 'p', 'middleware' => ['web']],
		// 'home' => ['middleware' => ['web']],
		// 'update' => [],
	],

	'page' => [
		'account-index' => ['locale/select', 'basket/mini', 'catalog/tree', 'catalog/search', 'account/profile', 'account/review', 'account/subscription', 'account/basket', 'account/history', 'account/favorite', 'account/watch', 'catalog/session'],
		'basket-index' => ['locale/select', 'catalog/tree', 'catalog/search', 'basket/standard', 'basket/bulk', 'basket/related'],
		'catalog-count' => ['catalog/count'],
		'catalog-detail' => ['locale/select', 'basket/mini', 'catalog/tree', 'catalog/search', 'catalog/stage', 'catalog/detail', 'catalog/session'],
		'catalog-home' => ['locale/select', 'basket/mini', 'catalog/tree', 'catalog/search', 'catalog/home'],
		'catalog-list' => ['locale/select', 'basket/mini', 'catalog/filter', 'catalog/tree', 'catalog/search', 'catalog/price', 'catalog/supplier', 'catalog/attribute', 'catalog/session', 'catalog/stage', 'catalog/lists'],
		'catalog-session' => ['locale/select', 'basket/mini', 'catalog/tree', 'catalog/search', 'catalog/session'],
		'catalog-stock' => ['catalog/stock'],
		'catalog-suggest' => ['catalog/suggest'],
		'catalog-tree' => ['locale/select', 'basket/mini', 'catalog/filter', 'catalog/tree', 'catalog/search', 'catalog/price', 'catalog/supplier', 'catalog/attribute', 'catalog/session', 'catalog/stage', 'catalog/lists'],
		'checkout-confirm' => ['catalog/tree', 'catalog/search', 'checkout/confirm'],
		'checkout-index' => ['locale/select', 'catalog/tree', 'catalog/search', 'checkout/standard'],
		'checkout-update' => ['checkout/update'],
		'supplier-detail' => ['locale/select', 'basket/mini', 'catalog/tree', 'catalog/search', 'supplier/detail', 'catalog/lists'],
		'cms' => ['cms/page', 'catalog/tree', 'basket/mini'],
	],

	'resource' => [
		'db' => [
			'adapter' => config( 'database.connections.' . config( 'database.default', 'mysql' ) . '.driver', 'mysql' ),
			'host' => config( 'database.connections.' . config( 'database.default', 'mysql' ) . '.host', '127.0.0.1' ),
			'port' => config( 'database.connections.' . config( 'database.default', 'mysql' ) . '.port', '3306' ),
			'socket' => config( 'database.connections.' . config( 'database.default', 'mysql' ) . '.unix_socket', '' ),
			'database' => config( 'database.connections.' . config( 'database.default', 'mysql' ) . '.database', 'exicompras' ),
			'username' => config( 'database.connections.' . config( 'database.default', 'mysql' ) . '.username', 'root' ),
			'password' => config( 'database.connections.' . config( 'database.default', 'mysql' ) . '.password', '' ),
			'stmt' => config( 'database.default', 'mysql' ) === 'mysql' ? ["SET SESSION sort_buffer_size=2097144; SET NAMES 'utf8mb4'; SET SESSION sql_mode='ANSI'"] : [],
			'limit' => 3,
			'defaultTableOptions' => [
				'charset' => config( 'database.connections.' . config( 'database.default', 'mysql' ) . '.charset' ),
				'collate' => config( 'database.connections.' . config( 'database.default', 'mysql' ) . '.collation' ),
			],
			'driverOptions' => config( 'database.connections.' . config( 'database.default', 'mysql' ) . '.options' ),
		],
		'fs' => [
			'adapter' => 'Standard',
			'tempdir' => storage_path( 'tmp' ),
			'basedir' => public_path(),
			'baseurl' => rtrim(env('ASSET_URL', PHP_SAPI == 'cli' ? env('APP_URL') : ''), '/'),
		],
		'fs-media' => [
			'adapter' => 'Standard',
			'tempdir' => storage_path( 'tmp' ),
			'basedir' => public_path( 'aimeos' ),
			'baseurl' => rtrim(env('ASSET_URL', PHP_SAPI == 'cli' ? env('APP_URL') : ''), '/') . '/aimeos',
		],
		'fs-mimeicon' => [
			'adapter' => 'Standard',
			'tempdir' => storage_path( 'tmp' ),
			'basedir' => public_path( 'vendor/shop/mimeicons' ),
			'baseurl' => rtrim(env('ASSET_URL', PHP_SAPI == 'cli' ? env('APP_URL') : ''), '/') . '/vendor/shop/mimeicons',
		],
		'fs-theme' => [
			'adapter' => 'Standard',
			'tempdir' => storage_path( 'tmp' ),
			'basedir' => public_path( 'vendor/shop/themes' ),
			'baseurl' => rtrim(env('ASSET_URL', PHP_SAPI == 'cli' ? env('APP_URL') : ''), '/') . '/vendor/shop/themes',
		],
		'fs-admin' => [
			'adapter' => 'Standard',
			'tempdir' => storage_path( 'tmp' ),
			'basedir' => storage_path( 'admin' ),
		],
		'fs-export' => [
			'adapter' => 'Standard',
			'tempdir' => storage_path( 'tmp' ),
			'basedir' => storage_path( 'export' ),
		],
		'fs-import' => [
			'adapter' => 'Standard',
			'tempdir' => storage_path( 'tmp' ),
			'basedir' => storage_path( 'import' ),
		],
		'fs-secure' => [
			'adapter' => 'Standard',
			'tempdir' => storage_path( 'tmp' ),
			'basedir' => storage_path( 'secure' ),
		],
		'mq' => [
			'adapter' => 'Standard',
			'db' => 'db',
		],
		'email' => [
			'from-email' => config( 'mail.from.address' ),
			'from-name' => config( 'mail.from.name' ),
		],
	],

	'admin' => [
		'jqadm' => [
			'resource' => [
				// El editor NO puede ver ni gestionar clientes/usuarios
				'customer' => ['groups' => ['admin', 'super']],
				'users'    => ['groups' => ['admin', 'super']],
			],
		],
	],

	'client' => [
		'html' => [
			'theme-presets' => [
				'default' => [
					// ── Frontend ──────────────────────────────
					'--ai-bg'            => '#1A1F36',
					'--ai-bg-alt'        => '#141828',
					'--ai-primary'       => '#F0F2FF',
					'--ai-primary-alt'   => '#A8B0CC',
					'--ai-secondary'     => '#FF6B35',
					'--ai-secondary-alt' => '#FF8C5A',
					'--ai-tertiary'      => '#4A7EFF',
					'--ai-tertiary-alt'  => '#7AA3FF',
					'--ai-danger'        => '#E53E3E',
					'--ai-success'       => '#38A169',
					'--ai-warning'       => '#D69E2E',
					'--ai-radius'        => '8',
					// ── Panel admin (navbar/sidebar) ──────────
					'--bs-menu-bg'       => '#1A1F36',
					'--bs-menu-alt-bg'   => '#141828',
					'--bs-menu'          => '#F0F2FF',
					'--bs-menu-alt'      => '#A8B0CC',
					// ── Navbar tienda ─────────────────────────
					'--ai-nav-bg'             => '#1A1F36',
					'--ai-nav-text'           => '#F0F2FF',
					'--ai-nav-text-hover'     => '#4A7EFF',
					'--ai-nav-icon'           => '#F0F2FF',
					'--ai-nav-dropdown-bg'    => '#1A1F36',
					'--ai-nav-dropdown-text'  => '#F0F2FF',
				],
			],
			'basket' => [
				'cache' => [],
			],
			'common' => [
				'cache' => [],
			],
			'catalog' => [
				'lists' => [
					'basket-add' => true,
				],
				'selection' => [
					'type' => [
						'color' => 'radio',
						'length' => 'radio',
						'width' => 'radio',
					],
				],
			],
		],
	],

	'controller' => [
		'frontend' => [
			'catalog' => [
				'levels-always' => 3
			]
		]
	],

	// Traducciones del frontend de Aimeos en español (es)
	'i18n' => [
		'es' => [],   // Aimeos tiene traducciones ES nativas incluidas
	],

	'madmin' => [
		'cache' => [
			'manager' => [
				// 'name' => 'None',
			],
		],
		'log' => [
			'manager' => [
				// 'loglevel' => 7,
			],
		],
	],

	'mshop' => [
		'locale' => [
			// 'site' => 'default',
		]
	],

	'command' => [],

	'frontend' => [],

	'backend' => [],

];
