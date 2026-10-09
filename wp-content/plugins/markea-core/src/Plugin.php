<?php
declare( strict_types=1 );

namespace Markea\Core;

use Markea\Core\Admin\AdminCleanup;
use Markea\Core\Admin\Roles;
use Markea\Core\Cli\SeedCommand;
use Markea\Core\Fields\ContentFields;
use Markea\Core\Fields\LandingFields;
use Markea\Core\Fields\SiteSettings;
use Markea\Core\Leads\LeadAdmin;
use Markea\Core\Leads\LeadEndpoint;

defined( 'ABSPATH' ) || exit;

/**
 * Wires every module. Each module registers its own hooks.
 */
final class Plugin {

	public static function boot(): void {
		( new PostTypes() )->register();
		( new Tracking() )->register();
		( new LeadEndpoint() )->register();
		( new LeadAdmin() )->register();
		( new Roles() )->register();
		( new AdminCleanup() )->register();

		// ACF-dependent modules.
		add_action( 'acf/init', array( self::class, 'register_fields' ) );
		add_action( 'admin_notices', array( self::class, 'acf_notice' ) );

		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			\WP_CLI::add_command( 'markea seed', SeedCommand::class );
		}
	}

	public static function register_fields(): void {
		( new SiteSettings() )->register();
		( new LandingFields() )->register();
		( new ContentFields() )->register();
	}

	public static function acf_notice(): void {
		if ( function_exists( 'acf_add_local_field_group' ) || ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		echo '<div class="notice notice-error"><p>' . esc_html__( 'Markea Core necesita ACF Pro activo para mostrar los campos de la landing y Site Settings.', 'markea-core' ) . '</p></div>';
	}
}
