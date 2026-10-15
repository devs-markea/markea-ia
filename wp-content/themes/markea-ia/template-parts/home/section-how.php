<?php
/**
 * Section 11: Como trabajamos. Centered header (badge, title, gradient rule, subtitle) and three service cards.
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
<section id="como-trabajamos" class="section how<?php echo esc_attr( markea_tone( $row ) ); ?>">
	<?php echo markea_section_anchor( $row ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in the helper. ?>
	<div class="container">
		<header class="how__header">
			<?php echo markea_eyebrow( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php if ( $title ) : ?>
				<h2 class="section__title"><?php echo markea_title( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
			<?php endif; ?>
			<span class="how__rule" aria-hidden="true"></span>
			<?php if ( ! empty( $row['text'] ) ) : ?>
				<p class="how__subtitle"><?php echo esc_html( $row['text'] ); ?></p>
			<?php endif; ?>
		</header>

		<?php if ( $items ) : ?>
			<div class="m-swiper" <?php echo markea_carousel_attrs(); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
				<ul class="how__grid" role="list">
					<?php foreach ( $items as $item ) : ?>
						<li class="how__card">
							<?php if ( ! empty( $item['icon'] ) ) : ?>
								<span class="how__icon"><?php echo markea_icon( (string) $item['icon'], 28 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
							<?php endif; ?>
							<div class="how__body">
								<h3 class="how__title"><?php echo esc_html( $item['title'] ?? '' ); ?></h3>
								<?php if ( ! empty( $item['text'] ) ) : ?>
									<p class="how__text"><?php echo esc_html( $item['text'] ); ?></p>
								<?php endif; ?>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>
	</div>
</section>
