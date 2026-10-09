<?php
declare( strict_types=1 );

namespace Markea\Core\Fields;

defined( 'ABSPATH' ) || exit;

/**
 * Tiny ACF field-definition builder. Field names are prefixed markea_ and keys are deterministic
 * (field_markea_<name>) so the seeder and the theme can refer to them.
 */
final class F {

	public static function key( string $name ): string {
		return 'field_markea_' . $name;
	}

	/**
	 * @param array<string,mixed> $extra
	 * @return array<string,mixed>
	 */
	public static function field( string $type, string $name, string $label, array $extra = array() ): array {
		return array_merge(
			array(
				'key'   => self::key( $name ),
				'name'  => 'markea_' . $name,
				'label' => $label,
				'type'  => $type,
			),
			$extra
		);
	}

	/** @return array<string,mixed> */
	public static function tab( string $id, string $label ): array {
		return array(
			'key'       => self::key( 'tab_' . $id ),
			'label'     => $label,
			'name'      => '',
			'type'      => 'tab',
			'placement' => 'top',
		);
	}

	/** @param array<string,mixed> $extra @return array<string,mixed> */
	public static function text( string $name, string $label, array $extra = array() ): array {
		return self::field( 'text', $name, $label, $extra );
	}

	/** @param array<string,mixed> $extra @return array<string,mixed> */
	public static function textarea( string $name, string $label, array $extra = array() ): array {
		return self::field( 'textarea', $name, $label, array_merge( array( 'rows' => 4, 'new_lines' => '' ), $extra ) );
	}

	/** @param array<string,mixed> $extra @return array<string,mixed> */
	public static function wysiwyg( string $name, string $label, array $extra = array() ): array {
		return self::field( 'wysiwyg', $name, $label, array_merge( array( 'media_upload' => 0, 'toolbar' => 'basic' ), $extra ) );
	}

	/** @param array<string,mixed> $extra @return array<string,mixed> */
	public static function url( string $name, string $label, array $extra = array() ): array {
		return self::field( 'url', $name, $label, $extra );
	}

	/** @param array<string,mixed> $extra @return array<string,mixed> */
	public static function email( string $name, string $label, array $extra = array() ): array {
		return self::field( 'email', $name, $label, $extra );
	}

	/** @param array<string,mixed> $extra @return array<string,mixed> */
	public static function image( string $name, string $label, array $extra = array() ): array {
		return self::field( 'image', $name, $label, array_merge( array( 'return_format' => 'id', 'preview_size' => 'thumbnail', 'library' => 'all' ), $extra ) );
	}

	/** @param array<string,mixed> $extra @return array<string,mixed> */
	public static function gallery( string $name, string $label, array $extra = array() ): array {
		return self::field( 'gallery', $name, $label, array_merge( array( 'return_format' => 'id', 'preview_size' => 'thumbnail', 'library' => 'all' ), $extra ) );
	}

	/** @param array<string,mixed> $extra @return array<string,mixed> */
	public static function toggle( string $name, string $label, array $extra = array() ): array {
		return self::field( 'true_false', $name, $label, array_merge( array( 'ui' => 1 ), $extra ) );
	}

	/**
	 * Repeater. Sub fields are built with sub() and named without the markea_ prefix (title, text...).
	 *
	 * @param array<int,array<string,mixed>> $subs
	 * @param array<string,mixed>            $extra
	 * @return array<string,mixed>
	 */
	public static function repeater( string $name, string $label, array $subs, array $extra = array() ): array {
		foreach ( $subs as &$sub ) {
			$sub['key'] = self::key( $name . '_' . $sub['name'] );
		}
		unset( $sub );

		return self::field(
			'repeater',
			$name,
			$label,
			array_merge(
				array(
					'layout'       => 'block',
					'button_label' => 'Añadir fila',
					'sub_fields'   => $subs,
				),
				$extra
			)
		);
	}

	/**
	 * Repeater sub field. The key is completed by repeater().
	 *
	 * @param array<string,mixed> $extra
	 * @return array<string,mixed>
	 */
	public static function sub( string $type, string $name, string $label, array $extra = array() ): array {
		return array_merge( array( 'type' => $type, 'name' => $name, 'label' => $label ), $extra );
	}

	/**
	 * Flexible Content layout. Sub field names are NOT prefixed; keys are assigned recursively
	 * as field_markea_<layout>_<name>[_<name>...] so the seeder and ACF can address them.
	 *
	 * @param array<int,array<string,mixed>> $subs
	 * @return array<string,mixed>
	 */
	public static function layout( string $name, string $label, array $subs ): array {
		// Every section has an eyebrow label (first) and a light/dark tone (last).
		array_unshift( $subs, self::sub( 'text', 'eyebrow', 'Etiqueta superior' ) );

		// Every section can be shown on a light or a dark (navy) background.
		$subs[] = self::sub(
			'select',
			'tone',
			'Tono de la sección',
			array( 'choices' => array( 'light' => 'Claro', 'dark' => 'Oscuro' ), 'default_value' => 'light', 'ui' => 0 )
		);

		return array(
			'key'        => 'layout_markea_' . $name,
			'name'       => $name,
			'label'      => $label,
			'display'    => 'block',
			'sub_fields' => self::assign_keys( $subs, self::key( $name ) ),
		);
	}

	/**
	 * @param array<int,array<string,mixed>> $fields
	 * @return array<int,array<string,mixed>>
	 */
	private static function assign_keys( array $fields, string $parent ): array {
		foreach ( $fields as &$field ) {
			$field['key'] = $parent . '_' . $field['name'];
			if ( ! empty( $field['sub_fields'] ) ) {
				$field['sub_fields'] = self::assign_keys( $field['sub_fields'], $field['key'] );
			}
		}
		unset( $field );

		return $fields;
	}

	/**
	 * Repeater used inside a layout (unprefixed name).
	 *
	 * @param array<int,array<string,mixed>> $subs
	 * @param array<string,mixed>            $extra
	 * @return array<string,mixed>
	 */
	public static function items( string $name, string $label, array $subs, array $extra = array() ): array {
		return self::sub( 'repeater', $name, $label, array_merge( array( 'layout' => 'block', 'button_label' => 'Añadir fila', 'sub_fields' => $subs ), $extra ) );
	}
}
