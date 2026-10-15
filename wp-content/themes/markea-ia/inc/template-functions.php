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
 * Escape a title and show the highlighted text: wrapped in place when it is a fragment of the title,
 * otherwise added after the title as its own highlighted line.
 */
function markea_highlight( string $title, string $highlight ): string {
	$title     = esc_html( $title );
	$highlight = trim( $highlight );
	if ( '' === $highlight ) {
		return $title;
	}

	$needle = esc_html( $highlight );

	if ( str_contains( $title, $needle ) ) {
		return str_replace( $needle, '<span class="u-highlight">' . $needle . '</span>', $title );
	}

	return $title . ' <span class="u-highlight u-highlight--line">' . $needle . '</span>';
}

/**
 * Landing sections (Flexible Content rows) of the page being shown.
 *
 * @return array<int,array<string,mixed>>
 */
function markea_sections(): array {
	static $cache = array();

	$post_id = get_queried_object_id() ?: (int) get_option( 'page_on_front' );
	if ( ! isset( $cache[ $post_id ] ) ) {
		$cache[ $post_id ] = $post_id ? markea_rows( 'sections', $post_id ) : array();
	}

	return $cache[ $post_id ];
}

/**
 * First row of a layout (e.g. the hero), or an empty array.
 *
 * @return array<string,mixed>
 */
function markea_section( string $layout ): array {
	foreach ( markea_sections() as $row ) {
		if ( ( $row['acf_fc_layout'] ?? '' ) === $layout ) {
			return $row;
		}
	}

	return array();
}

/**
 * Primary CTA link (label and URL live in the hero layout). Tracked as a "Hablemos" click.
 */
