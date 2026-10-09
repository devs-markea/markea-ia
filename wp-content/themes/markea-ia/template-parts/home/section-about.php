<?php
/**
 * Section 3: Quienes somos.
 *
 * @package Markea_IA
 */

defined( 'ABSPATH' ) || exit;

$row = $args['row'] ?? array();

$title  = (string) ( $row['title'] ?? '' );
$years  = (string) ( $row['years'] ?? '' );
$blocks = ( $row['items'] ?? array() );
if ( '' === $title && ! $blocks ) {
	return;
}
?>
<section id="quienes-somos" class="section about<?php echo esc_attr( markea_tone( $row ) ); ?>">
	<div class="container about__grid">
		<div>
			<?php if ( $years ) : ?>
				<p class="about__years"><?php echo esc_html( $years ); ?></p>
			<?php endif; ?>
			<?php if ( $title ) : ?>
				<h2 class="section__title"><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
			<?php if ( ( $row['text'] ?? '' ) ) : ?>
				<div class="prose"><?php echo wp_kses_post( ( $row['text'] ?? '' ) ); ?></div>
			<?php endif; ?>
		</div>
		<?php if ( $blocks ) : ?>
			<ul class="about__blocks" role="list">
				<?php foreach ( $blocks as $block ) : ?>
					<li class="card">
						<h3 class="card__title"><?php echo esc_html( $block['title'] ?? '' ); ?></h3>
						<p class="card__text"><?php echo esc_html( $block['text'] ?? '' ); ?></p>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>
