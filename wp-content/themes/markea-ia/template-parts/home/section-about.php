<?php
/**
 * Section 3: Quienes somos. Two panels: brand narrative (white) and operating model (navy, with a tilted photo).
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

$photo = markea_image( $row['panel_image'] ?? 0, 'large', array( 'class' => 'about__photo-img', 'sizes' => '(min-width: 992px) 600px, 90vw' ) );
if ( ! $photo ) {
	$photo = sprintf( '<img class="about__photo-img" src="%s" width="1100" height="733" alt="" loading="lazy" decoding="async">', esc_url( MARKEA_IA_URI . '/assets/images/about-panel.jpg' ) );
}
?>
<section id="quienes-somos" class="section about<?php echo esc_attr( markea_tone( $row ) ); ?>">
	<?php echo markea_section_anchor( $row ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in the helper. ?>
	<div class="container">
		<header class="section__header">
			<?php echo markea_eyebrow( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<h2 class="section__title about__title">
				<?php if ( $years ) : ?>
					<strong class="u-strong"><?php echo esc_html( $years ); ?></strong>
				<?php endif; ?>
				<?php echo esc_html( $title ); ?>
			</h2>
		</header>

		<div class="row about__panels">
			<div class="col-12 col-lg-6">
				<article class="about__narrative">
					<img class="about__logo" src="<?php echo esc_url( MARKEA_IA_URI . '/assets/images/markea-logo-navy.svg' ); ?>" width="193" height="26" alt="" loading="lazy">
					<div class="about__narrative-body">
						<?php if ( ! empty( $row['text'] ) ) : ?>
							<div class="about__text"><?php echo wp_kses_post( $row['text'] ); ?></div>
						<?php endif; ?>
						<p class="about__cta"><?php echo markea_cta( 'about' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
					</div>
				</article>
			</div>

			<div class="col-12 col-lg-6">
				<article class="about__model">
					<?php if ( ! empty( $row['panel_text'] ) ) : ?>
						<p class="about__model-text"><?php echo esc_html( $row['panel_text'] ); ?></p>
					<?php endif; ?>
					<?php if ( $blocks ) : ?>
						<ol class="about__steps" role="list">
							<?php foreach ( $blocks as $i => $block ) : ?>
								<li class="about__step">
									<span class="about__marker" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
									<span class="about__step-title"><?php echo esc_html( $block['title'] ?? '' ); ?></span>
								</li>
							<?php endforeach; ?>
						</ol>
					<?php endif; ?>
					<figure class="about__photo"><?php echo $photo; // phpcs:ignore WordPress.Security.EscapeOutput ?></figure>
				</article>
			</div>
		</div>
	</div>
</section>