function markea_cta( string $location, string $class = 'btn btn--primary' ): string {
	$hero  = markea_section( 'hero' );
	$label = (string) ( $hero['cta_label'] ?? '' );
	if ( '' === $label ) {
		return '';
	}

	$url = (string) ( $hero['cta_url'] ?? '' ) ?: '#contacto';

	return sprintf(
		'<a class="%1$s" href="%2$s" data-track="cta" data-track-label="%3$s" data-track-location="%4$s"><span>%3$s</span>%5$s</a>',
		esc_attr( $class ),
		esc_url( $url ),
		esc_html( $label ),
		esc_attr( $location ),
		markea_icon( 'arrow-up-right' ) // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG.
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
 * Project categories in creation order (all of them, like the design, even if one has no projects yet).
 *
 * @return WP_Term[]
 */
function markea_get_project_categories(): array {
	$terms = get_terms( array( 'taxonomy' => 'categoria-proyecto', 'hide_empty' => false, 'orderby' => 'term_id' ) );

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
function markea_social_links( array $order = array( 'LinkedIn', 'Instagram', 'Facebook' ) ): array {
	$all = array(
		'LinkedIn'  => (string) markea_option( 'social_linkedin' ),
		'Instagram' => (string) markea_option( 'social_instagram' ),
		'Facebook'  => (string) markea_option( 'social_facebook' ),
	);

	$links = array();
	foreach ( $order as $label ) {
		if ( ! empty( $all[ $label ] ) ) {
			$links[ $label ] = $all[ $label ];
		}
	}

	return $links;
}

/**
 * Attributes that turn an element into a Swiper carousel (mounted by assets/src/js/carousel.js).
 *
 * @param string              $media   Media query where it is a carousel; outside it the list stays a normal grid.
 * @param array<string,mixed> $options Swiper options, e.g. array( 'slidesPerView' => 1.5 ).
 */
function markea_carousel_attrs( string $media = '(max-width: 767.98px)', array $options = array() ): string {
	return sprintf(
		'data-swiper data-swiper-mq="%s" data-swiper-config="%s"',
		esc_attr( $media ),
		esc_attr( (string) wp_json_encode( (object) $options ) )
	);
}

/**
 * Brand logo <img>: Site Settings logo first, then the native Customizer logo, else empty.
 */
function markea_logo( string $size = 'markea-logo', array $attr = array() ): string {
	$logo = markea_image( markea_option( 'brand_logo' ), $size, $attr );
	if ( $logo ) {
		return $logo;
	}

	$custom = (int) get_theme_mod( 'custom_logo' );
	if ( $custom ) {
		return markea_image( $custom, $size, $attr );
	}

	// Default: the Figma logo that ships with the theme.
	return sprintf(
		'<img src="%s" width="193" height="26" alt="" decoding="async">',
		esc_url( MARKEA_IA_URI . '/assets/images/markea-logo.svg' )
	);
}

/**
 * CSS modifier for a section row: " section--dark" when the editor chose the dark tone.
 *
 * @param array<string,mixed> $row Flexible Content row.
 */
function markea_tone( array $row ): string {
	return 'dark' === ( $row['tone'] ?? '' ) ? ' section--dark' : '';
}

/**
 * Eyebrow label of a section row.
 *
 * @param array<string,mixed> $row Flexible Content row.
 */
function markea_eyebrow( array $row ): string {
	$text = trim( (string) ( $row['eyebrow'] ?? '' ) );

	return '' === $text ? '' : '<p class="eyebrow">' . esc_html( $text ) . '</p>';
}

/**
 * Lead paragraph of a section row (field "text" as plain text).
 *
 * @param array<string,mixed> $row Flexible Content row.
 */
function markea_lead( array $row ): string {
	$text = trim( (string) ( $row['text'] ?? '' ) );

	return '' === $text ? '' : '<p class="section__lead">' . esc_html( $text ) . '</p>';
}

/**
 * Section title with the "emphasis" fragment in bold (the rest uses the lighter weight).
 *
 * @param array<string,mixed> $row Flexible Content row.
 */
function markea_title( array $row, string $key = 'title' ): string {
	$title    = (string) ( $row[ $key ] ?? '' );
	$emphasis = trim( (string) ( $row['emphasis'] ?? '' ) );
	$html     = esc_html( $title );

	if ( '' === $emphasis ) {
		return $html;
	}

	$needle = esc_html( $emphasis );

	return str_contains( $html, $needle ) ? str_replace( $needle, '<strong class="u-strong">' . $needle . '</strong>', $html ) : $html;
}

/**
 * Logo for a tool: the uploaded image, or the Figma default shipped in assets/images/tools/<slug>.svg
 * (slug of the tool name). Returns an empty string when neither exists.
 *
 * @param array<string,mixed> $item Repeater row with name and logo.
 */
function markea_tool_logo( array $item ): string {
	$uploaded = markea_image( $item['logo'] ?? 0, 'markea-logo', array( 'class' => 'tools__logo' ) );
	if ( $uploaded ) {
		return $uploaded;
	}

	$slug = sanitize_title( (string) ( $item['name'] ?? '' ) );
	$file = MARKEA_IA_DIR . '/assets/images/tools/' . $slug . '.svg';
	if ( '' === $slug || ! is_readable( $file ) ) {
		return '';
	}

	// Intrinsic size from the SVG viewBox (design size at 1x).
	$width  = 80;
	$height = 24;
	if ( 1 === preg_match( '/viewBox="[\d.\-]+ [\d.\-]+ ([\d.]+) ([\d.]+)"/', (string) file_get_contents( $file ), $m ) ) {
		$width  = (float) $m[1];
		$height = (float) $m[2];
	}

	return sprintf(
		'<img class="tools__logo" src="%s" width="%s" height="%s" alt="" loading="lazy" decoding="async">',
		esc_url( MARKEA_IA_URI . '/assets/images/tools/' . $slug . '.svg' ),
		esc_attr( (string) round( $width ) ),
		esc_attr( (string) round( $height ) )
	);
}

/**
 * Motion (smooth scroll, reveals, menu and button effects) is on unless switched off in Site Settings > Animaciones.
 */
function markea_motion_enabled(): bool {
	return ! (bool) markea_option( 'motion_off' );
}

/**
 * The entry loader only runs on the front page and only when motion is on.
 */
function markea_loader_enabled(): bool {
	return is_front_page() && markea_motion_enabled() && (bool) markea_option( 'loader_on' );
}

/**
 * Readable text color (white or navy) over a hex background, by relative luminance (WCAG).
 */
function markea_text_on( string $hex ): string {
	$hex = ltrim( $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	if ( 6 !== strlen( $hex ) || ! ctype_xdigit( $hex ) ) {
		return '#ffffff';
	}

	$channels = array_map(
		static function ( string $pair ): float {
			$c = hexdec( $pair ) / 255;

			return $c <= 0.03928 ? $c / 12.92 : ( ( $c + 0.055 ) / 1.055 ) ** 2.4;
		},
		str_split( $hex, 2 )
	);
	$luminance = 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];

	// White text needs a luminance of at most ~0.18 to reach 4.5:1; above that, navy reads better.
	return $luminance > 0.18 ? '#001b71' : '#ffffff';
}
