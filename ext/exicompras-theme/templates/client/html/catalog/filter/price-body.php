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

// Rangos predefinidos (COP). Se ocultan si el max del catálogo es menor.
$presets = [
    ['label' => 'Menos de $100k', 'min' => 0, 'max' => 100000],
    ['label' => '$100k – $300k', 'min' => 100000, 'max' => 300000],
    ['label' => '$300k – $600k', 'min' => 300000, 'max' => 600000],
    ['label' => 'Más de $600k', 'min' => 600000, 'max' => null],
];

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
						min="0" max="<?= $enc->attr( $priceMax ) ?>" step="1000"
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
						min="0" max="<?= $enc->attr( $priceMax ) ?>" step="1000"
						value="<?= $enc->attr( $priceVal ) ?>"
						inputmode="numeric"
						placeholder="<?= $enc->attr( $priceHigh ) ?>">
				</label>
			</div>

			<input type="range"
				class="price-slider exi-price-slider"
				min="0" max="<?= $enc->attr( $priceMax ) ?>" step="1000"
				value="<?= $enc->attr( $priceVal ) ?>"
				title="<?= $enc->attr( $this->translate( 'client', 'Price range' ) ) ?>">

			<div class="exi-price-readout" aria-live="polite">
				<span class="exi-price-readout-label">Hasta</span>
				<span class="exi-price-readout-value" data-exi-price-readout>$0</span>
			</div>

			<?php
			$activeMin = $this->param( 'f_price/0', null );
			$activeMax = $this->param( 'f_price/1', null );
			?>
			<div class="exi-price-presets" role="group" aria-label="<?= $enc->attr( $this->translate( 'client', 'Price presets' ) ) ?>">
				<?php foreach( $presets as $p ) :
					$isActive = (string) $p['min'] === (string) $activeMin && (string) ($p['max'] ?? '') === (string) $activeMax;
					if( $p['max'] !== null && $p['max'] < $priceHigh ) {
						$tooSmall = true;
					} else {
						$tooSmall = false;
					}
				?>
					<a href="<?= $enc->attr( $this->link( $linkKey, ['f_price' => [$p['min'], $p['max']]] + $this->get( 'filterParams', [] ) ) ) ?>"
						class="exi-price-preset<?= $isActive ? ' is-active' : '' ?><?= $tooSmall ? ' exi-price-preset--disabled' : '' ?>"
						<?= $tooSmall ? 'aria-disabled="true"' : '' ?>>
						<?= $enc->html( $p['label'] ) ?>
					</a>
				<?php endforeach ?>
			</div>

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

<script>
(function() {
	var fmt = new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 });
	document.querySelectorAll('.exi-price').forEach(function(scope) {
		var slider = scope.querySelector('.exi-price-slider');
		var low    = scope.querySelector('input.price-low');
		var high   = scope.querySelector('input.price-high');
		var out    = scope.querySelector('[data-exi-price-readout]');
		if (!slider) return;

		function paint() {
			var min = parseInt(slider.min, 10) || 0;
			var max = parseInt(slider.max, 10) || 1;
			var val = parseInt(slider.value, 10) || 0;
			var pct = Math.max(0, Math.min(100, ((val - min) / (max - min)) * 100));
			slider.style.setProperty('--exi-slider-fill', pct + '%');
			if (out) out.textContent = fmt.format(val);
		}
		paint();
		slider.addEventListener('input', paint);

		if (high) {
			high.addEventListener('input', function() {
				var v = parseInt(high.value, 10) || 0;
				if (parseInt(slider.max, 10) >= v && v > 0) {
					slider.value = v;
					paint();
				}
			});
		}
		if (low) {
			low.addEventListener('input', function() {
				var v = parseInt(low.value, 10) || 0;
				var smin = parseInt(slider.min, 10) || 0;
				if (v >= smin && parseInt(slider.value, 10) < v) {
					slider.value = smin;
					paint();
				}
			});
		}
	});
})();
</script>
<?php $this->block()->stop() ?>
<?= $this->block()->get( 'catalog/filter/price' ) ?>