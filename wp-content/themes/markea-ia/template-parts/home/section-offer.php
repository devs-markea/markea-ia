<?php
/**
 * Section 7: Oferta diferenciada. Title (SemiBold fragment) and intro side by side, then three cards with
 * an icon, title, text and tags. Each card has a soft colored glow in its top-right corner.
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

$tones = array( 'electric', 'purple', 'indigo' ); // Icon, icon background and glow color per card position.
?>
<section id="oferta" class="section offer<?php echo esc_attr( markea_tone( $row ) ); ?>">
	<div class="container">
		<header class="offer__header">
			<?php if ( $title ) : ?>
				<h2 class="section__title"><?php echo markea_title( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
			<?php endif; ?>
			<?php if ( ! empty( $row['text'] ) ) : ?>
				<p class="offer__intro"><?php echo esc_html( $row['text'] ); ?></p>
			<?php endif; ?>
		</header>

		<?php if ( $items ) : ?>
			<ul class="offer__cards" role="list">
				<?php foreach ( $items as $i => $item ) : ?>
					<?php $tags = markea_tags( (string) ( $item['tags'] ?? '' ) ); ?>
					<li class="offer__card offer__card--<?php echo esc_attr( $tones[ $i % count( $tones ) ] ); ?>">
						<?php if ( ! empty( $item['icon'] ) ) : ?>
							<span class="offer__icon"><?php echo markea_icon( (string) $item['icon'], 24 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<?php endif; ?>
						<div class="offer__content">
							<h3 class="offer__title"><?php echo esc_html( $item['title'] ?? '' ); ?></h3>
							<?php if ( ! empty( $item['text'] ) ) : ?>
								<p class="offer__text"><?php echo esc_html( $item['text'] ); ?></p>
							<?php endif; ?>
						</div>
						<?php if ( $tags ) : ?>
							<ul class="offer__tags" role="list">
								<?php foreach ( $tags as $tag ) : ?>
									<li><?php echo esc_html( $tag ); ?></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>
