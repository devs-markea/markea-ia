<?php
declare( strict_types=1 );

namespace Markea\Core\Abilities;

use Markea\Core\PostTypes;

defined( 'ABSPATH' ) || exit;

/**
 * Site overview, cache purge and the change log.
 */
final class SiteAbilities {

	public function register(): void {
		Abilities::define(
			'markea/site-overview',
			'read',
			array(
				'label'               => 'Resumen del sitio',
				'description'         => 'Punto de partida: nombre, URL, versiones, entorno, tema, página de inicio y de la landing, conteo de contenido por estado y si hay Yoast SEO. No incluye datos de usuarios.',
				'category'            => 'markea-site',
				'input_schema'        => array( 'type' => 'object', 'additionalProperties' => false, 'default' => array() ),
				'output_schema'       => array( 'type' => 'object' ),
				'permission_callback' => Abilities::can( 'edit_posts' ),
				'execute_callback'    => static function (): array {
					$front  = (int) get_option( 'page_on_front' );
					$counts = array();
					foreach ( array( 'page', 'post', PostTypes::PROJECT, PostTypes::STRATEGIST ) as $type ) {
						if ( post_type_exists( $type ) ) {
							$counts[ $type ] = array_filter( (array) wp_count_posts( $type ), static fn( $n ) => (int) $n > 0 );
						}
					}

					return array(
						'name'        => get_bloginfo( 'name' ),
						'url'         => home_url( '/' ),
						'environment' => wp_get_environment_type(),
						'wordpress'   => get_bloginfo( 'version' ),
						'theme'       => wp_get_theme()->get( 'Name' ) . ' ' . wp_get_theme()->get( 'Version' ),
						'acf'         => defined( 'ACF_VERSION' ) ? ACF_VERSION : null,
						'yoast'       => defined( 'WPSEO_VERSION' ),
						'front_page'  => $front ? array( 'id' => $front, 'title' => get_the_title( $front ) ) : null,
						'counts'      => $counts,
					);
				},
			)
		);

		Abilities::define(
			'markea/cache-purge',
			'write',
			array(
				'label'               => 'Purgar la caché',
				'description'         => 'Vacía la caché de objetos y, si el sitio usa SiteGround Optimizer, la caché de página. Úsala después de editar contenido para verlo publicado.',
				'category'            => 'markea-site',
				'input_schema'        => array( 'type' => 'object', 'additionalProperties' => false, 'default' => array() ),
				'output_schema'       => array( 'type' => 'object' ),
				'permission_callback' => Abilities::can( 'edit_pages' ),
				'execute_callback'    => static function (): array {
					wp_cache_flush();
					$page_cache = false;
					if ( function_exists( 'sg_cachepress_purge_cache' ) ) {
						sg_cachepress_purge_cache();
						$page_cache = true;
					}
					Audit::record( 'markea/cache-purge', 'caché', array( array( 'field' => 'page_cache', 'old' => '', 'new' => $page_cache ? 'sí' : 'no disponible' ) ) );

					return array( 'object_cache' => true, 'page_cache' => $page_cache );
				},
			)
		);

		Abilities::define(
			'markea/audit-log',
			'read',
			array(
				'label'               => 'Bitácora de cambios',
				'description'         => 'Últimos cambios hechos con estas herramientas, con quién, cuándo y el valor anterior (para deshacer escribiendo el valor viejo).',
				'category'            => 'markea-site',
				'input_schema'        => Abilities::object_schema( array( 'limit' => array( 'type' => 'integer', 'minimum' => 1, 'maximum' => 100, 'default' => 20 ) ) ),
				'output_schema'       => array( 'type' => 'object' ),
				'permission_callback' => Abilities::can( 'edit_pages' ),
				'execute_callback'    => static fn( $input = array() ): array => array( 'entries' => Audit::latest( (int) ( ( (array) $input )['limit'] ?? 20 ) ) ),
			)
		);
	}
}
