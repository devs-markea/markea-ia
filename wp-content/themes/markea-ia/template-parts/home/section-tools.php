<?php
/**
 * Section 9: Herramientas (logos).
 *
 * @package Markea_IA
 */

defined( 'ABSPATH' ) || exit;

$row = $args['row'] ?? array();

$title = (string) ( $row['title'] ?? '' );
$items = ( $row['items'] ?? array() );
if ( '' === $title && ! $items ) {
	return;
}
?>
<section id="herramientas" class="section tools<?php echo esc_attr( markea_tone( $row ) ); ?>">
	<div class="container">
		<?php echo markea_eyebrow( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<?php if ( $title ) : ?>
			<h2 class="section__title"><?php echo esc_html( $title ); ?></h2>
		<?php endif; ?>
		<?php if ( $items ) : ?>
			<ul class="tools__grid" role="list">
				<?php foreach ( $items as $item ) : ?>
					<?php $logo = markea_image( $item['logo'] ?? 0, 'markea-logo' ); ?>
					<li>
						<?php if ( $logo ) : ?>
							<?php echo $logo; // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<span class="screen-reader-text"><?php echo esc_html( $item['name'] ?? '' ); ?></span>
						<?php else : ?>
							<?php echo esc_html( $item['name'] ?? '' ); ?>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>
