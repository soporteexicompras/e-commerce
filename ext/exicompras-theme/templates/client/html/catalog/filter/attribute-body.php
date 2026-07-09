<?php

/**
 * Exicompras — attribute filter override.
 *
 * Differences from Aimeos core:
 *  1. Early exit if NO attribute type has at least one option (kills the
 *     "Filtro" empty wrapper bug from the core).
 *  2. Each attribute type becomes a chip group (toggle pills) instead of
 *     a checkbox list. Better tap targets and visual cohesion with the rest.
 *  3. Section is collapsed by default on desktop and expanded on mobile
 *     (drawer always shows everything — body keeps it open there).
 *
 * Notes:
 *  - We re-use the same `catalog/filter/attribute` block name that Aimeos
 *    core uses. Calling $this->block()->start/stop() registers the output
 *    so $this->block()->get() in body.php consumes it.
 */

$enc = $this->encoder();

$multi = $this->config( 'client/html/catalog/multiroute', false );
$linkKey = $multi && $this->param( 'path' ) || $this->param( 'f_catid' ) ? 'client/html/catalog/tree/url' : 'client/html/catalog/lists/url';

$attrIds = array_filter( (array) $this->param( 'f_attrid', [] ) );
$optIds = array_filter( (array) $this->param( 'f_optid', [] ) );
$oneIds = array_filter( (array) $this->param( 'f_oneid', [] ) );
$attrTypes = $this->get( 'detailAttributeTypes', [] );
$attrMap = $this->get( 'attributeMap', [] );
$params = (array) $this->param();

$selectedAll = array_merge( $attrIds, $optIds );
foreach( $oneIds as $k => $v ) {
    foreach( (array) $v as $vv ) { $selectedAll[] = $vv; }
}

?>
<?php $this->block()->start( 'catalog/filter/attribute' ) ?>

<?php
$hasAnyOption = false;
foreach( $attrMap as $attributes ) {
    if( !empty( $attributes ) ) { $hasAnyOption = true; break; }
}
?>

<?php if( $hasAnyOption ) : ?>
	<div class="section catalog-filter-attribute exi-attr"
		aria-label="<?= $enc->attr( $this->translate( 'client', 'Product filters' ) ) ?>"
		data-counturl="<?= $enc->attr( $this->link( 'client/html/catalog/count/url', ['count' => 'attribute'] + $this->get( 'filterParams', [] ) ) ) ?>">

		<div class="exi-section-header" data-exi-toggle="next">
			<span><?= $enc->html( $this->translate( 'client', 'Filter' ), $enc::TRUST ) ?></span>
			<svg class="exi-section-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
				<polyline points="6 9 12 15 18 9"/>
			</svg>
		</div>

		<div class="attribute-lists exi-attr-lists">

			<?php if( !empty( $selectedAll ) ) : ?>
				<a class="exi-attr-reset" href="<?= $enc->attr( $this->link( $linkKey, $this->get( 'attributeResetParams', [] ) ) ) ?>">
					<?= $enc->html( $this->translate( 'client', 'Reset' ), $enc::TRUST ) ?>
				</a>
			<?php endif ?>

			<?php foreach( $attrMap as $attrType => $attributes ) : ?>
				<?php if( !empty( $attributes ) ) : ?>
					<div class="exi-attr-group" data-attr-type="<?= $enc->attr( $attrType ) ?>">
						<div class="exi-attr-type"><?= $enc->html( $attrTypes[$attrType]?->getName() ?? $attrType, $enc::TRUST ) ?></div>
						<div class="exi-chips">
							<?php foreach( $attributes as $id => $attribute ) :
								$formparam = $attribute->get( 'formparam', [] );
								$isChecked = !empty( $attribute->get( 'checked', false ) );
								$hasIcon = $attribute->getRefItems( 'media', 'icon', 'default' )->count() > 0;
							?>
								<label class="exi-chip<?= $isChecked ? ' is-active' : '' ?><?= $hasIcon ? ' exi-chip--icon' : '' ?>"
									for="attr-<?= $enc->attr( $id ) ?>">
									<input class="exi-chip-input"
										type="checkbox"
										id="attr-<?= $enc->attr( $id ) ?>"
										value="<?= $enc->attr( $id ) ?>"
										name="<?= $enc->attr( $this->formparam( $formparam ) ) ?>"
										<?= $isChecked ? 'checked="checked"' : '' ?>
									>
									<span class="exi-chip-icon" aria-hidden="true">
										<?php foreach( $attribute->getRefItems( 'media', 'icon', 'default' ) as $mediaItem ) : ?>
											<?= $this->partial(
												$this->config( 'client/html/common/partials/media', 'common/partials/media' ),
												array( 'item' => $mediaItem, 'boxAttributes' => array( 'class' => 'media-item' ) )
											) ?>
										<?php endforeach ?>
									</span>
									<span class="exi-chip-label"><?= $enc->html( $attribute->getName(), $enc::TRUST ) ?></span>
								</label>
							<?php endforeach ?>
						</div>
					</div>
				<?php endif ?>
			<?php endforeach ?>

		</div>
	</div>
<?php endif ?>
<?php $this->block()->stop() ?>
<?= $this->block()->get( 'catalog/filter/attribute' ) ?>
