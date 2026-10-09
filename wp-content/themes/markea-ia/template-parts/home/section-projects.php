<?php
/**
 * Section 8: Proyectos con filtros (CPT proyecto + taxonomia categoria-proyecto).
 *
 * @package Markea_IA
 */

defined( 'ABSPATH' ) || exit;

$row = $args['row'] ?? array();

$projects = markea_get_projects();
if ( ! $projects->have_posts() ) {
	return;
}
$categories = markea_get_project_categories();
?>
<section id="proyectos" class="section projects<?php echo esc_attr( markea_tone( $row ) ); ?>" data-projects>
	<div class="container">
		<?php echo markea_eyebrow( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<?php if ( ( $row['title'] ?? '' ) ) : ?>
			<h2 class="section__title"><?php echo markea_title( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
		<?php endif; ?>
		<?php if ( ( $row['text'] ?? '' ) ) : ?>
			<p class="section__lead"><?php echo esc_html( ( $row['text'] ?? '' ) ); ?></p>
		<?php endif; ?>

		<?php if ( $categories ) : ?>
			<div class="filters" role="group" aria-label="<?php esc_attr_e( 'Filtrar proyectos', 'markea-ia' ); ?>">
				<button type="button" class="filters__btn is-active" data-filter="*" aria-pressed="true"><?php esc_html_e( 'Todos', 'markea-ia' ); ?></button>
				<?php foreach ( $categories as $category ) : ?>
					<button type="button" class="filters__btn" data-filter="<?php echo esc_attr( $category->slug ); ?>" aria-pressed="false"><?php echo esc_html( $category->name ); ?></button>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<div class="cards">
			<ul class="row projects__grid" role="list">
			<?php
			while ( $projects->have_posts() ) :
				$projects->the_post();
				$slugs = wp_list_pluck( (array) get_the_terms( get_the_ID(), 'categoria-proyecto' ), 'slug' );
				$tag   = (string) markea_field( 'project_tag', '', get_the_ID() );
				$url   = (string) markea_field( 'project_url', '', get_the_ID() );
				?>
				<li class="col-12 col-md-6 col-lg-4" data-categories="<?php echo esc_attr( implode( ' ', array_filter( $slugs ) ) ); ?>"><div class="card project">
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="project__media"><?php the_post_thumbnail( 'markea-card' ); ?></div>
					<?php endif; ?>
					<?php if ( $tag ) : ?>
						<p class="eyebrow"><?php echo esc_html( $tag ); ?></p>
					<?php endif; ?>
					<h3 class="card__title">
						<?php if ( $url ) : ?>
							<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer"><?php the_title(); ?></a>
						<?php else : ?>
							<?php the_title(); ?>
						<?php endif; ?>
					</h3>
					<?php if ( has_excerpt() ) : ?>
						<p class="card__text"><?php echo esc_html( get_the_excerpt() ); ?></p>
					<?php endif; ?>
				</div>
				</li>
			<?php endwhile; ?>
		</ul>
		</div>
	</div>
</section>
<?php wp_reset_postdata(); ?>
