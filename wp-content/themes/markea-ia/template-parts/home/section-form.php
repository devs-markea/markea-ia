<?php
/**
 * Section 14: Formulario. A large card: left = eyebrow, title, rule, text, social icons and email;
 * right = form with underlined fields, interest chips (radio buttons) and the submit button.
 * Submitted by REST to markea-core (POST markea/v1/leads).
 *
 * @package Markea_IA
 */

defined( 'ABSPATH' ) || exit;

$row = $args['row'] ?? array();

$title = (string) ( $row['title'] ?? '' );
if ( '' === $title ) {
	return;
}

$interests = markea_lines( (string) ( $row['interests'] ?? '' ) );
$consent   = (string) markea_option( 'form_consent_text' );
$success   = (string) markea_option( 'form_success_message' );
$email     = (string) markea_option( 'contact_email' );
$social    = markea_social_links( array( 'Instagram', 'Facebook', 'LinkedIn' ) );
$icons     = array(
	'Instagram' => 'instagram',
	'Facebook'  => 'facebook',
	'LinkedIn'  => 'linkedin',
);
$heading   = (string) ( markea_section( 'hero' )['cta_label'] ?? '' ) ?: __( 'Hablemos', 'markea-ia' );
?>
<section id="contacto" class="section contact<?php echo esc_attr( markea_tone( $row ) ); ?>">
	<?php echo markea_section_anchor( $row ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in the helper. ?>
	<div class="contact__card">
		<div class="contact__intro">
			<?php if ( ! empty( $row['eyebrow'] ) ) : ?>
				<p class="contact__eyebrow"><?php echo esc_html( $row['eyebrow'] ); ?></p>
			<?php endif; ?>
			<h2 class="section__title"><?php echo markea_title( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
			<span class="contact__rule" aria-hidden="true"></span>
			<?php if ( ! empty( $row['text'] ) ) : ?>
				<p class="contact__text"><?php echo esc_html( $row['text'] ); ?></p>
			<?php endif; ?>
			<?php if ( $social ) : ?>
				<ul class="contact__social" role="list">
					<?php foreach ( $social as $label => $url ) : ?>
						<li>
							<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $label ); ?>" data-track="contact" data-track-type="social" data-track-network="<?php echo esc_attr( strtolower( $label ) ); ?>"><?php echo markea_icon( $icons[ $label ] ?? 'arrow-up-right', 36 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<?php if ( $email ) : ?>
				<a class="contact__email" href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>" data-track="contact" data-track-type="email"><?php echo markea_icon( 'mail', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( antispambot( $email ) ); ?></a>
			<?php endif; ?>
		</div>

		<form class="contact__form" method="post" novalidate data-lead-form data-endpoint="<?php echo esc_url( rest_url( 'markea/v1/leads' ) ); ?>" data-success="<?php echo esc_attr( $success ); ?>">
			<h3 class="contact__heading"><?php echo esc_html( $heading ); ?></h3>

			<div class="contact__grid">
				<div class="field">
					<label for="lead-name"><?php esc_html_e( 'Nombre completo', 'markea-ia' ); ?></label>
					<input id="lead-name" name="name" type="text" autocomplete="name" required>
				</div>
				<div class="field">
					<label for="lead-company"><?php esc_html_e( 'Empresa', 'markea-ia' ); ?></label>
					<input id="lead-company" name="company" type="text" autocomplete="organization">
				</div>
				<div class="field">
					<label for="lead-email"><?php esc_html_e( 'Email', 'markea-ia' ); ?></label>
					<input id="lead-email" name="email" type="email" autocomplete="email" required>
				</div>
				<div class="field">
					<label for="lead-phone"><?php esc_html_e( 'Teléfono', 'markea-ia' ); ?></label>
					<input id="lead-phone" name="phone" type="tel" autocomplete="tel">
				</div>
			</div>

			<?php if ( $interests ) : ?>
				<fieldset class="chips">
					<legend><?php esc_html_e( 'Me interesa', 'markea-ia' ); ?></legend>
					<div class="chips__list">
						<?php foreach ( $interests as $i => $interest ) : ?>
							<span class="chips__item">
								<input type="radio" name="interest" id="lead-interest-<?php echo (int) $i; ?>" value="<?php echo esc_attr( $interest ); ?>"<?php echo 0 === $i ? ' checked' : ''; ?>>
								<label for="lead-interest-<?php echo (int) $i; ?>"><?php echo esc_html( $interest ); ?></label>
							</span>
						<?php endforeach; ?>
					</div>
				</fieldset>
			<?php endif; ?>

			<div class="field field--area">
				<label for="lead-message"><?php esc_html_e( 'Cuéntanos sobre tu proyecto', 'markea-ia' ); ?></label>
				<textarea id="lead-message" name="message" rows="3"></textarea>
			</div>

			<div class="contact__hp" aria-hidden="true">
				<label for="lead-website">Website</label>
				<input id="lead-website" name="website" type="text" tabindex="-1" autocomplete="off">
			</div>

			<?php if ( $consent ) : ?>
				<div class="contact__consent">
					<input id="lead-consent" name="consent" type="checkbox" value="1" required>
					<label for="lead-consent"><?php echo wp_kses_post( $consent ); ?></label>
				</div>
			<?php endif; ?>

			<button class="btn btn--primary contact__submit" type="submit"><span><?php esc_html_e( 'ENVIAR', 'markea-ia' ); ?></span><?php echo markea_button_arrow(); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
			<p class="contact__status" role="status" aria-live="polite" data-form-status></p>
		</form>
	</div>
</section>
