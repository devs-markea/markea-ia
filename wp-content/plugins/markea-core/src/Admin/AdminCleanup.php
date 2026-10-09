<?php
declare( strict_types=1 );

namespace Markea\Core\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Hides what the marketing team does not use (blog, comments). Administrators keep everything.
 * Disable with: add_filter( 'markea_hide_blog_menus', '__return_false' );
 */
final class AdminCleanup {

	public function register(): void {
		add_action( 'admin_menu', array( $this, 'hide_menus' ), 999 );
		add_action( 'admin_bar_menu', array( $this, 'hide_admin_bar_nodes' ), 999 );
	}

	public function hide_menus(): void {
		if ( ! $this->should_hide() ) {
			return;
		}

		remove_menu_page( 'edit.php' );
		remove_menu_page( 'edit-comments.php' );
	}

	public function hide_admin_bar_nodes( \WP_Admin_Bar $bar ): void {
		if ( $this->should_hide() ) {
			$bar->remove_node( 'comments' );
			$bar->remove_node( 'new-post' );
		}
	}

	private function should_hide(): bool {
		return ! current_user_can( 'manage_options' ) && (bool) apply_filters( 'markea_hide_blog_menus', true );
	}
}
