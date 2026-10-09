<?php
/**
 * Site footer. All values come from Site Settings.
 *
 * @package Markea_IA
 */

defined( 'ABSPATH' ) || exit;

$markea_email      = (string) markea_option( 'contact_email' );
$markea_phone      = (string) markea_option( 'contact_phone' );
$markea_address    = (string) markea_option( 'contact_address' );
$markea_legal      = (string) markea_option( 'brand_legal_text' );
$markea_badges     = markea_option( 'brand_partner_badges', array() );
$markea_social     = markea_social_links();
$markea_cta_title  = (string) markea_option( 'footer_cta_title' );
$markea_tagline    = (string) markea_option( 'footer_tagline' );
$markea_text       = (string) markea_option( 'footer_text' );
$markea_services   = markea_lines( (string) markea_option( 'footer_services' ) );
$markea_copyright  = (string) markea_option( 'footer_copyright' );
$markea_news_title = (string) markea_option( 'newsletter_title' );
$markea_news_btn   = (string) markea_option( 'newsletter_button' );
$markea_news_url   = (string) markea_option( 'newsletter_action' );
?>
<footer class="site-footer">
	<div class="container">
		<?php if ( $markea_cta_title || $markea_email ) : ?>
			<div class="site-footer__cta">
				<?php if ( $markea_cta_title ) : ?>
					<p class="site-footer__question"><?php echo esc_html( $markea_cta_title ); ?></p>
				<?php endif; ?>
				<?php if ( $markea_email ) : ?>
					<a class="site-footer__email" href="mailto:<?php echo esc_attr( antispambot( $markea_email ) ); ?>" data-track="contact" data-track-type="email"><?php echo esc_html( antispambot( $markea_email ) ); ?></a>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<div class="row site-footer__grid">
			<div class="col-12 col-md-6 col-lg-4 site-footer__brand">
				<?php
				$markea_logo = markea_logo( 'markea-logo' );
				echo $markea_logo ? $markea_logo : '<strong>' . esc_html( get_bloginfo( 'name' ) ) . '</strong>'; // phpcs:ignore WordPress.Security.EscapeOutput
				?>
				<?php if ( $markea_tagline ) : ?>
					<p class="site-footer__tagline"><?php echo esc_html( $markea_tagline ); ?></p>
				<?php endif; ?>
				<?php if ( $markea_text ) : ?>
					<p class="site-footer__text"><?php echo esc_html( $markea_text ); ?></p>
				<?php endif; ?>
				<?php if ( is_array( $markea_badges ) && $markea_badges ) : ?>
					<ul class="badges" role="list">
						<?php foreach ( $markea_badges as $markea_badge ) : ?>
							<li><?php echo markea_image( $markea_badge, 'markea-logo' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>

			<div class="col-12 col-md-6 col-lg-3">
				<address class="site-footer__contact">
					<?php if ( $markea_address ) : ?>
						<span class="site-footer__label"><?php esc_html_e( 'Dirección', 'markea-ia' ); ?></span>
						<span><?php echo esc_html( $markea_address ); ?></span>
					<?php endif; ?>
					<?php if ( $markea_phone ) : ?>
						<a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $markea_phone ) ); ?>" data-track="contact" data-track-type="phone"><?php echo esc_html( $markea_phone ); ?></a>
					<?php endif; ?>
				</address>
			</div>

			<?php if ( $markea_services ) : ?>
				<div class="col-6 col-lg-2">
					<p class="site-footer__label"><?php esc_html_e( 'Servicios', 'markea-ia' ); ?></p>
					<ul class="site-footer__list" role="list">
						<?php foreach ( $markea_services as $markea_service ) : ?>
							<li><?php echo esc_html( $markea_service ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<?php if ( $markea_social ) : ?>
				<div class="col-6 col-lg-3">
					<p class="site-footer__label"><?php esc_html_e( 'Social', 'markea-ia' ); ?></p>
					<ul class="site-footer__list" role="list">
						<?php foreach ( $markea_social as $markea_label => $markea_url ) : ?>
							<li><a href="<?php echo esc_url( $markea_url ); ?>" target="_blank" rel="noopener noreferrer" data-track="contact" data-track-type="social" data-track-network="<?php echo esc_attr( strtolower( $markea_label ) ); ?>"><?php echo esc_html( $markea_label ); ?></a></li>
						<?php endforeach; ?>
					</ul>
					<?php if ( $markea_news_url && $markea_news_title ) : ?>
						<p class="site-footer__label"><?php echo esc_html( $markea_news_title ); ?></p>
						<a class="btn btn--primary" href="<?php echo esc_url( $markea_news_url ); ?>"><?php echo esc_html( $markea_news_btn ?: __( 'Registrarme', 'markea-ia' ) ); ?></a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<?php if ( $markea_legal || $markea_copyright ) : ?>
		<div class="container site-footer__legal">
			<?php if ( $markea_copyright ) : ?>
				<p><?php echo esc_html( $markea_copyright ); ?></p>
			<?php endif; ?>
			<?php if ( $markea_legal ) : ?>
				<?php echo wp_kses_post( $markea_legal ); ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</footer>
<?php wp_footer(); ?>
</body>
</html>
