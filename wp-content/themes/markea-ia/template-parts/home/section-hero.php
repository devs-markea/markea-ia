<?php
/**
 * Section 1: Hero.
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
<section id="inicio" class="section hero<?php echo esc_attr( markea_tone( $row ) ); ?>">
	<div class="container hero__inner">
		<h1 class="hero__title"><?php echo markea_highlight( $title, (string) ( $row['highlight'] ?? '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h1>
		<?php if ( ( $row['subtitle'] ?? '' ) ) : ?>
			<p class="hero__lead"><?php echo esc_html( ( $row['subtitle'] ?? '' ) ); ?></p>
		<?php endif; ?>
		<?php echo markea_cta( 'hero' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>

	</div>
</section>
