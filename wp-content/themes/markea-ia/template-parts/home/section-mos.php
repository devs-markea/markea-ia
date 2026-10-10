<?php
/**
 * Section 6: Marketing Operating System. Header (title + intro) and four pillar cards: the first is featured
 * (chart visual, index, title and text); the others show a number, a visual and an accent title.
 * Visuals are decorative SVGs in assets/images/mos, assigned by position.
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
$accents = array( 1 => 'indigo', 2 => 'purple', 3 => 'electric' );
$images  = MARKEA_IA_URI . '/assets/images/mos/';
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
			<ol class="mos__pillars" role="list">
				<?php foreach ( $cards as $i => $card ) : ?>
					<?php
					$number = str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT );
					$visual = $visuals[ $i ] ?? '';
					?>
					<?php if ( 0 === $i ) : ?>
						<li class="mos__card mos__card--featured">
							<?php if ( $visual ) : ?>
								<img class="mos__visual" src="<?php echo esc_url( $images . $visual . '.svg' ); ?>" width="420" height="300" alt="" loading="lazy" decoding="async">
							<?php endif; ?>
							<div class="mos__body">
								<p class="mos__index"><?php echo esc_html( $number ); ?>.</p>
								<h3 class="mos__title"><?php echo esc_html( $card['title'] ?? '' ); ?></h3>
								<?php if ( ! empty( $card['text'] ) ) : ?>
									<p class="mos__text"><?php echo esc_html( $card['text'] ); ?></p>
								<?php endif; ?>
							</div>
						</li>
					<?php else : ?>
						<li class="mos__card mos__card--<?php echo esc_attr( $accents[ $i ] ?? 'electric' ); ?>">
							<p class="mos__number" aria-hidden="true"><?php echo esc_html( $number ); ?>.</p>
							<?php if ( $visual ) : ?>
								<img class="mos__art" src="<?php echo esc_url( $images . $visual . '.svg' ); ?>" width="194" height="132" alt="" loading="lazy" decoding="async">
							<?php endif; ?>
							<div class="mos__footer">
								<span class="mos__rule" aria-hidden="true"></span>
								<h3 class="mos__card-title"><?php echo esc_html( $card['title'] ?? '' ); ?></h3>
							</div>
						</li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>
	</div>
</section>
