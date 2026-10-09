<?php
/**
 * Template Name: Landing Markea IA
 *
 * One partial per section in template-parts/home/. Empty sections render nothing.
 *
 * @package Markea_IA
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="contenido" class="site-main">
	<?php
	while ( have_posts() ) :
		the_post();

		$markea_sections = array(
			'hero',
			'problem',
			'about',
			'units',
			'case',
			'mos',
			'offer',
			'projects',
			'tools',
			'relevance',
			'how',
			'next-steps',
			'strategists',
			'form',
		);

		foreach ( $markea_sections as $markea_section ) {
			get_template_part( 'template-parts/home/section', $markea_section );
		}
	endwhile;
	?>
</main>
<?php
get_footer();
