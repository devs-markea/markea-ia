<?php
/**
 * Section 5: Caso de operacion propia. Always on the dark (navy) tone, like the design.
 * Left: label, title and photo card with a logo overlay. Right: pill, title, paragraph and bullets.
 *
 * @package Markea_IA
 */

defined( 'ABSPATH' ) || exit;

$row = $args['row'] ?? array();

$title = (string) ( $row['title'] ?? '' );
if ( '' === $title ) {
	return;
}

$image = markea_image( $row['image'] ?? 0, 'large', array( 'class' => 'case__image', 'sizes' => '(min-width: 992px) 480px, 92vw' ) );
if ( ! $image ) {
	$image = sprintf( '<img class="case__image" src="%s" width="1000" height="530" alt="" loading="lazy" decoding="async">', esc_url( MARKEA_IA_URI . '/assets/images/case-photo.jpg' ) );
}

$overlay = markea_image( $row['image_overlay'] ?? 0, 'medium', array( 'class' => 'case__overlay-img' ) );
if ( ! $overlay && empty( $row['image'] ) ) {
	$overlay = sprintf( '<img class="case__overlay-img" src="%s" width="200" height="110" alt="" loading="lazy" decoding="async">', esc_url( MARKEA_IA_URI . '/assets/images/case-logo.png' ) );
}

$bullets = ( $row['items'] ?? array() );
?>
<section id="caso" class="section case section--dark">
	<?php echo markea_section_anchor( $row ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in the helper. ?>
	<span class="case__orb" aria-hidden="true"></span>
	<div class="container">
		<div class="case__grid">
			<div class="case__left">
				<?php if ( ! empty( $row['eyebrow'] ) || ! empty( $row['subtitle'] ) ) : ?>
					<header class="case__heading">
						<?php if ( ! empty( $row['eyebrow'] ) ) : ?>
							<p class="case__label"><?php echo esc_html( $row['eyebrow'] ); ?></p>
						<?php endif; ?>
						<?php if ( ! empty( $row['subtitle'] ) ) : ?>
							<p class="case__subtitle"><?php echo esc_html( $row['subtitle'] ); ?></p>
						<?php endif; ?>
					</header>
				<?php endif; ?>
				<figure class="case__card">
					<?php echo $image; // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php if ( $overlay ) : ?>
						<span class="case__overlay"><?php echo $overlay; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<?php endif; ?>
				</figure>
			</div>

			<div class="case__right">
				<header class="case__intro">
					<?php if ( ! empty( $row['kicker'] ) ) : ?>
						<p class="case__pill"><?php echo esc_html( $row['kicker'] ); ?></p>
					<?php endif; ?>
					<h2 class="section__title"><?php echo markea_title( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
					<?php if ( ! empty( $row['text'] ) ) : ?>
						<p class="case__text"><?php echo esc_html( $row['text'] ); ?></p>
					<?php endif; ?>
				</header>
				<?php if ( $bullets ) : ?>
					<ul class="case__bullets" role="list">
						<?php foreach ( $bullets as $bullet ) : ?>
							<li><?php echo esc_html( is_array( $bullet ) ? ( $bullet['text'] ?? '' ) : $bullet ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
