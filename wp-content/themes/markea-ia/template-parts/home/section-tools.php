<?php
/**
 * Section 9: Herramientas (galeria de logos).
 *
 * @package Markea_IA
 */

defined( 'ABSPATH' ) || exit;

$gallery = markea_field( 'tools_gallery', array() );
if ( ! is_array( $gallery ) || ! $gallery ) {
	return;
}
?>
<section id="herramientas" class="section tools">
	<div class="container">
		<?php if ( markea_field( 'tools_title' ) ) : ?>
			<h2 class="section__title"><?php echo esc_html( markea_field( 'tools_title' ) ); ?></h2>
		<?php endif; ?>
		<ul class="tools__grid" role="list">
			<?php foreach ( $gallery as $image ) : ?>
				<li><?php echo markea_image( $image, 'markea-logo' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
