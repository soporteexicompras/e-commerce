<?php

/**
 * Exicompras — supplier filter, mobile-friendly layout.
 * Large search input + 20px touch-friendly checkboxes + 44px row min-height.
 */

$enc = $this->encoder();
$multi = $this->config( 'client/html/catalog/multiroute', false );
$linkKey = $multi && $this->param( 'path' ) || $this->param( 'f_catid' ) ? 'client/html/catalog/tree/url' : 'client/html/catalog/lists/url';

$supplierList = $this->get( 'supplierList', [] );
$selectedIds  = (array) $this->param( 'f_supid', [] );

?>
<?php $this->block()->start( 'catalog/filter/supplier' ) ?>
<div class="section catalog-filter-supplier exi-supplier"
	aria-label="<?= $enc->attr( $this->translate( 'client', 'Supplier list' ) ) ?>"
	data-counturl="<?= $enc->attr( $this->link( 'client/html/catalog/count/url', ['count' => 'supplier'] + $this->get( 'filterParams', [] ) ) ) ?>">

	<div class="header-name"><?= $enc->html( $this->translate( 'client', 'Suppliers' ), $enc::TRUST ) ?></div>

	<div class="supplier-lists">
		<?php if( $selectedIds ) : ?>
			<a class="exi-supplier-reset" href="<?= $enc->attr( $this->link( $linkKey, $this->get( 'supplierResetParams', [] ) ) ) ?>">
				Limpiar proveedores
			</a>
		<?php endif ?>

		<input class="form-control search exi-supplier-search"
			placeholder="Buscar proveedor">

		<ul class="attr-list exi-supplier-list">

			<?php foreach( $supplierList as $id => $supplier ) : ?>
				<li class="attr-item exi-supplier-item" data-id="<?= $enc->attr( $id ) ?>">
					<label class="exi-supplier-row" for="sup-<?= $enc->attr( $id ) ?>">
						<input class="attr-item exi-supplier-check"
							type="checkbox"
							id="sup-<?= $enc->attr( $id ) ?>"
							name="<?= $enc->attr( $this->formparam( ['f_supid', ''] ) ) ?>"
							value="<?= $enc->attr( $id ) ?>"
							<?= ( in_array( $id, $selectedIds ) ? 'checked="checked"' : '' ) ?>>

						<span class="exi-supplier-icon">
							<?php foreach( $supplier->getRefItems( 'media', 'icon', 'default' ) as $mediaItem ) : ?>
								<?= $this->partial(
									$this->config( 'client/html/common/partials/media', 'common/partials/media' ),
									array( 'item' => $mediaItem, 'boxAttributes' => array( 'class' => 'media-item' ) )
								) ?>
							<?php endforeach ?>
						</span>

						<span class="exi-supplier-name"><?= $enc->html( $supplier->getName(), $enc::TRUST ) ?></span>
					</label>
				</li>
			<?php endforeach ?>

			<li class="attr-item prototype" data-id="">
				<input class="attr-item" type="checkbox" id="_supproto" value="" name="<?= $enc->attr( $this->formparam( ['f_supid', ''] ) ) ?>" disabled>
				<label class="attr-name" for="_supproto"><span></span></label>
			</li>

		</ul>
	</div>

	<?php if( $this->config( 'client/html/catalog/filter/button', true ) ) : ?>
		<noscript>
			<button class="filter btn btn-primary" type="submit">
				<?= $enc->html( $this->translate( 'client', 'Show' ), $enc::TRUST ) ?>
			</button>
		</noscript>
	<?php endif ?>

</div>
<?php $this->block()->stop() ?>
<?= $this->block()->get( 'catalog/filter/supplier' ) ?>