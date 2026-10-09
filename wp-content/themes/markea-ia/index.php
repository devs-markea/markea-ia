<?php
/**
 * Fallback template. The landing lives in templates/landing.php.
 *
 * @package Markea_IA
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="contenido" class="site-main">
	<div class="container prose">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article <?php post_class(); ?>>
				<h1><?php the_title(); ?></h1>
				<?php the_content(); ?>
			</article>
		<?php endwhile; ?>
	</div>
</main>
<?php
get_footer();
