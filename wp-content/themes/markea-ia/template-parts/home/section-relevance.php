<?php
/**
 * Section 10: Relevancia sectorial. Header (title + text) and a card with an accordion (first point open)
 * on the left and a tilted photo on the right.
 *
 * @package Markea_IA
 */

defined( 'ABSPATH' ) || exit;

$row = $args['row'] ?? array();

$title  = (string) ( $row['title'] ?? '' );
$points = ( $row['items'] ?? array() );
if ( '' === $title && ! $points ) {
	return;
}

$photo = markea_image( $row['image'] ?? 0, 'large', array( 'class' => 'relevance__photo-img', 'sizes' => '(min-width: 992px) 360px, 80vw' ) );
if ( ! $photo ) {
	$photo = sprintf( '<img class="relevance__photo-img" src="%s" width="1000" height="667" alt="" loading="lazy" decoding="async">', esc_url( MARKEA_IA_URI . '/assets/images/relevance-photo.jpg' ) );
}
?>
<section id="relevancia" class="section relevance<?php echo esc_attr( markea_tone( $row ) ); ?>">
	<div class="container">
		<header class="relevance__header">
			<div class="relevance__heading">
				<?php echo markea_eyebrow( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php if ( $title ) : ?>
					<h2 class="section__title"><?php echo markea_title( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
				<?php endif; ?>
			</div>
			<?php if ( ! empty( $row['text'] ) ) : ?>
				<p class="relevance__intro"><?php echo esc_html( $row['text'] ); ?></p>
			<?php endif; ?>
		</header>

		<div class="relevance__card">
			<?php if ( $points ) : ?>
				<div class="relevance__list" data-accordion>
					<?php foreach ( $points as $i => $point ) : ?>
						<?php
						$has_text = ! empty( $point['text'] );
						$open     = 0 === $i && $has_text;
						?>
						<div class="relevance__item<?php echo $open ? ' is-open' : ''; ?>" data-accordion-item>
							<h3 class="relevance__heading-item">
								<?php if ( $has_text ) : ?>
									<button type="button" class="relevance__trigger" aria-expanded="<?php echo $open ? 'true' : 'false'; ?>" aria-controls="relevance-panel-<?php echo (int) $i; ?>" id="relevance-trigger-<?php echo (int) $i; ?>" data-accordion-trigger>
										<?php if ( ! empty( $point['icon'] ) ) : ?>
											<span class="relevance__icon"><?php echo markea_icon( (string) $point['icon'], 20 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
										<?php endif; ?>
										<span class="relevance__label"><?php echo esc_html( $point['title'] ?? '' ); ?></span>
									</button>
								<?php else : ?>
									<span class="relevance__trigger relevance__trigger--static">
										<?php if ( ! empty( $point['icon'] ) ) : ?>
											<span class="relevance__icon"><?php echo markea_icon( (string) $point['icon'], 20 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
										<?php endif; ?>
										<span class="relevance__label"><?php echo esc_html( $point['title'] ?? '' ); ?></span>
									</span>
								<?php endif; ?>
							</h3>
							<?php if ( $has_text ) : ?>
								<div class="relevance__panel" id="relevance-panel-<?php echo (int) $i; ?>" role="region" aria-labelledby="relevance-trigger-<?php echo (int) $i; ?>" <?php echo $open ? '' : 'hidden'; ?>>
									<div class="relevance__panel-inner"><p><?php echo esc_html( $point['text'] ); ?></p></div>
								</div>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<figure class="relevance__photo"><?php echo $photo; // phpcs:ignore WordPress.Security.EscapeOutput ?></figure>
		</div>
	</div>
</section>
