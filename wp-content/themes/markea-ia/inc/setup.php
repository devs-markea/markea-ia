<?php
/**
 * Theme setup.
 *
 * @package Markea_IA
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', 'markea_ia_setup' );

function markea_ia_setup(): void {
	load_theme_textdomain( 'markea-ia', MARKEA_IA_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'custom-logo' );

	add_image_size( 'markea-card', 720, 480, true );
	add_image_size( 'markea-portrait', 480, 600, true );
	add_image_size( 'markea-logo', 320, 160, false );

	register_nav_menus(
		array(
			'primary' => __( 'Navegación principal', 'markea-ia' ),
		)
	);
}
