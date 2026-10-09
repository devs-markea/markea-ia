<?php
/**
 * Markea IA theme bootstrap. Keep this file minimal: everything lives in inc/.
 *
 * @package Markea_IA
 */

defined( 'ABSPATH' ) || exit;

define( 'MARKEA_IA_VERSION', '0.1.0' );
define( 'MARKEA_IA_DIR', get_template_directory() );
define( 'MARKEA_IA_URI', get_template_directory_uri() );

foreach ( array( 'setup', 'enqueue', 'icons', 'hooks', 'template-functions' ) as $markea_ia_file ) {
	require_once MARKEA_IA_DIR . '/inc/' . $markea_ia_file . '.php';
}
unset( $markea_ia_file );
