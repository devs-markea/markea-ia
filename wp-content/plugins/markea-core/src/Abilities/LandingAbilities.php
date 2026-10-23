<?php
declare( strict_types=1 );

namespace Markea\Core\Abilities;

use Markea\Core\Fields\LandingFields;
use WP_Error;

defined( 'ABSPATH' ) || exit;

/**
 * The landing is ONE ACF Flexible Content (markea_sections) on the page that uses templates/landing.php.
 * Sections are addressed by their 1-based position (list them first). Writes go field by field with
 * update_sub_field, never by replacing the whole field.
 */
final class LandingAbilities {

	private const FIELD = 'markea_sections';

	public function register(): void {
		$page_id = array( 'type' => 'integer', 'description' => 'ID de la página de la landing. Si se omite, la página de inicio.' );
		$index   = array( 'type' => 'integer', 'minimum' => 1, 'description' => 'Posición de la sección (empieza en 1; ver landing-list-sections).' );
		$values  = array( 'type' => 'object', 'description' => 'Mapa nombre de campo => valor (ver landing-get-schema). Solo campos de texto, select, imagen (ID), url, color y sí/no.' );

		Abilities::define(
			'markea/landing-get-schema',
			'read',
			array(
				'label'               => 'Esquema de la landing',
				'description'         => 'Lista las secciones (layouts) que acepta la landing y los campos de cada una: nombre, tipo, opciones de los select y repeaters. Úsala antes de escribir.',
				'category'            => 'markea-landing',
				'input_schema'        => array( 'type' => 'object', 'additionalProperties' => false, 'default' => array() ),
				'output_schema'       => array( 'type' => 'object' ),
				'permission_callback' => Abilities::can( 'edit_pages' ),
				'execute_callback'    => static function (): array {
					$out = array();
					foreach ( FieldAccess::landing_layouts() as $name => $layout ) {
						$out[] = array(
							'layout' => $name,
							'label'  => (string) $layout['label'],
							'fields' => FieldAccess::describe( (array) $layout['sub_fields'] ),
						);
					}

					return array( 'layouts' => $out );
				},
			)
		);

		Abilities::define(
			'markea/landing-list-sections',
			'read',
			array(
				'label'               => 'Listar secciones de la landing',
				'description'         => 'Devuelve las secciones en orden, con su posición, layout y título.',
				'category'            => 'markea-landing',
				'input_schema'        => Abilities::object_schema( array( 'page_id' => $page_id ) ),
				'output_schema'       => array( 'type' => 'object' ),
				'permission_callback' => fn( $input = array() ): bool => $this->can_edit( (array) $input ),
				'execute_callback'    => function ( $input = array() ) {
					$id = $this->resolve( (array) $input );
					if ( is_wp_error( $id ) ) {
						return $id;
					}

					$layouts  = FieldAccess::landing_layouts();
					$sections = array();
					foreach ( $this->rows( $id ) as $i => $row ) {
						$layout     = (string) ( $row['acf_fc_layout'] ?? '' );
						$sections[] = array(
							'index'  => $i + 1,
							'layout' => $layout,
							'label'  => (string) ( $layouts[ $layout ]['label'] ?? $layout ),
							'title'  => (string) ( $row['title'] ?? $row['eyebrow'] ?? '' ),
						);
					}

					return array( 'page_id' => $id, 'sections' => $sections );
				},
			)
		);

		Abilities::define(
			'markea/landing-get-section',
			'read',
			array(
				'label'               => 'Leer una sección de la landing',
				'description'         => 'Devuelve todos los valores de una sección (imágenes como ID de medio, repeaters como listas de filas numeradas desde 1).',
				'category'            => 'markea-landing',
				'input_schema'        => Abilities::object_schema( array( 'page_id' => $page_id, 'index' => $index ), array( 'index' ) ),
				'output_schema'       => array( 'type' => 'object' ),
				'permission_callback' => fn( $input = array() ): bool => $this->can_edit( (array) $input ),
				'execute_callback'    => function ( $input = array() ) {
					$input = (array) $input;
					$found = $this->section( $input );
					if ( is_wp_error( $found ) ) {
						return $found;
					}
					[ $id, $row ] = $found;
					$layout       = (string) $row['acf_fc_layout'];
					unset( $row['acf_fc_layout'] );

					return array( 'page_id' => $id, 'index' => (int) $input['index'], 'layout' => $layout, 'values' => $row );
				},
			)
		);

		Abilities::define(
			'markea/landing-update-section',
			'write',
			array(
				'label'               => 'Editar campos de una sección de la landing',
				'description'         => 'Cambia solo los campos indicados de una sección. "fields" edita campos de la sección; "repeater_rows" edita campos dentro de filas existentes de un repeater (por ejemplo el texto de la tarjeta 2 de "items"). Valida todo antes de escribir y deja registro con el valor anterior.',
				'category'            => 'markea-landing',
				'input_schema'        => Abilities::object_schema(
					array(
						'page_id'       => $page_id,
						'index'         => $index,
						'fields'        => $values,
						'repeater_rows' => array(
							'type'        => 'array',
							'description' => 'Cambios dentro de filas de un repeater.',
							'items'       => Abilities::object_schema(
								array(
									'repeater' => array( 'type' => 'string', 'description' => 'Nombre del repeater, ej. items.' ),
									'row'      => array( 'type' => 'integer', 'minimum' => 1, 'description' => 'Número de fila (empieza en 1).' ),
									'fields'   => $values,
								),
								array( 'repeater', 'row', 'fields' )
							),
						),
					),
					array( 'index' )
				),
				'output_schema'       => array( 'type' => 'object' ),
				'permission_callback' => fn( $input = array() ): bool => $this->can_edit( (array) $input ),
				'execute_callback'    => fn( $input = array() ) => $this->update_section( (array) $input ),
			)
		);

		Abilities::define(
			'markea/landing-add-item',
			'create',
			array(
				'label'               => 'Añadir una fila a un repeater de la landing',
				'description'         => 'Agrega una fila al final de un repeater de una sección (por ejemplo una card nueva en "items"). Respeta el máximo de filas del campo.',
				'category'            => 'markea-landing',
				'input_schema'        => Abilities::object_schema(
					array(
						'page_id'  => $page_id,
						'index'    => $index,
						'repeater' => array( 'type' => 'string' ),
						'fields'   => $values,
					),
					array( 'index', 'repeater', 'fields' )
				),
				'output_schema'       => array( 'type' => 'object' ),
				'permission_callback' => fn( $input = array() ): bool => $this->can_edit( (array) $input ),
				'execute_callback'    => fn( $input = array() ) => $this->add_item( (array) $input ),
			)
		);

		Abilities::define(
			'markea/landing-add-section',
			'create',
			array(
				'label'               => 'Añadir una sección al final de la landing',
				'description'         => 'Agrega una sección nueva (layout permitido por el tema) al final de la landing, con los campos indicados. Para moverla no hay herramienta: se reordena en wp-admin.',
				'category'            => 'markea-landing',
				'input_schema'        => Abilities::object_schema(
					array(
						'page_id' => $page_id,
						'layout'  => array( 'type' => 'string', 'enum' => LandingFields::LAYOUTS ),
						'fields'  => $values,
					),
					array( 'layout' )
				),
				'output_schema'       => array( 'type' => 'object' ),
				'permission_callback' => fn( $input = array() ): bool => $this->can_edit( (array) $input ),
				'execute_callback'    => fn( $input = array() ) => $this->add_section( (array) $input ),
			)
		);
	}

