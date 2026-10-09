<?php
/**
 * Section 3: Quienes somos.
 *
 * @package Markea_IA
 */

defined( 'ABSPATH' ) || exit;

$title  = (string) markea_field( 'about_title' );
$years  = (string) markea_field( 'about_years' );
$blocks = markea_rows( 'about_blocks' );
if ( '' === $title && ! $blocks ) {
	return;
}
?>
<section id="quienes-somos" class="section about">
	<div class="container about__grid">
		<div>
			<?php if ( $years ) : ?>
				<p class="about__years"><?php echo esc_html( $years ); ?></p>
			<?php endif; ?>
			<?php if ( $title ) : ?>
				<h2 class="section__title"><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
			<?php if ( markea_field( 'about_text' ) ) : ?>
				<div class="prose"><?php echo wp_kses_post( markea_field( 'about_text' ) ); ?></div>
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
