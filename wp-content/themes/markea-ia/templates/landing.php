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
	// Sections are ACF Flexible Content layouts; layout name => template-parts/home/section-<name>.php.
	$markea_allowed = array( 'hero', 'problem', 'about', 'units', 'case', 'mos', 'offer', 'projects', 'tools', 'relevance', 'how', 'next_steps', 'strategists', 'form' );

	foreach ( markea_sections() as $markea_row ) {
		$markea_layout = (string) ( $markea_row['acf_fc_layout'] ?? '' );
		if ( in_array( $markea_layout, $markea_allowed, true ) ) {
			get_template_part( 'template-parts/home/section', str_replace( '_', '-', $markea_layout ), array( 'row' => $markea_row ) );
		}
	}
	?>
</main>
<?php
get_footer();
