<?php
declare( strict_types=1 );

namespace Markea\Core;

defined( 'ABSPATH' ) || exit;

/**
 * Maintenance mode controlled from Site Settings > Mantenimiento.
 *
 * Visitors get a standalone page with HTTP 503 (so search engines do not index it); logged-in users who can
 * edit pages keep seeing the real site. wp-admin, wp-login, REST, cron, AJAX and WP-CLI are never blocked.
 */
final class Maintenance {

	public function register(): void {
		add_action( 'template_redirect', array( $this, 'maybe_block' ), 0 );
		add_action( 'admin_bar_menu', array( $this, 'admin_bar_notice' ), 100 );
		add_action( 'admin_notices', array( $this, 'admin_notice' ) );
	}

	public function active(): bool {
		return function_exists( 'get_field' ) && (bool) get_field( 'markea_maintenance_on', 'option' );
	}

	public function maybe_block(): void {
		if ( ! $this->active() || $this->bypass() ) {
			return;
		}

		status_header( 503 );
		header( 'Retry-After: 3600' );
		header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
		nocache_headers();
		header( 'X-Robots-Tag: noindex, nofollow' );

		$this->render();
		exit;
	}

	public function admin_bar_notice( \WP_Admin_Bar $bar ): void {
		if ( ! $this->active() || ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		$bar->add_node(
			array(
				'id'    => 'markea-maintenance',
				'title' => esc_html__( 'Modo mantenimiento activo', 'markea-core' ),
				'href'  => current_user_can( 'manage_options' ) ? admin_url( 'admin.php?page=markea-settings' ) : false,
				'meta'  => array( 'class' => 'markea-maintenance-node' ),
			)
		);
	}

	public function admin_notice(): void {
		if ( ! $this->active() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		printf(
			'<div class="notice notice-warning"><p>%s <a href="%s">%s</a></p></div>',
			esc_html__( 'El sitio está en modo mantenimiento: los visitantes ven la pantalla de aviso.', 'markea-core' ),
			esc_url( admin_url( 'admin.php?page=markea-settings' ) ),
			esc_html__( 'Desactivar en Site Settings > Mantenimiento', 'markea-core' )
		);
	}

	/** Users and request types that must always see the real site. */
	private function bypass(): bool {
		if ( is_user_logged_in() && current_user_can( 'edit_pages' ) ) {
			return true;
		}

		return is_admin()
			|| wp_doing_ajax()
			|| wp_doing_cron()
			|| ( defined( 'REST_REQUEST' ) && REST_REQUEST )
			|| ( defined( 'WP_CLI' ) && WP_CLI );
	}

	private function render(): void {
		$text = static function ( string $name, string $fallback ): string {
			$value = get_field( 'markea_' . $name, 'option' );

			return is_string( $value ) && '' !== trim( $value ) ? $value : $fallback;
		};
		$color = static function ( string $name, string $fallback ): string {
			$value = get_field( 'markea_color_' . $name, 'option' );

			return is_string( $value ) && preg_match( '/^#[0-9a-f]{6}$/i', $value ) ? $value : $fallback;
		};

		$title   = $text( 'maintenance_title', 'Estamos mejorando el sitio' );
		$message = $text( 'maintenance_message', 'Volvemos muy pronto con una nueva versión. Gracias por tu paciencia.' );
		$email   = (string) get_field( 'markea_contact_email', 'option' );
		$navy    = $color( 'navy', '#001B71' );
		$electric = $color( 'electric', '#0049FF' );

		$logo_id  = (int) get_field( 'markea_brand_logo', 'option' );
		$logo_url = $logo_id ? (string) wp_get_attachment_image_url( $logo_id, 'medium' ) : '';
		if ( '' === $logo_url && is_readable( get_stylesheet_directory() . '/assets/images/markea-logo-navy.svg' ) ) {
			$logo_url = get_stylesheet_directory_uri() . '/assets/images/markea-logo-navy.svg';
		}
		?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex, nofollow">
	<title><?php echo esc_html( $title . ' - ' . get_bloginfo( 'name' ) ); ?></title>
	<style>
		:root{--navy:<?php echo esc_html( $navy ); ?>;--electric:<?php echo esc_html( $electric ); ?>}
		*{box-sizing:border-box}
		body{margin:0;min-height:100vh;display:grid;place-items:center;padding:24px;background:#fff;color:var(--navy);font-family:Poppins,system-ui,-apple-system,"Segoe UI",Roboto,sans-serif;line-height:1.6;text-align:center}
		main{max-width:560px}
		img{display:block;max-width:200px;height:auto;margin:0 auto 40px}
		h1{margin:0 0 16px;font-family:Montserrat,system-ui,sans-serif;font-size:clamp(28px,6vw,40px);line-height:1.15;letter-spacing:-.02em;font-weight:600}
		p{margin:0 0 24px;font-size:16px;opacity:.8}
		a{color:var(--electric);font-weight:600}
	</style>
</head>
<body>
	<main>
		<?php if ( '' !== $logo_url ) : ?>
			<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
		<?php endif; ?>
		<h1><?php echo esc_html( $title ); ?></h1>
		<p><?php echo esc_html( $message ); ?></p>
		<?php if ( is_email( $email ) ) : ?>
			<p><a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a></p>
		<?php endif; ?>
	</main>
</body>
</html>
		<?php
	}
}
