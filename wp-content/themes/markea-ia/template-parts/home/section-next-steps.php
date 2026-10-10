<?php
/**
 * Section 12: Proximos pasos. Always on the navy tone. Left: badge, title, text, CTA and the video card.
 * Middle: a dashed timeline with three icon discs. Right: three step cards.
 *
 * @package Markea_IA
 */

defined( 'ABSPATH' ) || exit;

$row = $args['row'] ?? array();

$title = (string) ( $row['title'] ?? '' );
$steps = ( $row['items'] ?? array() );
if ( '' === $title && ! $steps ) {
	return;
}

$video_url = (string) ( $row['video_url'] ?? '' );
$embed     = $video_url ? wp_oembed_get( $video_url, array( 'width' => 960 ) ) : false;
$poster    = markea_image( $row['video_image'] ?? 0, 'large', array( 'class' => 'next__poster', 'sizes' => '(min-width: 992px) 620px, 92vw' ) );
if ( ! $poster ) {
	$poster = sprintf( '<img class="next__poster" src="%s" width="1100" height="619" alt="" loading="lazy" decoding="async">', esc_url( MARKEA_IA_URI . '/assets/images/next-video.jpg' ) );
}
$label    = (string) ( $row['video_label'] ?? '' ) ?: __( 'Reproducir', 'markea-ia' );
$duration = (string) ( $row['video_duration'] ?? '' );

$timeline = array( 'search-18', 'bar-chart', 'file-text' ); // Icons of the three timeline discs, by position.
?>
<section id="proximos-pasos" class="section next section--dark">
	<div class="container">
		<div class="next__layout">
			<div class="next__main">
				<?php echo markea_eyebrow( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php if ( $title ) : ?>
					<h2 class="section__title"><?php echo markea_title( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
				<?php endif; ?>
				<?php if ( ! empty( $row['text'] ) ) : ?>
					<p class="next__text"><?php echo esc_html( $row['text'] ); ?></p>
				<?php endif; ?>
				<p class="next__cta"><?php echo markea_cta( 'next-steps' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>

				<div class="next__video" data-video>
					<?php echo $poster; // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php if ( $embed ) : ?>
						<button type="button" class="next__play" data-video-play>
							<span class="next__play-disc"><?php echo markea_icon( 'play', 12 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
							<span class="next__play-text">
								<span class="next__play-label"><?php echo esc_html( $label ); ?></span>
								<?php if ( $duration ) : ?>
									<span class="next__play-duration"><?php echo esc_html( $duration ); ?></span>
								<?php endif; ?>
							</span>
						</button>
						<template data-video-embed><?php echo $embed; // phpcs:ignore WordPress.Security.EscapeOutput -- oEmbed HTML. ?></template>
					<?php endif; ?>
				</div>
			</div>

			<div class="next__timeline" aria-hidden="true">
				<?php foreach ( $timeline as $i => $icon ) : ?>
					<?php if ( $i > 0 ) : ?>
						<span class="next__line"></span>
					<?php endif; ?>
					<span class="next__disc<?php echo 0 === $i ? ' next__disc--filled' : ''; ?>"><?php echo markea_icon( $icon, 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<?php endforeach; ?>
			</div>

			<?php if ( $steps ) : ?>
				<ol class="next__steps" role="list">
					<?php foreach ( $steps as $i => $step ) : ?>
						<li class="next__step">
							<span class="next__number" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
							<div class="next__step-body">
								<h3 class="next__step-title"><?php echo esc_html( $step['title'] ?? '' ); ?></h3>
								<?php if ( ! empty( $step['text'] ) ) : ?>
									<p class="next__step-text"><?php echo esc_html( $step['text'] ); ?></p>
								<?php endif; ?>
							</div>
						</li>
					<?php endforeach; ?>
				</ol>
			<?php endif; ?>
		</div>
	</div>
</section>
