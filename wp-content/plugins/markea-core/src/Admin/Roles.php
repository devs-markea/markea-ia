<?php
declare( strict_types=1 );

namespace Markea\Core\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Capabilities for leads. Administrator and Editor can read and delete leads; nobody can create them by hand.
 */
final class Roles {

	private const VERSION_OPTION = 'markea_core_caps_version';

	public function register(): void {
		add_action( 'init', array( $this, 'maybe_sync' ), 20 );
	}

	public static function activate(): void {
		( new self() )->sync();
	}

	public function maybe_sync(): void {
		if ( get_option( self::VERSION_OPTION ) !== MARKEA_CORE_VERSION ) {
			$this->sync();
		}
	}

	public function sync(): void {
		$caps = array(
			'edit_leads',
			'edit_others_leads',
			'edit_private_leads',
			'edit_published_leads',
			'read_private_leads',
			'delete_leads',
			'delete_others_leads',
			'delete_private_leads',
			'delete_published_leads',
			'publish_leads',
		);

		foreach ( array( 'administrator', 'editor' ) as $role_name ) {
			$role = get_role( $role_name );
			if ( ! $role ) {
				continue;
			}
			foreach ( $caps as $cap ) {
				$role->add_cap( $cap );
			}
		}

		update_option( self::VERSION_OPTION, MARKEA_CORE_VERSION, false );
	}
}
