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
	<div class="container">
		<?php echo markea_eyebrow( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<h2 class="section__title about__title">
			<?php if ( $years ) : ?>
				<span class="about__years"><?php echo esc_html( $years ); ?></span>
			<?php endif; ?>
			<?php echo esc_html( $title ); ?>
		</h2>
		<div class="row about__panels">
			<div class="col-12 col-lg-6">
				<div class="card about__narrative">
					<?php if ( ! empty( $row['text'] ) ) : ?>
						<div class="prose"><?php echo wp_kses_post( $row['text'] ); ?></div>
					<?php endif; ?>
					<p><?php echo markea_cta( 'about' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
				</div>
			</div>
			<div class="col-12 col-lg-6">
				<div class="card about__model">
					<?php if ( ! empty( $row['panel_text'] ) ) : ?>
						<p class="card__text"><?php echo esc_html( $row['panel_text'] ); ?></p>
					<?php endif; ?>
					<?php if ( $blocks ) : ?>
						<ol class="about__blocks" role="list">
							<?php foreach ( $blocks as $i => $block ) : ?>
								<li class="about__block">
									<span class="about__marker" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
									<span class="about__block-title"><?php echo esc_html( $block['title'] ?? '' ); ?></span>
								</li>
							<?php endforeach; ?>
						</ol>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>
