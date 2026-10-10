<?php
/**
 * Section 2: El problema. Centered header, three elevated cards with icons, centered CTA.
 *
 * @package Markea_IA
 */

defined( 'ABSPATH' ) || exit;

$row = $args['row'] ?? array();

$title = (string) ( $row['title'] ?? '' );
$cards = ( $row['items'] ?? array() );
if ( '' === $title && ! $cards ) {
	return;
}
?>
<section id="problema" class="section problem<?php echo esc_attr( markea_tone( $row ) ); ?>">
	<div class="container">
		<header class="section__header section__header--center">
			<?php echo markea_eyebrow( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php if ( $title ) : ?>
				<h2 class="section__title"><?php echo markea_title( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
			<?php endif; ?>
			<?php echo markea_lead( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</header>

		<?php if ( $cards ) : ?>
			<ul class="row problem__cards" role="list">
				<?php foreach ( $cards as $card ) : ?>
					<li class="col-12 col-md-6 col-lg-4">
						<article class="card card--elevated">
							<?php if ( ! empty( $card['icon'] ) ) : ?>
								<span class="card__icon"><?php echo markea_icon( (string) $card['icon'], 20 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
							<?php endif; ?>
							<?php if ( ! empty( $card['title'] ) ) : ?>
								<h3 class="card__title"><?php echo esc_html( $card['title'] ); ?></h3>
							<?php endif; ?>
							<?php if ( ! empty( $card['text'] ) ) : ?>
								<p class="card__text"><?php echo esc_html( $card['text'] ); ?></p>
							<?php endif; ?>
						</article>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<p class="section__cta section__cta--center"><?php echo markea_cta( 'problem' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
	</div>
</section>
