<?php
/**
 * Site footer. All values come from Site Settings.
 *
 * @package Markea_IA
 */

defined( 'ABSPATH' ) || exit;

$markea_email   = (string) markea_option( 'contact_email' );
$markea_phone   = (string) markea_option( 'contact_phone' );
$markea_address = (string) markea_option( 'contact_address' );
$markea_legal   = (string) markea_option( 'brand_legal_text' );
$markea_badges  = markea_option( 'brand_partner_badges', array() );
$markea_social  = markea_social_links();
?>
<footer class="site-footer">
	<div class="container site-footer__grid">
		<div class="site-footer__brand">
			<?php
			$markea_logo = markea_logo( 'markea-logo' );
			echo $markea_logo ? $markea_logo : '<strong>' . esc_html( get_bloginfo( 'name' ) ) . '</strong>'; // phpcs:ignore WordPress.Security.EscapeOutput
			?>
			<?php if ( is_array( $markea_badges ) && $markea_badges ) : ?>
				<ul class="badges" role="list">
					<?php foreach ( $markea_badges as $markea_badge ) : ?>
						<li><?php echo markea_image( $markea_badge, 'markea-logo' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<address class="site-footer__contact">
			<?php if ( $markea_email ) : ?>
				<a href="mailto:<?php echo esc_attr( antispambot( $markea_email ) ); ?>" data-track="contact" data-track-type="email"><?php echo esc_html( antispambot( $markea_email ) ); ?></a>
			<?php endif; ?>
			<?php if ( $markea_phone ) : ?>
				<a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $markea_phone ) ); ?>" data-track="contact" data-track-type="phone"><?php echo esc_html( $markea_phone ); ?></a>
			<?php endif; ?>
			<?php if ( $markea_address ) : ?>
				<span><?php echo esc_html( $markea_address ); ?></span>
			<?php endif; ?>
		</address>

		<?php if ( $markea_social ) : ?>
			<ul class="site-footer__social" role="list">
				<?php foreach ( $markea_social as $markea_label => $markea_url ) : ?>
					<li><a href="<?php echo esc_url( $markea_url ); ?>" target="_blank" rel="noopener noreferrer" data-track="contact" data-track-type="social" data-track-network="<?php echo esc_attr( strtolower( $markea_label ) ); ?>"><?php echo esc_html( $markea_label ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>

	<?php if ( $markea_legal ) : ?>
		<div class="container site-footer__legal"><?php echo wp_kses_post( $markea_legal ); ?></div>
	<?php endif; ?>
</footer>
<?php wp_footer(); ?>
</body>
</html>
