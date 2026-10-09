<?php
/**
 * Section 5: Caso de operacion propia.
 *
 * @package Markea_IA
 */

defined( 'ABSPATH' ) || exit;

$row = $args['row'] ?? array();

$title = (string) ( $row['title'] ?? '' );
if ( '' === $title ) {
	return;
}
?>
<section id="caso" class="section case<?php echo esc_attr( markea_tone( $row ) ); ?>">
	<div class="container container--narrow">
		<?php if ( ( $row['eyebrow'] ?? '' ) ) : ?>
			<p class="eyebrow"><?php echo esc_html( ( $row['eyebrow'] ?? '' ) ); ?></p>
		<?php endif; ?>
		<h2 class="section__title"><?php echo esc_html( $title ); ?></h2>
		<?php if ( ( $row['text'] ?? '' ) ) : ?>
			<div class="prose"><?php echo wp_kses_post( ( $row['text'] ?? '' ) ); ?></div>
		<?php endif; ?>
	</div>
</section>
