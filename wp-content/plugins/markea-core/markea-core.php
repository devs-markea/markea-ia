<?php
/**
 * Plugin Name: Markea Core
 * Description: Datos de la landing Markea IA: post types, campos ACF, Site Settings, tracking, leads y seeder.
 * Version: 0.1.0
 * Requires at least: 6.5
 * Requires PHP: 8.2
 * Author: Markea Agency
 * Text Domain: markea-core
 *
 * @package Markea\Core
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

define( 'MARKEA_CORE_VERSION', '0.1.0' );
define( 'MARKEA_CORE_FILE', __FILE__ );
define( 'MARKEA_CORE_DIR', plugin_dir_path( __FILE__ ) );

// PSR-4 autoloader (Markea\Core\Sub\Class => src/Sub/Class.php). No Composer needed at runtime.
spl_autoload_register(
	static function ( string $class ): void {
		$prefix = 'Markea\\Core\\';
		if ( ! str_starts_with( $class, $prefix ) ) {
			return;
		}
		$file = MARKEA_CORE_DIR . 'src/' . str_replace( '\\', '/', substr( $class, strlen( $prefix ) ) ) . '.php';
		if ( is_readable( $file ) ) {
			require_once $file;
		}
	}
);

// Never allow file editing in production, even if wp-config.php forgets it.
if ( ! defined( 'DISALLOW_FILE_EDIT' ) && 'production' === wp_get_environment_type() ) {
	define( 'DISALLOW_FILE_EDIT', true );
}

\Markea\Core\Plugin::boot();
