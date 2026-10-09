<?php
/**
 * Asset loading (Vite build output or Vite dev server).
 *
 * Define MARKEA_VITE_DEV (e.g. 'http://localhost:5173') in wp-config.php to use the dev server.
 *
 * @package Markea_IA
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', 'markea_ia_enqueue_assets' );
add_filter( 'script_loader_tag', 'markea_ia_module_script_tag', 10, 3 );

/**
 * Read the Vite manifest entry for the main file.
 */
function markea_ia_manifest_entry(): ?array {
	static $manifest = null;

	if ( null === $manifest ) {
		$path     = MARKEA_IA_DIR . '/assets/dist/.vite/manifest.json';
		$manifest = is_readable( $path ) ? json_decode( (string) file_get_contents( $path ), true ) : array();
	}

	return $manifest['assets/src/js/main.js'] ?? null;
}

function markea_ia_enqueue_assets(): void {
	if ( defined( 'MARKEA_VITE_DEV' ) && MARKEA_VITE_DEV ) {
		$dev = untrailingslashit( MARKEA_VITE_DEV );
		wp_enqueue_script( 'markea-vite-client', $dev . '/@vite/client', array(), null, array( 'in_footer' => true ) );
		wp_enqueue_script( 'markea-ia', $dev . '/assets/src/js/main.js', array(), null, array( 'in_footer' => true ) );
		return;
	}

	$entry = markea_ia_manifest_entry();
	if ( ! $entry ) {
		return; // Not built yet: run `npm run build` in the theme.
	}

	$base = MARKEA_IA_URI . '/assets/dist/';

	foreach ( $entry['css'] ?? array() as $css ) {
		wp_enqueue_style( 'markea-ia', $base . $css, array(), null );
	}

	wp_enqueue_script( 'markea-ia', $base . $entry['file'], array(), null, array( 'in_footer' => true, 'strategy' => 'defer' ) );
}

/**
 * Vite outputs ES modules.
 */
function markea_ia_module_script_tag( string $tag, string $handle, string $src ): string {
	if ( in_array( $handle, array( 'markea-ia', 'markea-vite-client' ), true ) ) {
		return '<script type="module" src="' . esc_url( $src ) . '"></script>' . "\n";
	}

	return $tag;
}