	/** @param array<string,mixed> $input */
	private function can_edit( array $input ): bool {
		$id = $this->resolve( $input );

		return ! is_wp_error( $id ) && current_user_can( 'edit_post', $id );
	}

	/** @param array<string,mixed> $input @return int|WP_Error */
	private function resolve( array $input ) {
		$id = (int) ( $input['page_id'] ?? 0 );
		if ( $id <= 0 ) {
			$id = (int) get_option( 'page_on_front' );
		}

		$post = $id > 0 ? get_post( $id ) : null;
		if ( ! $post || 'page' !== $post->post_type || LandingFields::TEMPLATE !== get_post_meta( $id, '_wp_page_template', true ) ) {
			return new WP_Error( 'markea_not_landing', 'Esa página no usa la plantilla de la landing.', array( 'status' => 404 ) );
		}

		return $id;
	}

	/** @return array<int,array<string,mixed>> */
	private function rows( int $id ): array {
		// Formatted read: rows come keyed by field NAME (the unformatted read returns internal field keys). Every field
		// type used here formats to its plain value (image => ID, select => string, true_false => bool).
		// Writes in the same request must be visible to the next read (and to the max-rows check): drop ACF's value cache.
		if ( function_exists( 'acf_flush_value_cache' ) ) {
			acf_flush_value_cache( $id, self::FIELD );
		}
		$rows = get_field( self::FIELD, $id );

		return is_array( $rows ) ? array_values( $rows ) : array();
	}

