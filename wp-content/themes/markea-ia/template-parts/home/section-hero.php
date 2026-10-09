<?php
/**
 * Section 1: Hero.
 *
 * @package Markea_IA
 */

defined( 'ABSPATH' ) || exit;

$title = (string) markea_field( 'hero_title' );
if ( '' === $title ) {
	return;
}
?>
<section id="inicio" class="section hero">
	<div class="container hero__inner">
		<h1 class="hero__title"><?php echo markea_highlight( $title, (string) markea_field( 'hero_highlight' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h1>
		<?php if ( markea_field( 'hero_subtitle' ) ) : ?>
			<p class="hero__lead"><?php echo esc_html( markea_field( 'hero_subtitle' ) ); ?></p>
		<?php endif; ?>
		<?php echo markea_cta( 'hero' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</div>
</section>
