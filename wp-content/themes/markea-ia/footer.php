<?php
/**
 * Site footer (navy). Everything comes from Site Settings.
 * Top: question and email on the left, tagline and partner pills on the right. Then four columns
 * (address, services, social, newsletter), the big wordmark and the copyright line.
 *
 * @package Markea_IA
 */

defined( 'ABSPATH' ) || exit;

$markea_email      = (string) markea_option( 'contact_email' );
$markea_phone      = (string) markea_option( 'contact_phone' );
$markea_address    = (string) markea_option( 'contact_address' );
$markea_legal      = (string) markea_option( 'brand_legal_text' );
$markea_badges     = markea_lines( (string) markea_option( 'footer_badges' ) );
$markea_social     = markea_social_links();
$markea_cta_title  = (string) markea_option( 'footer_cta_title' );
$markea_cta_bold   = (string) markea_option( 'footer_cta_emphasis' );
$markea_tagline    = (string) markea_option( 'footer_tagline' );
$markea_text       = (string) markea_option( 'footer_text' );
$markea_services   = markea_lines( (string) markea_option( 'footer_services' ) );
$markea_copyright  = (string) markea_option( 'footer_copyright' );
$markea_news_title = (string) markea_option( 'newsletter_title' );
$markea_news_btn   = (string) markea_option( 'newsletter_button' );
$markea_news_url   = (string) markea_option( 'newsletter_action' );
$markea_icons      = array(
	'LinkedIn'  => 'linkedin-solid',
	'Instagram' => 'instagram-solid',
	'Facebook'  => 'facebook-solid',
);
?>
<footer class="site-footer">
	<div class="container site-footer__top">
		<div class="site-footer__ask">
			<?php if ( $markea_cta_title ) : ?>
				<p class="site-footer__question"><?php echo markea_title( array( 'title' => $markea_cta_title, 'emphasis' => $markea_cta_bold ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
			<?php endif; ?>
			<?php if ( $markea_email ) : ?>
				<a class="site-footer__email" href="mailto:<?php echo esc_attr( antispambot( $markea_email ) ); ?>" data-track="contact" data-track-type="email">
					<?php echo markea_icon( 'send', 26 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php echo esc_html( antispambot( $markea_email ) ); ?>
				</a>
			<?php endif; ?>
		</div>

		<div class="site-footer__pitch">
			<?php if ( $markea_tagline ) : ?>
				<p class="site-footer__tagline"><?php echo esc_html( $markea_tagline ); ?></p>
			<?php endif; ?>
			<?php if ( $markea_text ) : ?>
				<p class="site-footer__text"><?php echo esc_html( $markea_text ); ?></p>
			<?php endif; ?>
			<?php if ( $markea_badges ) : ?>
				<ul class="site-footer__badges" role="list">
					<?php foreach ( $markea_badges as $markea_badge ) : ?>
						<li><?php echo esc_html( $markea_badge ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>

	<div class="site-footer__rule" aria-hidden="true"></div>

	<div class="container site-footer__columns">
		<div class="site-footer__col">
			<p class="site-footer__label"><?php esc_html_e( 'Dirección', 'markea-ia' ); ?></p>
			<address class="site-footer__address">
				<?php if ( $markea_address ) : ?>
					<span><?php echo esc_html( $markea_address ); ?></span>
				<?php endif; ?>
				<?php if ( $markea_phone ) : ?>
					<a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $markea_phone ) ); ?>" data-track="contact" data-track-type="phone"><?php echo esc_html( $markea_phone ); ?></a>
				<?php endif; ?>
			</address>
		</div>

		<?php if ( $markea_services ) : ?>
			<div class="site-footer__col">
				<p class="site-footer__label"><?php esc_html_e( 'Servicios', 'markea-ia' ); ?></p>
				<ul class="site-footer__list" role="list">
					<?php foreach ( $markea_services as $markea_service ) : ?>
						<li><?php echo esc_html( $markea_service ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<?php if ( $markea_social ) : ?>
			<div class="site-footer__col">
				<p class="site-footer__label"><?php esc_html_e( 'Social', 'markea-ia' ); ?></p>
				<ul class="site-footer__list" role="list">
					<?php foreach ( $markea_social as $markea_label => $markea_url ) : ?>
						<li>
							<a class="site-footer__social" href="<?php echo esc_url( $markea_url ); ?>" target="_blank" rel="noopener noreferrer" data-track="contact" data-track-type="social" data-track-network="<?php echo esc_attr( strtolower( $markea_label ) ); ?>">
								<?php echo markea_icon( $markea_icons[ $markea_label ] ?? 'arrow-up-right', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								<?php echo esc_html( $markea_label ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<?php if ( $markea_news_url && $markea_news_title ) : ?>
			<div class="site-footer__col site-footer__col--news">
				<p class="site-footer__label"><?php echo esc_html( $markea_news_title ); ?></p>
				<a class="btn btn--primary site-footer__news-btn" href="<?php echo esc_url( $markea_news_url ); ?>"><?php echo esc_html( $markea_news_btn ?: __( 'Registrarme', 'markea-ia' ) ); ?></a>
			</div>
		<?php endif; ?>
	</div>

	<div class="site-footer__rule" aria-hidden="true"></div>

	<div class="container site-footer__brand">
		<img src="<?php echo esc_url( MARKEA_IA_URI . '/assets/images/markea-wordmark.svg' ); ?>" width="942" height="128" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" loading="lazy" decoding="async">
	</div>

	<?php if ( $markea_copyright || $markea_legal ) : ?>
		<div class="site-footer__legal">
			<?php if ( $markea_copyright ) : ?>
				<p><?php echo esc_html( $markea_copyright ); ?></p>
			<?php endif; ?>
			<?php if ( $markea_legal ) : ?>
				<div><?php echo wp_kses_post( $markea_legal ); ?></div>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</footer>
<?php wp_footer(); ?>
</body>
</html>