	/**
	 * @param array<string,mixed> $input
	 * @return array{0:int,1:array<string,mixed>}|WP_Error
	 */
	private function section( array $input ) {
		$id = $this->resolve( $input );
		if ( is_wp_error( $id ) ) {
			return $id;
		}

		$rows  = $this->rows( $id );
		$index = (int) ( $input['index'] ?? 0 );
		if ( $index < 1 || $index > count( $rows ) ) {
			return new WP_Error( 'markea_bad_index', sprintf( 'La sección %d no existe (hay %d).', $index, count( $rows ) ), array( 'status' => 404 ) );
		}

		return array( $id, $rows[ $index - 1 ] );
	}

	/** @param array<string,mixed> $input @return array<string,mixed>|WP_Error */
	private function update_section( array $input ) {
		$found = $this->section( $input );
		if ( is_wp_error( $found ) ) {
			return $found;
		}
		[ $id, $row ] = $found;

		$index  = (int) $input['index'];
		$layout = FieldAccess::landing_layouts()[ (string) $row['acf_fc_layout'] ] ?? null;
		if ( ! $layout ) {
			return new WP_Error( 'markea_unknown_layout', 'Layout desconocido.', array( 'status' => 400 ) );
		}
		$subs = (array) $layout['sub_fields'];

		$writes  = array(); // [ selector, value, label, old ].
		$changes = array();

		$clean = FieldAccess::sanitize_map( $subs, (array) ( $input['fields'] ?? array() ) );
		if ( is_wp_error( $clean ) ) {
			return $clean;
		}
		foreach ( $clean as $name => $value ) {
			$writes[] = array( array( self::FIELD, $index, $name ), $value, $name, $row[ $name ] ?? '' );
		}

		foreach ( (array) ( $input['repeater_rows'] ?? array() ) as $change ) {
			$repeater = FieldAccess::find( $subs, (string) $change['repeater'] );
			if ( ! $repeater || 'repeater' !== $repeater['type'] ) {
				return new WP_Error( 'markea_unknown_repeater', sprintf( '"%s" no es un repeater de esta sección.', $change['repeater'] ), array( 'status' => 400 ) );
			}

			$existing = (array) ( $row[ $repeater['name'] ] ?? array() );
			$number   = (int) $change['row'];
			if ( $number < 1 || $number > count( $existing ) ) {
				return new WP_Error( 'markea_bad_row', sprintf( '"%s" tiene %d filas; la fila %d no existe.', $repeater['name'], count( $existing ), $number ), array( 'status' => 404 ) );
			}

			$clean = FieldAccess::sanitize_map( (array) $repeater['sub_fields'], (array) $change['fields'] );
			if ( is_wp_error( $clean ) ) {
				return $clean;
			}
			$current = array_values( $existing )[ $number - 1 ];
			foreach ( $clean as $name => $value ) {
				$writes[] = array( array( self::FIELD, $index, $repeater['name'], $number, $name ), $value, $repeater['name'] . '[' . $number . '].' . $name, $current[ $name ] ?? '' );
			}
		}

		if ( ! $writes ) {
			return new WP_Error( 'markea_nothing_to_do', 'No se indicó ningún campo que cambiar.', array( 'status' => 400 ) );
		}

		foreach ( $writes as [ $selector, $value, $label, $old ] ) {
			if ( ! update_sub_field( $selector, $value, $id ) && (string) $old !== (string) $value ) {
				return new WP_Error( 'markea_write_failed', sprintf( 'No se pudo escribir "%s".', $label ), array( 'status' => 500 ) );
			}
			$changes[] = array( 'field' => $label, 'old' => $old, 'new' => $value );
		}

		Audit::record( 'markea/landing-update-section', sprintf( 'página %d, sección %d (%s)', $id, $index, $row['acf_fc_layout'] ), $changes );

		return array( 'page_id' => $id, 'index' => $index, 'updated' => array_column( $changes, 'field' ) );
	}

