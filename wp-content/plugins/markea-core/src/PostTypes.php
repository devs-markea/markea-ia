<?php
declare( strict_types=1 );

namespace Markea\Core;

defined( 'ABSPATH' ) || exit;

/**
 * CPT proyecto, estratega and lead; taxonomy categoria-proyecto.
 */
final class PostTypes {

	public const PROJECT    = 'proyecto';
	public const STRATEGIST = 'estratega';
	public const LEAD       = 'lead';
	public const CATEGORY   = 'categoria-proyecto';

	public function register(): void {
		add_action( 'init', array( $this, 'register_types' ) );
	}

	public function register_types(): void {
		register_taxonomy(
			self::CATEGORY,
			array( self::PROJECT ),
			array(
				'labels'            => $this->labels( 'Categoría', 'Categorías' ),
				'public'            => false,
				'show_ui'           => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'hierarchical'      => true,
				'rewrite'           => false,
			)
		);

		register_post_type(
			self::PROJECT,
			array(
				'labels'       => $this->labels( 'Proyecto', 'Proyectos' ),
				'public'       => false,
				'show_ui'      => true,
				'show_in_rest' => true,
				'menu_icon'    => 'dashicons-portfolio',
				'menu_position' => 21,
				'supports'     => array( 'title', 'thumbnail', 'excerpt', 'page-attributes' ),
				'rewrite'      => false,
			)
		);

		register_post_type(
			self::STRATEGIST,
			array(
				'labels'       => $this->labels( 'Estratega', 'Estrategas' ),
				'public'       => false,
				'show_ui'      => true,
				'show_in_rest' => true,
				'menu_icon'    => 'dashicons-groups',
				'menu_position' => 22,
				'supports'     => array( 'title', 'thumbnail', 'page-attributes' ),
				'rewrite'      => false,
			)
		);

		register_post_type(
			self::LEAD,
			array(
				'labels'              => $this->labels( 'Lead', 'Leads' ),
				'public'              => false,
				'show_ui'             => true,
				'show_in_rest'        => false,
				'exclude_from_search' => true,
				'menu_icon'           => 'dashicons-email-alt',
				'menu_position'       => 23,
				'supports'            => array( 'title' ),
				'capability_type'     => array( 'lead', 'leads' ),
				'map_meta_cap'        => true,
				'capabilities'        => array( 'create_posts' => 'do_not_allow' ),
				'rewrite'             => false,
			)
		);
	}

	/**
	 * Spanish (es_MX) labels.
	 *
	 * @return array<string,string>
	 */
	private function labels( string $singular, string $plural ): array {
		return array(
			'name'               => $plural,
			'singular_name'      => $singular,
			'menu_name'          => $plural,
			'add_new'            => 'Añadir nuevo',
			'add_new_item'       => 'Añadir ' . mb_strtolower( $singular ),
			'edit_item'          => 'Editar ' . mb_strtolower( $singular ),
			'new_item'           => 'Nuevo ' . mb_strtolower( $singular ),
			'view_item'          => 'Ver ' . mb_strtolower( $singular ),
			'search_items'       => 'Buscar ' . mb_strtolower( $plural ),
			'not_found'          => 'No se encontraron registros',
			'not_found_in_trash' => 'No hay registros en la papelera',
			'all_items'          => 'Todos: ' . mb_strtolower( $plural ),
		);
	}
}
