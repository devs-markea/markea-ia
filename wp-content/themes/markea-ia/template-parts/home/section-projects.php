<?php
/**
 * Section 8: Proyectos (CPT proyecto + taxonomia categoria-proyecto). Header, category pills (filter) and a
 * two-column grid of cards: image, category and title.
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
		<header class="projects__header">
			<div class="projects__heading">
				<?php echo markea_eyebrow( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php if ( ! empty( $row['title'] ) ) : ?>
					<h2 class="section__title"><?php echo markea_title( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
				<?php endif; ?>
			</div>
			<?php if ( ! empty( $row['text'] ) ) : ?>
				<p class="projects__intro"><?php echo esc_html( $row['text'] ); ?></p>
			<?php endif; ?>
		</header>

		<?php if ( $categories ) : ?>
			<div class="filters" role="group" aria-label="<?php esc_attr_e( 'Filtrar proyectos', 'markea-ia' ); ?>">
				<button type="button" class="filters__btn is-active" data-filter="*" aria-pressed="true"><?php esc_html_e( 'Todos', 'markea-ia' ); ?></button>
				<?php foreach ( $categories as $category ) : ?>
					<button type="button" class="filters__btn" data-filter="<?php echo esc_attr( $category->slug ); ?>" aria-pressed="false"><?php echo esc_html( $category->name ); ?></button>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<ul class="row projects__grid" role="list">
			<?php
			while ( $projects->have_posts() ) :
				$projects->the_post();
				$terms = (array) get_the_terms( get_the_ID(), 'categoria-proyecto' );
				$slugs = wp_list_pluck( array_filter( $terms, static fn( $t ) => $t instanceof WP_Term ), 'slug' );
				$label = (string) markea_field( 'project_tag', '', get_the_ID() );
				if ( '' === $label && ! empty( $terms[0] ) && $terms[0] instanceof WP_Term ) {
					$label = $terms[0]->name;
				}
				$url = (string) markea_field( 'project_url', '', get_the_ID() );
				?>
				<li class="col-12 col-md-6" data-categories="<?php echo esc_attr( implode( ' ', $slugs ) ); ?>">
					<article class="projects__card">
						<div class="projects__media">
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'large', array( 'class' => 'projects__image', 'sizes' => '(min-width: 768px) 588px, 92vw' ) ); ?>
							<?php endif; ?>
						</div>
						<div class="projects__meta">
							<?php if ( $label ) : ?>
								<p class="projects__category"><?php echo esc_html( $label ); ?></p>
							<?php endif; ?>
							<h3 class="projects__title">
								<?php if ( $url ) : ?>
									<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer"><?php the_title(); ?></a>
								<?php else : ?>
									<?php the_title(); ?>
								<?php endif; ?>
							</h3>
						</div>
					</article>
				</li>
			<?php endwhile; ?>
		</ul>
	</div>
</section>
<?php wp_reset_postdata(); ?>
