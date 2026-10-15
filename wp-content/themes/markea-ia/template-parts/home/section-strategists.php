<?php
/**
 * Section 13: Estrategas (CPT estratega). Centered header, then four portrait cards that straddle a navy band.
 * The name plate slides up on hover or focus (the first card starts open).
 *
 * @package Markea_IA
 */

defined( 'ABSPATH' ) || exit;

$row = $args['row'] ?? array();

$people = markea_get_strategists();
if ( ! $people->have_posts() ) {
	return;
}
?>
<section id="estrategas" class="section strategists<?php echo esc_attr( markea_tone( $row ) ); ?>">
	<?php echo markea_section_anchor( $row ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in the helper. ?>
	<div class="container">
		<header class="strategists__header">
			<?php echo markea_eyebrow( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php if ( ! empty( $row['title'] ) ) : ?>
				<h2 class="section__title"><?php echo markea_title( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
			<?php endif; ?>
			<span class="strategists__rule" aria-hidden="true"></span>
			<?php if ( ! empty( $row['text'] ) ) : ?>
				<p class="strategists__text"><?php echo esc_html( $row['text'] ); ?></p>
			<?php endif; ?>
		</header>
	</div>

	<div class="container strategists__team">
		<div class="m-swiper" <?php echo markea_carousel_attrs(); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
			<ul class="strategists__list" role="list">
				<?php
				$i = 0;
				while ( $people->have_posts() ) :
					$people->the_post();
					$linkedin = (string) markea_field( 'linkedin', '', get_the_ID() );
					$role     = (string) markea_field( 'role', '', get_the_ID() );
					?>
					<li class="strategists__card<?php echo 0 === $i ? ' is-first' : ''; ?>" tabindex="0">
						<?php if ( has_post_thumbnail() ) : ?>
							<?php the_post_thumbnail( 'large', array( 'class' => 'strategists__photo', 'sizes' => '(min-width: 992px) 290px, (min-width: 576px) 46vw, 92vw' ) ); ?>
						<?php endif; ?>
						<div class="strategists__plate">
							<h3 class="strategists__name"><?php the_title(); ?></h3>
							<?php if ( $role ) : ?>
								<p class="strategists__role"><?php echo esc_html( $role ); ?></p>
							<?php endif; ?>
							<?php if ( $linkedin ) : ?>
								<a class="strategists__link" href="<?php echo esc_url( $linkedin ); ?>" target="_blank" rel="noopener noreferrer" data-track="contact" data-track-type="social" data-track-network="linkedin">LinkedIn<span class="screen-reader-text">: <?php the_title(); ?></span></a>
							<?php endif; ?>
						</div>
					</li>
					<?php ++$i; ?>
				<?php endwhile; ?>
			</ul>
		</div>
	</div>
</section>
<?php wp_reset_postdata(); ?>
