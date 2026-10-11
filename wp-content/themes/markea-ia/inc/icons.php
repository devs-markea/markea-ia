<?php
/**
 * Inline SVG icons exported from Figma (assets/icons/*.svg). One place for every icon so markup stays
 * small and colors follow currentColor. Files are theme assets, not user input.
 *
 * @package Markea_IA
 */

defined( 'ABSPATH' ) || exit;

/**
 * Icon markup (decorative: hidden from assistive technology).
 *
 * @param string $name File name without extension in assets/icons (arrow-up-right, menu, close, mail,
 *                     instagram, facebook, linkedin, clock, user-check, activity...).
 * @param int    $size Width and height in px.
 */
function markea_icon( string $name, int $size = 20 ): string {
	static $cache = array();

	if ( 1 !== preg_match( '/^[a-z0-9-]+$/', $name ) ) {
		return '';
	}

	if ( ! isset( $cache[ $name ] ) ) {
		$file           = MARKEA_IA_DIR . '/assets/icons/' . $name . '.svg';
		$cache[ $name ] = is_readable( $file ) ? trim( (string) file_get_contents( $file ) ) : '';
	}

	if ( '' === $cache[ $name ] ) {
		return '';
	}

	return (string) preg_replace(
		'/^<svg /',
		sprintf( '<svg class="icon icon--%s" width="%d" height="%d" aria-hidden="true" focusable="false" ', esc_attr( $name ), $size, $size ),
		$cache[ $name ],
		1
	);
}

/**
 * Decorative illustration from assets/images/<folder>/<name>.svg, inlined so CSS can animate its parts.
 * Files are theme assets, not user input.
 *
 * @param string $path  Path under assets/images without extension (e.g. mos/stack).
 * @param string $class CSS class for the <svg>.
 */
function markea_inline_svg( string $path, string $class ): string {
	if ( 1 !== preg_match( '#^[a-z0-9/-]+$#', $path ) ) {
		return '';
	}

	$file = MARKEA_IA_DIR . '/assets/images/' . $path . '.svg';
	if ( ! is_readable( $file ) ) {
		return '';
	}

	return (string) preg_replace(
		'/^<svg /',
		sprintf( '<svg class="%s" aria-hidden="true" focusable="false" ', esc_attr( $class ) ),
		trim( (string) file_get_contents( $file ) ),
		1
	);
}
