<?php

/**
 * Exicompras — catalog filter wrapper with mobile bottom-sheet drawer.
 *
 * Detection: this body.php is rendered for TWO contexts:
 *   1. Navbar search box (only `catalog/filter/search` block populated)
 *   2. Sidebar filter (search + price + supplier + attribute blocks populated)
 *
 * We render the drawer UI (trigger + backdrop + drawer + script) ONLY in
 * context #2 to avoid breaking the navbar search.
 *
 * Desktop (lg+): renders inline as before (sidebar).
 * Mobile (<lg):  fixed bottom sheet, opened via the trigger button.
 *                Vanilla JS handles open/close via data attributes.
 *                ESC + tap-backdrop + close-button + submit all close it.
 */

$enc = $this->encoder();
$multi = $this->config( 'client/html/catalog/multiroute', false );
$linkKey = $multi && $this->param( 'path' ) || $this->param( 'f_catid' ) ? 'client/html/catalog/tree/url' : 'client/html/catalog/lists/url';
$params = map( $this->param() )->only( ['path', 'f_catid', 'f_name', 'f_search'] );

if( $catid = $this->config( 'client/html/catalog/filter/tree/startid' ) ) {
	$params = $params->union( ['f_catid' => $catid] );
}

$hasPrice       = (int) $this->get( 'priceHigh', 0 ) > 0;
$hasSupplier    = !empty( $this->get( 'supplierList', [] ) );
$hasAttribute   = !empty( $this->get( 'attributeMap', [] ) );
$hasFilters     = $hasPrice || $hasSupplier || $hasAttribute;

$jsonUrl = $enc->attr( $this->link( 'client/jsonapi/url' ) );
$action  = $enc->attr( $this->link( $linkKey, $params->all() ) );

?>
<?php if( $hasFilters ) : ?>

	<div class="section aimeos catalog-filter exi-filter-root" data-jsonurl="<?= $jsonUrl ?>">

		<button type="button"
			class="exi-filter-trigger"
			data-exi-filter-open
			aria-controls="exi-filter-drawer"
			aria-expanded="false">
			<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
				<polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
			</svg>
			<span>Filtros</span>
		</button>

		<div class="exi-filter-backdrop"
			data-exi-filter-close
			aria-hidden="true"></div>

		<nav class="container-xxl exi-filter-nav">
			<form method="GET" action="<?= $action ?>" class="exi-filter-form">

				<?php foreach( map( $this->param() )->only( ['f_sort', 'l_type'] ) as $name => $value ) : ?>
					<input type="hidden" name="<?= $enc->attr( $this->formparam( $name ) ) ?>" value="<?= $enc->attr( $value ) ?>">
				<?php endforeach ?>

				<div class="exi-filter-drawer"
					id="exi-filter-drawer"
					role="dialog"
					aria-modal="false"
					aria-labelledby="exi-filter-title">

					<div class="exi-filter-drawer-header">
						<span class="exi-filter-handle" aria-hidden="true"></span>
<h2 id="exi-filter-title" class="exi-filter-drawer-title">
						Filtros
					</h2>
					<button type="button"
						class="exi-filter-close"
						data-exi-filter-close
						aria-label="Cerrar filtros">
							<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
								<path d="M18 6L6 18M6 6l12 12"/>
							</svg>
						</button>
					</div>

					<div class="exi-filter-drawer-body">
						<?= $this->block()->get( 'catalog/filter/tree' ) ?>
						<?= $this->block()->get( 'catalog/filter/price' ) ?>
						<?= $this->block()->get( 'catalog/filter/attribute' ) ?>
					</div>

					<div class="exi-filter-drawer-footer">
						<a class="exi-btn exi-btn-ghost"
							href="<?= $enc->attr( $this->link( $linkKey, $this->get( 'filterResetParams', $params->all() ) ) ) ?>"
							data-exi-filter-close>
							Limpiar todo
						</a>
						<button type="submit"
							class="exi-btn exi-btn-primary"
							data-exi-filter-close>
							Ver resultados
						</button>
					</div>

				</div>

			</form>
		</nav>

	</div>

	<script>
	(function() {
		document.querySelectorAll('.exi-filter-root').forEach(function(root) {
			var drawer  = root.querySelector('.exi-filter-drawer');
			var trigger = root.querySelector('[data-exi-filter-open]');
			if (!drawer || !trigger) return;

			var body = document.body;

			function open() {
				drawer.classList.add('is-open');
				root.classList.add('is-open');
				trigger.setAttribute('aria-expanded', 'true');
				drawer.setAttribute('aria-modal', 'true');
				body.style.overflow = 'hidden';
			}

			function close() {
				drawer.classList.remove('is-open');
				root.classList.remove('is-open');
				trigger.setAttribute('aria-expanded', 'false');
				drawer.setAttribute('aria-modal', 'false');
				body.style.overflow = '';
			}

			trigger.addEventListener('click', open);

			root.addEventListener('click', function(e) {
				var t = e.target.closest('[data-exi-filter-close]');
				if (t) close();
			});

			document.addEventListener('keydown', function(e) {
				if (e.key === 'Escape' && drawer.classList.contains('is-open')) close();
			});
		});
	})();

		// Exicompras: collapse sidebar sections (header-name rows + exi-section-header)
		// Only collapses sections that opted-in via data-exi-toggle="next" or are
		// inside a desktop sidebar. The drawer keeps sections always open.
		(function() {
			var STORAGE_KEY = 'exi_filter_collapsed';
			var mem = {};
			try { mem = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}') || {}; } catch(e) {}

			// Selector applies to .exi-section-header (attribute-body override)
			// AND to .header-name (price-body override) — but only outside the
			// drawer so the mobile UI always shows content.
			var heads = document.querySelectorAll(
				'.exicatalog-aside .exi-section-header,' +
				'.exicatalog-aside .section > .header-name'
			);

			heads.forEach(function(head) {
				var key = head.textContent.trim();
				head.setAttribute('role', 'button');
				head.setAttribute('tabindex', '0');

				// Find the content to collapse — siblings of the header
				var next = head.nextElementSibling;
				if (!next || !(next.classList.contains('exi-price-lists') || next.classList.contains('exi-attr-lists') || next.classList.contains('price-lists') || next.classList.contains('attribute-lists'))) {
					return;
				}

				function apply(state) {
					if (state) {
						head.classList.add('is-collapsed');
						next.classList.add('is-collapsed');
						head.setAttribute('aria-expanded', 'false');
					} else {
						head.classList.remove('is-collapsed');
						next.classList.remove('is-collapsed');
						head.setAttribute('aria-expanded', 'true');
					}
				}
				apply(!!mem[key]);

				function toggle() {
					var isColl = head.classList.contains('is-collapsed');
					var newState = !isColl;
					apply(newState);
					mem[key] = newState;
					try { localStorage.setItem(STORAGE_KEY, JSON.stringify(mem)); } catch(e) {}
				}
				head.addEventListener('click', toggle);
				head.addEventListener('keydown', function(e) {
					if (e.key === 'Enter' || e.key === ' ') {
						e.preventDefault();
						toggle();
					}
				});
			});
		})();
	</script>

<?php else : ?>

	<div class="section aimeos catalog-filter" data-jsonurl="<?= $jsonUrl ?>">
		<nav class="container-xxl">
			<form method="GET" action="<?= $action ?>">
				<?= $this->block()->get( 'catalog/filter/tree' ) ?>
				<?= $this->block()->get( 'catalog/filter/price' ) ?>
				<?= $this->block()->get( 'catalog/filter/attribute' ) ?>
			</form>
		</nav>
	</div>

<?php endif ?>