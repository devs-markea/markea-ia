<?php
/**
 * Section 6: Marketing Operating System. Header (title + intro) and four pillar cards that work as an accordion
 * on desktop: one card is active (tall, gradient stage with its illustration, badge, index, title and text), the
 * others are compact (number, illustration, title). The first card is active by default; clicking another one hands
 * the active state over (assets/src/js/motion/extras.js). Below 992 px every card shows its open face.
 * Illustrations are decorative SVGs in assets/images/mos, inlined so their parts can animate, assigned by position.
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

$visuals = array( 'dashboard', 'stack', 'checklist', 'roadmap' );
$accents = array( 'electric', 'indigo', 'purple', 'electric' );
?>
<section id="mos" class="section mos<?php echo esc_attr( markea_tone( $row ) ); ?>">
	<div class="container">
		<header class="mos__header">
			<?php if ( $title ) : ?>
				<h2 class="section__title"><?php echo markea_title( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
			<?php endif; ?>
			<?php if ( ! empty( $row['text'] ) ) : ?>
				<p class="mos__intro"><?php echo esc_html( $row['text'] ); ?></p>
			<?php endif; ?>
		</header>

		<?php if ( $cards ) : ?>
			<ol class="mos__pillars" role="list" data-active="0">
				<?php foreach ( $cards as $i => $card ) : ?>
					<?php
					$number = str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT );
					$visual = $visuals[ $i ] ?? '';
					$title  = (string) ( $card['title'] ?? '' );
					?>
					<li class="mos__card mos__card--<?php echo esc_attr( $accents[ $i ] ?? 'electric' ); ?><?php echo 0 === $i ? ' is-active' : ''; ?>">
						<div class="mos__face mos__face--open">
							<?php if ( $visual ) : ?>
								<div class="mos__stage<?php echo 0 === $i ? ' mos__stage--chart' : ''; ?>">
									<span class="mos__badge" aria-hidden="true">
										<span class="mos__dot"></span>
										<span class="mos__badge-line"></span>
									</span>
									<?php echo markea_inline_svg( 'mos/' . $visual, 'mos__art' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								</div>
							<?php endif; ?>
							<div class="mos__body">
								<p class="mos__index"><?php echo esc_html( $number ); ?>.</p>
								<h3 class="mos__title"><?php echo esc_html( $title ); ?></h3>
								<?php if ( ! empty( $card['text'] ) ) : ?>
									<p class="mos__text"><?php echo esc_html( $card['text'] ); ?></p>
								<?php endif; ?>
							</div>
						</div>
						<div class="mos__face mos__face--closed" aria-hidden="true">
							<p class="mos__number"><?php echo esc_html( $number ); ?>.</p>
							<?php if ( $visual ) : ?>
								<?php echo markea_inline_svg( 'mos/' . $visual, 'mos__art' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<?php endif; ?>
							<div class="mos__footer">
								<span class="mos__rule"></span>
								<p class="mos__card-title"><?php echo esc_html( $title ); ?></p>
							</div>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>
	</div>
</section>
