<?php
/**
 * Section 5: Caso de operacion propia.
 *
 * @package Markea_IA
 */

defined( 'ABSPATH' ) || exit;

$title = (string) markea_field( 'case_title' );
if ( '' === $title ) {
	return;
}
?>
<section id="caso" class="section case">
	<div class="container container--narrow">
		<?php if ( markea_field( 'case_eyebrow' ) ) : ?>
			<p class="eyebrow"><?php echo esc_html( markea_field( 'case_eyebrow' ) ); ?></p>
		<?php endif; ?>
		<h2 class="section__title"><?php echo esc_html( $title ); ?></h2>
		<?php if ( markea_field( 'case_text' ) ) : ?>
			<div class="prose"><?php echo wp_kses_post( markea_field( 'case_text' ) ); ?></div>
		<?php endif; ?>
	</div>
</section>
