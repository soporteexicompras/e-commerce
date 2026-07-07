<?php

/**
 * Exicompras — price filter, mobile-friendly layout.
 * Two labelled inputs (Min / Max), a wide range slider, and stacked actions.
 */

$enc = $this->encoder();
$multi = $this->config( 'client/html/catalog/multiroute', false );
$linkKey = $multi && $this->param( 'path' ) || $this->param( 'f_catid' ) ? 'client/html/catalog/tree/url' : 'client/html/catalog/lists/url';

$priceLow  = (int) $this->param( 'f_price/0', 0 );
$priceHigh = (int) $this->get( 'priceHigh', 0 );
$priceMax  = (int) max( $this->param( 'f_price/1', 1 ), $priceHigh ?: 1 );
$priceVal  = (int) $this->param( 'f_price/1', $priceHigh ?: 1 );

?>
<?php $this->block()->start( 'catalog/filter/price' ) ?>
<?php if( $priceHigh > 0 ) : ?>
	<div class="section catalog-filter-price exi-price" aria-label="<?= $enc->attr( $this->translate( 'client', 'Price filter' ) ) ?>">
		<div class="header-name"><?= $enc->html( $this->translate( 'client', 'Price' ), $enc::TRUST ) ?></div>

		<div class="price-lists">
			<div class="exi-price-row">
				<label class="exi-price-field">
					<span class="exi-price-label">Mín</span>
					<input type="number"
						class="price-low"
						name="<?= $this->formparam( ['f_price', 0] ) ?>"
						min="0" max="<?= $enc->attr( $priceMax ) ?>" step="1"
						value="<?= $enc->attr( $priceLow ) ?>"
						inputmode="numeric"
						placeholder="0">
				</label>
				<span class="exi-price-sep" aria-hidden="true">—</span>
				<label class="exi-price-field">
					<span class="exi-price-label">Máx</span>
					<input type="number"
						class="price-high"
						name="<?= $this->formparam( ['f_price', 1] ) ?>"
						min="0" max="<?= $enc->attr( $priceMax ) ?>" step="1"
						value="<?= $enc->attr( $priceVal ) ?>"
						inputmode="numeric"
						placeholder="<?= $enc->attr( $priceHigh ) ?>">
				</label>
			</div>

			<input type="range"
				class="price-slider exi-price-slider"
				min="0" max="<?= $enc->attr( $priceMax ) ?>" step="1"
				value="<?= $enc->attr( $priceVal ) ?>"
				title="<?= $enc->attr( $this->translate( 'client', 'Price range' ) ) ?>">

			<div class="exi-price-actions">
				<button type="submit" class="exi-btn exi-btn-primary exi-btn-block">
					<?= $enc->html( $this->translate( 'client', 'Apply' ), $enc::TRUST ) ?>
				</button>
				<?php if( $this->param( 'f_price' ) ) : ?>
					<a class="exi-price-reset" href="<?= $enc->attr( $this->link( $linkKey, $this->get( 'priceResetParams', [] ) ) ) ?>">
						Restablecer precio
					</a>
				<?php endif ?>
			</div>
		</div>
	</div>
<?php endif ?>
<?php $this->block()->stop() ?>
<?= $this->block()->get( 'catalog/filter/price' ) ?>