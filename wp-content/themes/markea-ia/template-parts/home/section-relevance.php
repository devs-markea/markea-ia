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
<section id="relevancia" class="section relevance">
	<div class="container container--narrow">
		<?php if ( $title ) : ?>
			<h2 class="section__title"><?php echo esc_html( $title ); ?></h2>
		<?php endif; ?>
		<?php if ( ( $row['text'] ?? '' ) ) : ?>
			<p class="section__lead"><?php echo esc_html( ( $row['text'] ?? '' ) ); ?></p>
		<?php endif; ?>
		<?php if ( $points ) : ?>
			<ul class="checklist" role="list">
				<?php foreach ( $points as $point ) : ?>
					<li><?php echo esc_html( is_array( $point ) ? ( $point['text'] ?? '' ) : $point ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>
