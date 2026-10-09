<?php
/**
 * Section 14: Formulario. Submitted by REST to markea-core (POST markea/v1/leads).
 *
 * @package Markea_IA
 */

defined( 'ABSPATH' ) || exit;

$title = (string) markea_field( 'form_title' );
if ( '' === $title ) {
	return;
}

$interests = markea_lines( (string) markea_field( 'form_interests' ) );
$consent   = (string) markea_option( 'form_consent_text' );
$success   = (string) markea_option( 'form_success_message' );
?>
<section id="contacto" class="section contact">
	<div class="container container--narrow">
		<h2 class="section__title"><?php echo esc_html( $title ); ?></h2>
		<?php if ( markea_field( 'form_text' ) ) : ?>
			<p class="section__lead"><?php echo esc_html( markea_field( 'form_text' ) ); ?></p>
		<?php endif; ?>

		<form class="form" method="post" novalidate data-lead-form data-endpoint="<?php echo esc_url( rest_url( 'markea/v1/leads' ) ); ?>" data-success="<?php echo esc_attr( $success ); ?>">
			<div class="form__row">
				<label for="lead-name"><?php esc_html_e( 'Nombre', 'markea-ia' ); ?></label>
				<input id="lead-name" name="name" type="text" autocomplete="name" required>
			</div>
			<div class="form__row">
				<label for="lead-email"><?php esc_html_e( 'Correo', 'markea-ia' ); ?></label>
				<input id="lead-email" name="email" type="email" autocomplete="email" required>
			</div>
			<div class="form__row">
				<label for="lead-company"><?php esc_html_e( 'Empresa', 'markea-ia' ); ?></label>
				<input id="lead-company" name="company" type="text" autocomplete="organization">
			</div>
			<div class="form__row">
				<label for="lead-phone"><?php esc_html_e( 'Teléfono', 'markea-ia' ); ?></label>
				<input id="lead-phone" name="phone" type="tel" autocomplete="tel">
			</div>
			<?php if ( $interests ) : ?>
				<div class="form__row">
					<label for="lead-interest"><?php esc_html_e( 'Me interesa', 'markea-ia' ); ?></label>
					<select id="lead-interest" name="interest">
						<?php foreach ( $interests as $interest ) : ?>
							<option value="<?php echo esc_attr( $interest ); ?>"><?php echo esc_html( $interest ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			<?php endif; ?>
			<div class="form__row">
				<label for="lead-message"><?php esc_html_e( 'Mensaje', 'markea-ia' ); ?></label>
				<textarea id="lead-message" name="message" rows="4"></textarea>
			</div>

			<div class="form__hp" aria-hidden="true">
				<label for="lead-website">Website</label>
				<input id="lead-website" name="website" type="text" tabindex="-1" autocomplete="off">
			</div>

			<?php if ( $consent ) : ?>
				<div class="form__row form__row--check">
					<input id="lead-consent" name="consent" type="checkbox" value="1" required>
					<label for="lead-consent"><?php echo wp_kses_post( $consent ); ?></label>
				</div>
			<?php endif; ?>

			<button class="btn btn--primary" type="submit"><?php esc_html_e( 'Enviar', 'markea-ia' ); ?></button>
			<p class="form__status" role="status" aria-live="polite" data-form-status></p>
		</form>
	</div>
</section>
