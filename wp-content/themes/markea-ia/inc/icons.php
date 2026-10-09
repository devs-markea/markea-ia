<?php
/**
 * Inline SVG icons. One place for every icon so markup stays small and colors follow currentColor.
 *
 * @package Markea_IA
 */

defined( 'ABSPATH' ) || exit;

/**
 * Icon markup (decorative: hidden from assistive technology).
 *
 * @param string $name Icon name: arrow-up-right, menu, close, mail, instagram, facebook, linkedin.
 * @param int    $size Width and height in px.
 */
function markea_icon( string $name, int $size = 20 ): string {
	static $icons = array(
		// Lucide-style 24 px grid, stroke icons.
		'arrow-up-right' => array( '0 0 24 24', '<path d="M7 17 17 7"/><path d="M7 7h10v10"/>', '2' ),
		'mail'           => array( '0 0 24 24', '<rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>', '2' ),
		'instagram'      => array( '0 0 24 24', '<rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><path d="M17.5 6.5h.01"/>', '1.75' ),
		'facebook'       => array( '0 0 24 24', '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>', '1.75' ),
		'linkedin'       => array( '0 0 24 24', '<path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect width="4" height="12" x="2" y="9"/><circle cx="4" cy="4" r="2"/>', '1.75' ),
		// Figma header icons (34 px grid, 1 px lines).
		'menu'           => array( '0 0 34 34', '<path d="M0 13.5H27"/><path d="M5 23.5H34"/>', '1' ),
		'close'          => array( '0 0 38 38', '<path d="M9 9 29 29"/><path d="M29 9 9 29"/>', '1' ),
	);

	if ( ! isset( $icons[ $name ] ) ) {
		return '';
	}

	[ $view_box, $paths, $stroke ] = $icons[ $name ];

	return sprintf(
		'<svg class="icon icon--%1$s" width="%2$d" height="%2$d" viewBox="%3$s" fill="none" stroke="currentColor" stroke-width="%4$s" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%5$s</svg>',
		esc_attr( $name ),
		$size,
		esc_attr( $view_box ),
		esc_attr( $stroke ),
		$paths // phpcs:ignore WordPress.Security.EscapeOutput -- static, trusted SVG paths.
	);
}
