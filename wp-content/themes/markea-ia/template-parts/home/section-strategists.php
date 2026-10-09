<?php
/**
 * Section 13: Estrategas (CPT estratega).
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
	<div class="container">
		<?php echo markea_eyebrow( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<?php if ( ( $row['title'] ?? '' ) ) : ?>
			<h2 class="section__title"><?php echo markea_title( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
		<?php endif; ?>
		<?php if ( ( $row['text'] ?? '' ) ) : ?>
			<p class="section__lead"><?php echo esc_html( ( $row['text'] ?? '' ) ); ?></p>
		<?php endif; ?>
		<div class="cards">
			<ul class="row" role="list">
			<?php
			while ( $people->have_posts() ) :
				$people->the_post();
				$linkedin = (string) markea_field( 'linkedin', '', get_the_ID() );
				?>
				<li class="col-12 col-md-6 col-lg-3"><div class="card person">
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="person__photo"><?php the_post_thumbnail( 'markea-portrait' ); ?></div>
					<?php endif; ?>
					<h3 class="card__title"><?php the_title(); ?></h3>
					<p class="card__text"><?php echo esc_html( markea_field( 'role', '', get_the_ID() ) ); ?></p>
					<?php if ( $linkedin ) : ?>
						<a class="person__link" href="<?php echo esc_url( $linkedin ); ?>" target="_blank" rel="noopener noreferrer" data-track="contact" data-track-type="social" data-track-network="linkedin">
							LinkedIn<span class="screen-reader-text">: <?php the_title(); ?></span>
						</a>
					<?php endif; ?>
				</div>
				</li>
			<?php endwhile; ?>
		</ul>
		</div>
	</div>
</section>
<?php wp_reset_postdata(); ?>
