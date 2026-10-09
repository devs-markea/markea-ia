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
		<?php echo markea_eyebrow( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<?php if ( ! empty( $row['subtitle'] ) ) : ?>
			<p class="case__subtitle"><?php echo esc_html( $row['subtitle'] ); ?></p>
		<?php endif; ?>
		<?php if ( ! empty( $row['kicker'] ) ) : ?>
			<p class="eyebrow case__kicker"><?php echo esc_html( $row['kicker'] ); ?></p>
		<?php endif; ?>
		<h2 class="section__title"><?php echo markea_title( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
		<?php if ( ! empty( $row['text'] ) ) : ?>
			<div class="prose"><?php echo wp_kses_post( $row['text'] ); ?></div>
		<?php endif; ?>
	</div>
</section>
