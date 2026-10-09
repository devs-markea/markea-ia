<?php
/**
 * Theme hooks: cleanup and extension points. Data and tracking scripts live in markea-core.
 *
 * @package Markea_IA
 */

defined( 'ABSPATH' ) || exit;

// Front cleanup (no emoji script, no legacy head links).
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );

add_filter( 'body_class', 'markea_ia_body_class' );
add_action( 'wp_enqueue_scripts', 'markea_ia_dequeue_unused', 100 );

function markea_ia_body_class( array $classes ): array {
	$classes[] = 'markea-ia';

	return $classes;
}

/**
 * The landing does not use classic block styles beyond global styles.
 */
function markea_ia_dequeue_unused(): void {
	wp_dequeue_style( 'classic-theme-styles' );
	wp_dequeue_style( 'wp-block-library' );
}

/**
 * Extension point right after <body> (markea-core prints GTM noscript here).
 */
add_action( 'wp_body_open', static function (): void {
	do_action( 'markea_after_body_open' );
} );

/**
 * Fallback landing template for the front page when no landing template is assigned.
 */
add_filter( 'template_include', 'markea_ia_front_page_template' );

function markea_ia_front_page_template( string $template ): string {
	if ( is_front_page() && is_page() && basename( $template ) !== 'landing.php' && 'page' === get_option( 'show_on_front' ) ) {
		$landing = MARKEA_IA_DIR . '/templates/landing.php';
		if ( is_readable( $landing ) && ! get_page_template_slug() ) {
			return $landing;
		}
	}

	return $template;
}
