<?php
declare( strict_types=1 );

namespace Markea\Core\Abilities;

use WP_Error;

defined( 'ABSPATH' ) || exit;

/**
 * Site Settings (ACF options page). Anything admin-only in the options page (tracking IDs, maintenance mode) is
 * never exposed, and neither is the form recipient (it decides where lead emails go).
 */
final class SettingsAbilities {

	private const DENY = array( 'markea_form_recipient' );

	public function register(): void {
		Abilities::define(
			'markea/settings-get',
			'read',
			array(
				'label'               => 'Leer Site Settings',
				'description'         => 'Devuelve los ajustes del sitio que se pueden gestionar (contacto, redes, marca, colores, formulario, menú, animaciones, footer, newsletter) con su valor actual. No incluye tracking, mantenimiento ni el correo destino del formulario.',
				'category'            => 'markea-settings',
				'input_schema'        => Abilities::object_schema( array( 'names' => array( 'type' => 'array', 'items' => array( 'type' => 'string' ), 'description' => 'Opcional: solo estos campos.' ) ) ),
				'output_schema'       => array( 'type' => 'object' ),
				'permission_callback' => Abilities::can( 'edit_pages' ),
				'execute_callback'    => function ( $input = array() ) {
					$only   = (array) ( ( (array) $input )['names'] ?? array() );
					$fields = array();
					foreach ( $this->fields() as $field ) {
						$short = substr( (string) $field['name'], strlen( 'markea_' ) );
						if ( $only && ! in_array( $short, $only, true ) ) {
							continue;
						}
						$fields[] = array(
							'name'     => $short,
							'label'    => (string) $field['label'],
							'type'     => (string) $field['type'],
							'writable' => FieldAccess::is_writable( $field ),
							'value'    => get_field( (string) $field['name'], 'option', false ),
						) + ( 'select' === $field['type'] ? array( 'choices' => array_map( 'strval', array_keys( (array) $field['choices'] ) ) ) : array() );
					}

					return array( 'fields' => $fields );
				},
			)
		);

		Abilities::define(
			'markea/settings-update',
			'write',
			array(
				'label'               => 'Editar Site Settings',
				'description'         => 'Cambia solo los ajustes indicados (nombre sin el prefijo markea_, ej. contact_phone, footer_tagline, color_electric). Imágenes por ID de medio. Valida todo antes de escribir y deja registro con el valor anterior.',
				'category'            => 'markea-settings',
				'input_schema'        => Abilities::object_schema( array( 'fields' => array( 'type' => 'object', 'description' => 'Mapa nombre => valor.' ) ), array( 'fields' ) ),
				'output_schema'       => array( 'type' => 'object' ),
				'permission_callback' => Abilities::can( 'edit_pages' ),
				'execute_callback'    => fn( $input = array() ) => $this->update( (array) $input ),
			)
		);
	}

	/** @return array<int,array<string,mixed>> Allowed fields (prefixed names). */
	private function fields(): array {
		$group  = acf_get_fields( 'group_markea_settings' );
		$result = array();

		foreach ( is_array( $group ) ? $group : array() as $field ) {
			$type = (string) ( $field['type'] ?? '' );
			if ( in_array( $type, array( 'tab', 'message' ), true ) || '' === (string) $field['name'] ) {
				continue;
			}
			// Admin-only fields (tracking, maintenance) use keys starting with field_markea_admin_.
			if ( str_starts_with( (string) $field['key'], 'field_markea_admin_' ) || in_array( $field['name'], self::DENY, true ) ) {
				continue;
			}
			$result[] = $field;
		}

		return $result;
	}

	/** @param array<string,mixed> $input @return array<string,mixed>|WP_Error */
	private function update( array $input ) {
		$allowed = array();
		foreach ( $this->fields() as $field ) {
			$allowed[ substr( (string) $field['name'], strlen( 'markea_' ) ) ] = $field;
		}

		$changes = array();
		$writes  = array();
		foreach ( (array) $input['fields'] as $name => $value ) {
			$field = $allowed[ (string) $name ] ?? null;
			if ( ! $field ) {
				return new WP_Error( 'markea_unknown_setting', sprintf( 'El ajuste "%s" no existe o no se puede gestionar.', $name ), array( 'status' => 400 ) );
			}

			$clean = FieldAccess::sanitize( $field, $value );
			if ( is_wp_error( $clean ) ) {
				return $clean;
			}
			$writes[]  = array( (string) $field['name'], $clean );
			$changes[] = array( 'field' => (string) $name, 'old' => get_field( (string) $field['name'], 'option', false ), 'new' => $clean );
		}

		if ( ! $writes ) {
			return new WP_Error( 'markea_nothing_to_do', 'No se indicó ningún ajuste.', array( 'status' => 400 ) );
		}

		foreach ( $writes as [ $name, $value ] ) {
			update_field( $name, $value, 'option' );
		}

		Audit::record( 'markea/settings-update', 'Site Settings', $changes );

		return array( 'updated' => array_column( $changes, 'field' ) );
	}
}
