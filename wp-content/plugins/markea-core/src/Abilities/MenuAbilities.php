<?php
declare( strict_types=1 );

namespace Markea\Core\Abilities;

use WP_Error;

defined( 'ABSPATH' ) || exit;

/**
 * Navigation menus: read, edit links, add links and choose which menu a theme location uses. Items are never removed.
 */
final class MenuAbilities {

	public function register(): void {
		$menu = array( 'type' => 'integer', 'minimum' => 1, 'description' => 'ID del menú (ver menus-list).' );

		Abilities::define(
			'markea/menus-list',
			'read',
			array(
				'label'               => 'Listar menús',
				'description'         => 'Lista los menús y las ubicaciones del tema con el menú asignado a cada una.',
				'category'            => 'markea-menus',
				'input_schema'        => array( 'type' => 'object', 'additionalProperties' => false, 'default' => array() ),
				'output_schema'       => array( 'type' => 'object' ),
				'permission_callback' => Abilities::can( 'edit_theme_options' ),
				'execute_callback'    => static function (): array {
					$locations = get_nav_menu_locations();
					$menus     = array();
					foreach ( wp_get_nav_menus() as $m ) {
						$menus[] = array(
							'id'        => (int) $m->term_id,
							'name'      => $m->name,
							'items'     => (int) $m->count,
							'locations' => array_keys( array_filter( $locations, static fn( $id ) => (int) $id === (int) $m->term_id ) ),
						);
					}

					return array( 'menus' => $menus, 'locations' => get_registered_nav_menus() );
				},
			)
		);

		Abilities::define(
			'markea/menu-get-items',
			'read',
			array(
				'label'               => 'Leer los enlaces de un menú',
				'description'         => 'Devuelve los enlaces de un menú en orden: ID, título, URL, padre y posición.',
				'category'            => 'markea-menus',
				'input_schema'        => Abilities::object_schema( array( 'menu' => $menu ), array( 'menu' ) ),
				'output_schema'       => array( 'type' => 'object' ),
				'permission_callback' => Abilities::can( 'edit_theme_options' ),
				'execute_callback'    => function ( $input = array() ) {
					$items = wp_get_nav_menu_items( (int) ( (array) $input )['menu'] );

					return false === $items ? new WP_Error( 'markea_no_menu', 'Ese menú no existe.', array( 'status' => 404 ) ) : array( 'items' => array_map( array( $this, 'item' ), $items ) );
				},
			)
		);

		Abilities::define(
			'markea/menu-update-item',
			'write',
			array(
				'label'               => 'Editar un enlace del menú',
				'description'         => 'Cambia el título y, en enlaces personalizados, la URL de un enlace del menú.',
				'category'            => 'markea-menus',
				'input_schema'        => Abilities::object_schema(
					array(
						'item_id' => array( 'type' => 'integer', 'minimum' => 1 ),
						'title'   => array( 'type' => 'string' ),
						'url'     => array( 'type' => 'string', 'description' => 'Solo enlaces personalizados.' ),
					),
					array( 'item_id' )
				),
				'output_schema'       => array( 'type' => 'object' ),
				'permission_callback' => Abilities::can( 'edit_theme_options' ),
				'execute_callback'    => fn( $input = array() ) => $this->update_item( (array) $input ),
			)
		);

		Abilities::define(
			'markea/menu-add-item',
			'create',
			array(
				'label'               => 'Añadir un enlace al menú',
				'description'         => 'Agrega un enlace personalizado (por ejemplo /#contacto) al final de un menú o bajo otro enlace.',
				'category'            => 'markea-menus',
				'input_schema'        => Abilities::object_schema(
					array(
						'menu'      => $menu,
						'title'     => array( 'type' => 'string' ),
						'url'       => array( 'type' => 'string' ),
						'parent_id' => array( 'type' => 'integer', 'minimum' => 0, 'description' => 'ID del enlace padre; 0 para primer nivel.' ),
					),
					array( 'menu', 'title', 'url' )
				),
				'output_schema'       => array( 'type' => 'object' ),
				'permission_callback' => Abilities::can( 'edit_theme_options' ),
				'execute_callback'    => function ( $input = array() ) {
					$input = (array) $input;
					$menu  = (int) $input['menu'];
					$url   = $this->clean_url( (string) $input['url'] );
					if ( ! wp_get_nav_menu_object( $menu ) || '' === $url ) {
						return new WP_Error( 'markea_bad_input', 'Menú o URL no válidos.', array( 'status' => 400 ) );
					}

					$id = wp_update_nav_menu_item(
						$menu,
						0,
						array(
							'menu-item-type'      => 'custom',
							'menu-item-title'     => sanitize_text_field( (string) $input['title'] ),
							'menu-item-url'       => $url,
							'menu-item-parent-id' => (int) ( $input['parent_id'] ?? 0 ),
							'menu-item-status'    => 'publish',
						)
					);
					if ( is_wp_error( $id ) ) {
						return $id;
					}
					Audit::record( 'markea/menu-add-item', sprintf( 'menú #%d', $menu ), array( array( 'field' => 'enlace +' . $id, 'old' => '', 'new' => $input['title'] . ' → ' . $url ) ) );

					return array( 'item_id' => (int) $id );
				},
			)
		);

		Abilities::define(
			'markea/menu-assign-location',
			'write',
			array(
				'label'               => 'Asignar un menú a una ubicación',
				'description'         => 'Elige qué menú usa una ubicación del tema (por ejemplo primary). El menú anterior no se borra.',
				'category'            => 'markea-menus',
				'input_schema'        => Abilities::object_schema( array( 'menu' => $menu, 'location' => array( 'type' => 'string' ) ), array( 'menu', 'location' ) ),
				'output_schema'       => array( 'type' => 'object' ),
				'permission_callback' => Abilities::can( 'edit_theme_options' ),
				'execute_callback'    => function ( $input = array() ) {
					$input     = (array) $input;
					$location  = (string) $input['location'];
					$locations = get_nav_menu_locations();
					if ( ! array_key_exists( $location, get_registered_nav_menus() ) || ! wp_get_nav_menu_object( (int) $input['menu'] ) ) {
						return new WP_Error( 'markea_bad_input', 'Ubicación o menú no válidos (ver menus-list).', array( 'status' => 400 ) );
					}

					$old                 = (int) ( $locations[ $location ] ?? 0 );
					$locations[ $location ] = (int) $input['menu'];
					set_theme_mod( 'nav_menu_locations', $locations );
					Audit::record( 'markea/menu-assign-location', 'ubicación ' . $location, array( array( 'field' => 'menu', 'old' => $old, 'new' => (int) $input['menu'] ) ) );

					return array( 'location' => $location, 'menu' => (int) $input['menu'], 'previous_menu' => $old );
				},
			)
		);
	}

