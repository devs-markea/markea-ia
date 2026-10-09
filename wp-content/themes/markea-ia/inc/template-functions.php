<?php
/**
 * Template helpers. Read-only: data is defined by the markea-core plugin (ACF fields prefixed markea_).
 *
 * @package Markea_IA
 */

defined( 'ABSPATH' ) || exit;

/**
 * Page field (ACF if active, post meta otherwise).
 *
 * @param mixed $default Returned when the field is empty.
 * @return mixed
 */
function markea_field( string $name, $default = '', ?int $post_id = null ) {
	$key     = 'markea_' . $name;
	$post_id = $post_id ?: get_the_ID();
	$value   = function_exists( 'get_field' ) ? get_field( $key, $post_id ) : get_post_meta( $post_id, $key, true );

	return ( null === $value || '' === $value || false === $value ) ? $default : $value;
}

/**
 * Repeater rows as an array (empty if none).
 */
function markea_rows( string $name, ?int $post_id = null ): array {
	$rows = markea_field( $name, array(), $post_id );

	return is_array( $rows ) ? $rows : array();
}

/**
 * Site Settings value (ACF Options Page).
 *
 * @param mixed $default Returned when the option is empty.
 * @return mixed
 */
function markea_option( string $name, $default = '' ) {
	$key   = 'markea_' . $name;
	$value = function_exists( 'get_field' ) ? get_field( $key, 'option' ) : get_option( 'options_' . $key );

	return ( null === $value || '' === $value || false === $value ) ? $default : $value;
}

/**
 * Split a textarea into trimmed, non-empty lines.
 *
 * @return string[]
 */
function markea_lines( string $text ): array {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\R/', $text ) ?: array() ) ) );
}

/**
 * Split comma separated values.
 *
 * @return string[]
 */
function markea_tags( string $text ): array {
	return array_values( array_filter( array_map( 'trim', explode( ',', $text ) ) ) );
}

/**
 * Responsive, lazy image from an ACF value (ID or array).
 *
 * @param mixed $image ID or ACF image array.
 */
function markea_image( $image, string $size = 'large', array $attr = array() ): string {
	$id = is_array( $image ) ? (int) ( $image['ID'] ?? 0 ) : (int) $image;

	return $id ? wp_get_attachment_image( $id, $size, false, array_merge( array( 'loading' => 'lazy', 'decoding' => 'async' ), $attr ) ) : '';
}

/**
 * Escape a title and wrap the highlighted fragment, if present.
 */
function markea_highlight( string $title, string $highlight ): string {
	$title = esc_html( $title );
	if ( '' === $highlight ) {
		return $title;
	}

	$needle = esc_html( $highlight );

	return str_contains( $title, $needle )
		? str_replace( $needle, '<span class="u-highlight">' . $needle . '</span>', $title )
		: $title;
}

/**
 * Primary CTA link. Tracked as a "Hablemos" click.
 */
function markea_cta( string $location, string $class = 'btn btn--primary' ): string {
	$label = (string) markea_field( 'hero_cta_label', '', (int) get_option( 'page_on_front' ) ?: null );
	if ( '' === $label ) {
		return '';
	}

	$url = (string) markea_field( 'hero_cta_url', '#contacto', (int) get_option( 'page_on_front' ) ?: null );

	return sprintf(
		'<a class="%1$s" href="%2$s" data-track="cta" data-track-label="%3$s" data-track-location="%4$s">%3$s</a>',
		esc_attr( $class ),
		esc_url( $url ),
		esc_html( $label ),
		esc_attr( $location )
	);
}

/**
 * Projects ordered manually (menu_order), then title.
 */
function markea_get_projects(): WP_Query {
	return new WP_Query(
		array(
			'post_type'      => 'proyecto',
			'posts_per_page' => 50,
			'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
			'no_found_rows'  => true,
		)
	);
}

/**
 * Project categories that have at least one project.
 *
 * @return WP_Term[]
 */
function markea_get_project_categories(): array {
	$terms = get_terms( array( 'taxonomy' => 'categoria-proyecto', 'hide_empty' => true ) );

	return is_wp_error( $terms ) ? array() : $terms;
}

/**
 * Strategists ordered manually.
 */
function markea_get_strategists(): WP_Query {
	return new WP_Query(
		array(
			'post_type'      => 'estratega',
			'posts_per_page' => 20,
			'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
			'no_found_rows'  => true,
		)
	);
}

/**
 * Social links from Site Settings: label => url.
 *
 * @return array<string,string>
 */
function markea_social_links(): array {
	$links = array(
		'LinkedIn'  => (string) markea_option( 'social_linkedin' ),
		'Instagram' => (string) markea_option( 'social_instagram' ),
		'Facebook'  => (string) markea_option( 'social_facebook' ),
	);

	return array_filter( $links );
}