	/** @param array<string,mixed> $input @return array<string,mixed>|WP_Error */
	private function add_item( array $input ) {
		$found = $this->section( $input );
		if ( is_wp_error( $found ) ) {
			return $found;
		}
		[ $id, $row ] = $found;

		$index  = (int) $input['index'];
		$layout = FieldAccess::landing_layouts()[ (string) $row['acf_fc_layout'] ] ?? null;
		$field  = $layout ? FieldAccess::find( (array) $layout['sub_fields'], (string) $input['repeater'] ) : null;
		if ( ! $field || 'repeater' !== $field['type'] ) {
			return new WP_Error( 'markea_unknown_repeater', sprintf( '"%s" no es un repeater de esta sección.', $input['repeater'] ), array( 'status' => 400 ) );
		}

		$count = count( (array) ( $row[ $field['name'] ] ?? array() ) );
		if ( ! empty( $field['max'] ) && $count >= (int) $field['max'] ) {
			return new WP_Error( 'markea_max_rows', sprintf( '"%s" admite como máximo %d filas.', $field['name'], (int) $field['max'] ), array( 'status' => 400 ) );
		}

		$clean = FieldAccess::sanitize_map( (array) $field['sub_fields'], (array) $input['fields'] );
		if ( is_wp_error( $clean ) ) {
			return $clean;
		}

		$new = add_sub_row( array( self::FIELD, $index, $field['name'] ), $clean, $id );
		if ( ! $new ) {
			return new WP_Error( 'markea_write_failed', 'No se pudo añadir la fila.', array( 'status' => 500 ) );
		}

		Audit::record( 'markea/landing-add-item', sprintf( 'página %d, sección %d, %s', $id, $index, $field['name'] ), array( array( 'field' => $field['name'] . '[+' . (int) $new . ']', 'old' => '', 'new' => $clean ) ) );

		return array( 'page_id' => $id, 'index' => $index, 'repeater' => $field['name'], 'row' => (int) $new );
	}

	/** @param array<string,mixed> $input @return array<string,mixed>|WP_Error */
	private function add_section( array $input ) {
		$id = $this->resolve( $input );
		if ( is_wp_error( $id ) ) {
			return $id;
		}

		$layout = FieldAccess::landing_layouts()[ (string) $input['layout'] ] ?? null;
		if ( ! $layout ) {
			return new WP_Error( 'markea_unknown_layout', 'Layout desconocido.', array( 'status' => 400 ) );
		}

		$clean = FieldAccess::sanitize_map( (array) $layout['sub_fields'], (array) ( $input['fields'] ?? array() ) );
		if ( is_wp_error( $clean ) ) {
			return $clean;
		}

		$new = add_row( self::FIELD, array_merge( array( 'acf_fc_layout' => $layout['name'] ), $clean ), $id );
		if ( ! $new ) {
			return new WP_Error( 'markea_write_failed', 'No se pudo añadir la sección.', array( 'status' => 500 ) );
		}

		Audit::record( 'markea/landing-add-section', sprintf( 'página %d', $id ), array( array( 'field' => 'sección +' . (int) $new . ' (' . $layout['name'] . ')', 'old' => '', 'new' => $clean ) ) );

		return array( 'page_id' => $id, 'index' => (int) $new, 'layout' => (string) $layout['name'] );
	}
}
