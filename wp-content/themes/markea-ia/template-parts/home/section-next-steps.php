<?php
/**
 * Section 12: Proximos pasos (3 pasos + video).
 *
 * @package Markea_IA
 */

defined( 'ABSPATH' ) || exit;

$row = $args['row'] ?? array();

$title = (string) ( $row['title'] ?? '' );
$steps = ( $row['items'] ?? array() );
$video = (string) ( $row['video_url'] ?? '' );
if ( '' === $title && ! $steps ) {
	return;
}
$embed = $video ? wp_oembed_get( $video, array( 'width' => 960 ) ) : false;
?>
<section id="proximos-pasos" class="section next<?php echo esc_attr( markea_tone( $row ) ); ?>">
	<div class="container">
		<div class="row next__row">
		<div class="col-12 col-lg-6">
			<?php if ( $title ) : ?>
				<h2 class="section__title"><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
			<ol class="steps" role="list">
				<?php foreach ( $steps as $i => $step ) : ?>
					<li class="steps__item">
						<span class="card__index" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<h3 class="card__title"><?php echo esc_html( $step['title'] ?? '' ); ?></h3>
						<p class="card__text"><?php echo esc_html( $step['text'] ?? '' ); ?></p>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
		<?php if ( $embed ) : ?>
			<div class="col-12 col-lg-6">
			<div class="video" data-video>
				<button type="button" class="video__play" data-video-play>
					<span class="screen-reader-text"><?php esc_html_e( 'Reproducir video', 'markea-ia' ); ?></span>
				</button>
				<template data-video-embed><?php echo $embed; // phpcs:ignore WordPress.Security.EscapeOutput -- oEmbed HTML. ?></template>
			</div>
			</div>
		<?php endif; ?>
		</div>
	</div>
</section>
