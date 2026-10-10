<?php
/**
 * Full-width menu overlay (opened by the header button). Content comes from Site Settings
 * and the "Navegacion principal" menu (Apariencia > Menus).
 *
 * @package Markea_IA
 */

defined( 'ABSPATH' ) || exit;

$markea_eyebrow   = (string) markea_option( 'menu_eyebrow' );
$markea_title     = (string) markea_option( 'menu_title' );
$markea_email     = (string) markea_option( 'contact_email' );
$markea_copyright = (string) markea_option( 'menu_copyright' );
$markea_social    = markea_social_links( array( 'Instagram', 'Facebook', 'LinkedIn' ) );
$markea_icons     = array(
	'Instagram' => 'instagram',
	'Facebook'  => 'facebook',
	'LinkedIn'  => 'linkedin',
);
?>
<div id="site-menu" class="site-menu" role="dialog" aria-modal="true" aria-labelledby="site-menu-title" data-menu data-lenis-prevent>
	<div class="site-menu__card">
		<div class="site-menu__grid">
			<div class="site-menu__intro">
				<?php if ( $markea_eyebrow ) : ?>
					<p class="site-menu__eyebrow"><?php echo esc_html( $markea_eyebrow ); ?></p>
				<?php endif; ?>
				<p id="site-menu-title" class="site-menu__title"><?php echo esc_html( $markea_title ?: __( 'Menú', 'markea-ia' ) ); ?></p>
				<span class="site-menu__rule" aria-hidden="true"></span>
				<?php if ( $markea_email ) : ?>
					<a class="site-menu__email" href="mailto:<?php echo esc_attr( antispambot( $markea_email ) ); ?>" data-track="contact" data-track-type="email">
						<?php echo markea_icon( 'mail', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php echo esc_html( antispambot( $markea_email ) ); ?>
					</a>
				<?php endif; ?>
				<p class="site-menu__cta"><?php echo markea_cta( 'menu' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
			</div>

			<nav class="site-menu__nav" aria-labelledby="site-menu-nav-label">
				<p id="site-menu-nav-label" class="site-menu__label"><?php esc_html_e( 'Navegación', 'markea-ia' ); ?></p>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'site-menu__list',
						'fallback_cb'    => false,
						'depth'          => 1,
					)
				);
				?>
			</nav>
		</div>

		<div class="site-menu__footer">
			<?php if ( $markea_copyright ) : ?>
				<p class="site-menu__copyright"><?php echo esc_html( $markea_copyright ); ?></p>
			<?php endif; ?>
			<?php if ( $markea_social ) : ?>
				<ul class="site-menu__social" role="list">
					<?php foreach ( $markea_social as $markea_label => $markea_url ) : ?>
						<li>
							<a href="<?php echo esc_url( $markea_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $markea_label ); ?>" data-track="contact" data-track-type="social" data-track-network="<?php echo esc_attr( strtolower( $markea_label ) ); ?>">
								<?php echo markea_icon( $markea_icons[ $markea_label ] ?? 'arrow-up-right', 36 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>
</div>
