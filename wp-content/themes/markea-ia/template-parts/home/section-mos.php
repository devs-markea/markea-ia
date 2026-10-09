<?php
/**
 * Cards section: mos.
 *
 * @package Markea_IA
 */

defined( 'ABSPATH' ) || exit;

$row = $args['row'] ?? array();

$title = (string) ( $row['title'] ?? '' );
$rows  = ( $row['items'] ?? array() );
if ( '' === $title && ! $rows ) {
	return;
}
?>
<section id="mos" class="section mos<?php echo esc_attr( markea_tone( $row ) ); ?>">
	<div class="container">
		<?php echo markea_eyebrow( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<?php if ( $title ) : ?>
			<h2 class="section__title"><?php echo markea_title( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
		<?php endif; ?>
		<?php echo markea_lead( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<?php if ( $rows ) : ?>
			<div class="cards">
				<ol class="row" role="list">
				<?php foreach ( $rows as $i => $card ) : ?>
					<li class="col-12 col-md-6 col-lg-3"><div class="card">
						<span class="card__index" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<?php if ( ! empty( $card['title'] ) ) : ?>
							<h3 class="card__title"><?php echo esc_html( $card['title'] ); ?></h3>
						<?php endif; ?>
						<?php if ( ! empty( $card['text'] ) ) : ?>
							<p class="card__text"><?php echo esc_html( $card['text'] ); ?></p>
						<?php endif; ?>
					</div>
				</li>
				<?php endforeach; ?>
			</ol>
		</div>
		<?php endif; ?>
	</div>
</section>
