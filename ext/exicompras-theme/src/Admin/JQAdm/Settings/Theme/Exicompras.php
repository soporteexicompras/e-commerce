<?php

namespace Aimeos\Admin\JQAdm\Settings\Theme;

/**
 * Exicompras — Extiende el Theme\Standard de Aimeos para:
 *   - Guardar los colores del tema (comportamiento nativo)
 *   - Auto-incrementar "theme_version" en el sitio para forzar el
 *     refresco de CSS/JS cacheado por el navegador al guardar.
 *
 * Configurar con:  admin/jqadm/settings/theme/name => 'exicompras'
 */
class Exicompras extends Standard
{
	public function save() : ?string
	{
		$result = parent::save();

		$site   = $this->context()->locale()->getSiteItem();
		$curr   = (int) $site->getConfigValue( 'theme_version', 0 );
		$site->setConfigValue( 'theme_version', $curr + 1 );

		return $result;
	}
}