<?php
/**
 * Section 9: Herramientas (galeria de logos).
 *
 * @package Markea_IA
 */

defined( 'ABSPATH' ) || exit;

$row = $args['row'] ?? array();

$gallery = ( $row['gallery'] ?? array() );
if ( ! is_array( $gallery ) || ! $gallery ) {
	return;
}
?>
<section id="herramientas" class="section tools<?php echo esc_attr( markea_tone( $row ) ); ?>">
	<div class="container">
		<?php if ( ( $row['title'] ?? '' ) ) : ?>
			<h2 class="section__title"><?php echo esc_html( ( $row['title'] ?? '' ) ); ?></h2>
		<?php endif; ?>
		<ul class="tools__grid" role="list">
			<?php foreach ( $gallery as $image ) : ?>
				<li><?php echo markea_image( $image, 'markea-logo' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