	private function clean_url( string $url ): string {
		return str_starts_with( $url, '/' ) || str_starts_with( $url, '#' ) ? esc_url_raw( $url ) : esc_url_raw( $url, array( 'http', 'https', 'mailto', 'tel' ) );
	}

	/** @return array<string,mixed> */
	private function item( \WP_Post $item ): array {
		return array(
			'id'       => (int) $item->ID,
			'title'    => (string) $item->title,
			'url'      => (string) $item->url,
			'type'     => (string) $item->type,
			'parent'   => (int) $item->menu_item_parent,
			'position' => (int) $item->menu_order,
		);
	}

	/** @param array<string,mixed> $input @return array<string,mixed>|WP_Error */
	private function update_item( array $input ) {
		$id   = (int) $input['item_id'];
		$post = get_post( $id );
		if ( ! $post || 'nav_menu_item' !== $post->post_type ) {
			return new WP_Error( 'markea_no_item', 'Ese enlace de menú no existe.', array( 'status' => 404 ) );
		}

		$item  = wp_setup_nav_menu_item( $post );
		$menus = wp_get_object_terms( $id, 'nav_menu', array( 'fields' => 'ids' ) );
		if ( is_wp_error( $menus ) || ! $menus ) {
			return new WP_Error( 'markea_no_menu', 'El enlace no pertenece a ningún menú.', array( 'status' => 400 ) );
		}

		$title   = array_key_exists( 'title', $input ) ? sanitize_text_field( (string) $input['title'] ) : (string) $item->title;
		$url     = (string) $item->url;
		$changes = array();

		if ( array_key_exists( 'url', $input ) ) {
			if ( 'custom' !== $item->type ) {
				return new WP_Error( 'markea_not_custom', 'La URL solo se cambia en enlaces personalizados.', array( 'status' => 400 ) );
			}
			$url = $this->clean_url( (string) $input['url'] );
			if ( '' === $url ) {
				return new WP_Error( 'markea_bad_url', 'URL no válida.', array( 'status' => 400 ) );
			}
		}
		if ( $title !== $item->title ) {
			$changes[] = array( 'field' => 'title', 'old' => $item->title, 'new' => $title );
		}
		if ( $url !== $item->url ) {
			$changes[] = array( 'field' => 'url', 'old' => $item->url, 'new' => $url );
		}
		if ( ! $changes ) {
			return new WP_Error( 'markea_nothing_to_do', 'No hay nada que cambiar.', array( 'status' => 400 ) );
		}

		$result = wp_update_nav_menu_item(
			(int) $menus[0],
			$id,
			array(
				'menu-item-type'        => $item->type,
				'menu-item-object'      => $item->object,
				'menu-item-object-id'   => $item->object_id,
				'menu-item-title'       => $title,
				'menu-item-url'         => $url,
				'menu-item-parent-id'   => $item->menu_item_parent,
				'menu-item-position'    => $item->menu_order,
				'menu-item-classes'     => implode( ' ', (array) $item->classes ),
				'menu-item-target'      => $item->target,
				'menu-item-attr-title'  => $item->attr_title,
				'menu-item-description' => $item->description,
				'menu-item-xfn'         => $item->xfn,
				'menu-item-status'      => 'publish',
			)
		);
		if ( is_wp_error( $result ) ) {
			return $result;
		}
		Audit::record( 'markea/menu-update-item', sprintf( 'enlace #%d', $id ), $changes );

		return array( 'item_id' => $id, 'updated' => array_column( $changes, 'field' ) );
	}
}
