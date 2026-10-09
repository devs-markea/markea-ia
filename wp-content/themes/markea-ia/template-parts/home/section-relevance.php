<?php
/**
 * Section 10: Relevancia sectorial.
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
?>
<section id="relevancia" class="section relevance<?php echo esc_attr( markea_tone( $row ) ); ?>">
	<div class="container container--narrow">
		<?php echo markea_eyebrow( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<?php if ( $title ) : ?>
			<h2 class="section__title"><?php echo markea_title( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
		<?php endif; ?>
		<?php echo markea_lead( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<?php if ( $points ) : ?>
			<ul class="checklist" role="list">
				<?php foreach ( $points as $point ) : ?>
					<li>
						<strong><?php echo esc_html( $point['title'] ?? '' ); ?></strong>
						<?php if ( ! empty( $point['text'] ) ) : ?>
							<span class="checklist__text"><?php echo esc_html( $point['text'] ); ?></span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>
