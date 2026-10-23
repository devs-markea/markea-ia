<?php
declare( strict_types=1 );

namespace Markea\Core\Abilities;

use WP_Error;

defined( 'ABSPATH' ) || exit;

/**
 * Reads ACF field definitions and validates / sanitizes values by field type before anything is written.
 * Only plain value types are writable here; structure (repeater rows, layouts) goes through dedicated abilities.
 */
final class FieldAccess {

	/** Types a client can write. */
	private const WRITABLE = array( 'text', 'textarea', 'wysiwyg', 'email', 'url', 'select', 'true_false', 'number', 'image', 'color_picker' );

	/** @return array<string,array<string,mixed>> Landing layouts keyed by name. */
	public static function landing_layouts(): array {
		$field   = acf_get_field( 'field_markea_sections' );
		$layouts = is_array( $field ) ? (array) ( $field['layouts'] ?? array() ) : array();
		$by_name = array();

		foreach ( $layouts as $layout ) {
			$by_name[ (string) $layout['name'] ] = $layout;
		}

		return $by_name;
	}

	/** @param array<int,array<string,mixed>> $fields */
	public static function find( array $fields, string $name ): ?array {
		foreach ( $fields as $field ) {
			if ( ( $field['name'] ?? '' ) === $name ) {
				return $field;
			}
		}

		return null;
	}

	public static function is_writable( array $field ): bool {
		return in_array( $field['type'] ?? '', self::WRITABLE, true );
	}

	/**
	 * Client-facing description of fields (recursive for repeaters).
	 *
	 * @param array<int,array<string,mixed>> $fields
	 * @return array<int,array<string,mixed>>
	 */
	public static function describe( array $fields, string $strip_prefix = '' ): array {
		$out = array();

		foreach ( $fields as $field ) {
			$type = (string) ( $field['type'] ?? '' );
			if ( in_array( $type, array( 'tab', 'message', 'accordion' ), true ) ) {
				continue;
			}

			$item = array(
				'name'     => '' !== $strip_prefix ? preg_replace( '/^' . preg_quote( $strip_prefix, '/' ) . '/', '', (string) $field['name'] ) : (string) $field['name'],
				'label'    => (string) ( $field['label'] ?? '' ),
				'type'     => $type,
				'writable' => self::is_writable( $field ),
			);
			if ( ! empty( $field['instructions'] ) ) {
				$item['help'] = (string) $field['instructions'];
			}
			if ( 'select' === $type ) {
				$item['choices'] = array_map( 'strval', array_keys( (array) ( $field['choices'] ?? array() ) ) );
			}
			if ( 'repeater' === $type ) {
				$item['fields'] = self::describe( (array) ( $field['sub_fields'] ?? array() ) );
				if ( ! empty( $field['max'] ) ) {
					$item['max_rows'] = (int) $field['max'];
				}
			}

			$out[] = $item;
		}

		return $out;
	}

	/**
	 * Validates and sanitizes one value for a field. Returns the value to store or a WP_Error.
	 *
	 * @param mixed $value
	 * @return mixed|WP_Error
	 */
	public static function sanitize( array $field, $value ) {
		$name = (string) ( $field['name'] ?? '' );
		$type = (string) ( $field['type'] ?? '' );

		if ( ! self::is_writable( $field ) ) {
			return new WP_Error( 'markea_field_not_writable', sprintf( 'El campo "%s" (%s) no se puede escribir con esta herramienta.', $name, $type ), array( 'status' => 400 ) );
		}

		if ( 'true_false' === $type ) {
			return filter_var( $value, FILTER_VALIDATE_BOOLEAN ) ? 1 : 0;
		}

		if ( ! is_scalar( $value ) && null !== $value ) {
			return new WP_Error( 'markea_field_invalid', sprintf( 'El campo "%s" espera un valor simple.', $name ), array( 'status' => 400 ) );
		}

		$text = (string) $value;

		switch ( $type ) {
			case 'text':
				return sanitize_text_field( $text );
			case 'textarea':
				return sanitize_textarea_field( $text );
			case 'wysiwyg':
				return wp_kses_post( $text );
			case 'email':
				return '' === $text || is_email( $text ) ? sanitize_email( $text ) : new WP_Error( 'markea_field_invalid', sprintf( '"%s" no es un correo válido.', $text ), array( 'status' => 400 ) );
			case 'url':
				if ( '' === $text ) {
					return '';
				}
				$clean = esc_url_raw( $text, array( 'http', 'https', 'mailto', 'tel' ) );

				return '' !== $clean ? $clean : new WP_Error( 'markea_field_invalid', sprintf( '"%s" no es una URL válida.', $text ), array( 'status' => 400 ) );
			case 'select':
				$choices = array_map( 'strval', array_keys( (array) ( $field['choices'] ?? array() ) ) );

				return in_array( $text, $choices, true ) ? $text : new WP_Error( 'markea_field_invalid', sprintf( 'El campo "%s" acepta: %s.', $name, implode( ', ', array_map( static fn( $c ) => '' === $c ? '(vacío)' : $c, $choices ) ) ), array( 'status' => 400 ) );
			case 'number':
				return '' === $text || is_numeric( $text ) ? $text : new WP_Error( 'markea_field_invalid', sprintf( 'El campo "%s" espera un número.', $name ), array( 'status' => 400 ) );
			case 'image':
				if ( '' === $text || '0' === $text ) {
					return '';
				}
				$id = (int) $text;

				return $id > 0 && wp_attachment_is_image( $id ) ? $id : new WP_Error( 'markea_field_invalid', sprintf( 'El ID %s no es una imagen de la biblioteca de medios.', $text ), array( 'status' => 400 ) );
			case 'color_picker':
				return 1 === preg_match( '/^#[0-9a-fA-F]{6}$/', $text ) ? strtoupper( $text ) : new WP_Error( 'markea_field_invalid', sprintf( 'El campo "%s" espera un color #RRGGBB.', $name ), array( 'status' => 400 ) );
		}

		return new WP_Error( 'markea_field_invalid', 'Tipo de campo no soportado.', array( 'status' => 400 ) );
	}

	/**
	 * Sanitizes a map of name => value against a field list. All-or-nothing: the first problem aborts.
	 *
	 * @param array<int,array<string,mixed>> $fields
	 * @param array<string,mixed>            $values
	 * @return array<string,mixed>|WP_Error
	 */
	public static function sanitize_map( array $fields, array $values ) {
		$clean = array();

		foreach ( $values as $name => $value ) {
			$field = self::find( $fields, (string) $name );
			if ( ! $field ) {
				return new WP_Error( 'markea_unknown_field', sprintf( 'El campo "%s" no existe en este contexto.', $name ), array( 'status' => 400 ) );
			}

			$result = self::sanitize( $field, $value );
			if ( is_wp_error( $result ) ) {
				return $result;
			}
			$clean[ (string) $name ] = $result;
		}

		return $clean;
	}
}
