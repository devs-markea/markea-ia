<?php
declare( strict_types=1 );

namespace Markea\Core\Fields;

use Markea\Core\PostTypes;

defined( 'ABSPATH' ) || exit;

/**
 * Extra fields for proyecto and estratega.
 */
final class ContentFields {

	public function register(): void {
		acf_add_local_field_group(
			array(
				'key'      => 'group_markea_proyecto',
				'title'    => 'Datos del proyecto',
				'fields'   => array(
					F::text( 'project_tag', 'Etiqueta / tipo', array( 'instructions' => 'Texto corto sobre el título de la card.' ) ),
					F::url( 'project_url', 'Enlace (opcional)' ),
				),
				'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => PostTypes::PROJECT ) ) ),
			)
		);

		acf_add_local_field_group(
			array(
				'key'      => 'group_markea_estratega',
				'title'    => 'Datos del estratega',
				'fields'   => array(
					F::text( 'role', 'Cargo' ),
					F::url( 'linkedin', 'LinkedIn (opcional)' ),
				),
				'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => PostTypes::STRATEGIST ) ) ),
			)
		);
	}
}
