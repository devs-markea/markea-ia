<?php
/**
 * Section 7: Oferta diferenciada.
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
<section id="oferta" class="section offer<?php echo esc_attr( markea_tone( $row ) ); ?>">
	<div class="container">
		<?php echo markea_eyebrow( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<?php if ( $title ) : ?>
			<h2 class="section__title"><?php echo esc_html( $title ); ?></h2>
		<?php endif; ?>
		<?php echo markea_lead( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<div class="cards">
			<ul class="row" role="list">
			<?php foreach ( $items as $item ) : ?>
				<li class="col-12 col-md-6 col-lg-4"><div class="card">
					<h3 class="card__title"><?php echo esc_html( $item['title'] ?? '' ); ?></h3>
					<p class="card__text"><?php echo esc_html( $item['text'] ?? '' ); ?></p>
					<?php $tags = markea_tags( (string) ( $item['tags'] ?? '' ) ); ?>
					<?php if ( $tags ) : ?>
						<ul class="tags" role="list">
							<?php foreach ( $tags as $tag ) : ?>
								<li class="tag"><?php echo esc_html( $tag ); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
				</li>
			<?php endforeach; ?>
		</ul>
		</div>
	</div>
</section>
