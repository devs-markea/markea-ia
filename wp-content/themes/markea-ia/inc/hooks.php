<?php
/**
 * Theme hooks: cleanup and extension points. Data and tracking scripts live in markea-core.
 *
 * @package Markea_IA
 */

defined( 'ABSPATH' ) || exit;

// Front cleanup (no emoji script, no legacy head links).
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );

add_filter( 'body_class', 'markea_ia_body_class' );
add_action( 'wp_enqueue_scripts', 'markea_ia_dequeue_unused', 100 );

function markea_ia_body_class( array $classes ): array {
	$classes[] = 'markea-ia';

	return $classes;
}

/**
 * The landing does not use classic block styles beyond global styles.
 */
function markea_ia_dequeue_unused(): void {
	wp_dequeue_style( 'classic-theme-styles' );
	wp_dequeue_style( 'wp-block-library' );
}

/**
 * Extension point right after <body> (markea-core prints GTM noscript here).
 */
add_action( 'wp_body_open', static function (): void {
	do_action( 'markea_after_body_open' );
} );

/**
 * Fallback landing template for the front page when no landing template is assigned.
 */
add_filter( 'template_include', 'markea_ia_front_page_template' );

function markea_ia_front_page_template( string $template ): string {
	if ( is_front_page() && is_page() && basename( $template ) !== 'landing.php' && 'page' === get_option( 'show_on_front' ) ) {
		$landing = MARKEA_IA_DIR . '/templates/landing.php';
		if ( is_readable( $landing ) && ! get_page_template_slug() ) {
			return $landing;
		}
	}

	return $template;
}

/**
 * Favicon from Site Settings (falls back to the native Site Icon).
 */
add_filter( 'get_site_icon_url', 'markea_ia_favicon_url', 10, 3 );

function markea_ia_favicon_url( string $url, int $size, int $blog_id ): string {
	$id = markea_option( 'brand_favicon' );
	$id = is_array( $id ) ? (int) ( $id['ID'] ?? 0 ) : (int) $id;
	if ( ! $id ) {
		return $url;
	}

	$src = wp_get_attachment_image_url( $id, array( $size ?: 512, $size ?: 512 ) );

	return $src ? $src : $url;
}

/**
 * Palette from Site Settings > Colores, printed after the theme stylesheet so it wins.
 * Only valid hex values are emitted; anything else falls back to the CSS defaults.
 */
add_action( 'wp_head', 'markea_ia_print_palette', 100 );

function markea_ia_print_palette(): void {
	$map = array();
	foreach ( array( 'navy', 'electric', 'white', 'cyan', 'purple', 'indigo', 'dark' ) as $slug ) {
		$map[ 'color_' . $slug ] = array( '--' . $slug, '--wp--preset--color--' . $slug );
	}

	$css = '';
	foreach ( $map as $option => $vars ) {
		$hex = sanitize_hex_color( (string) markea_option( $option ) );
		if ( $hex ) {
			foreach ( $vars as $var ) {
				$css .= $var . ':' . $hex . ';';
			}
		}
	}

	if ( $css ) {
		echo '<style id="markea-palette">:root{' . $css . '}</style>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput -- values are validated hex colors.
	}
}

/**
 * Hide the admin bar on the front end for everyone, so the landing is seen as visitors see it.
 * Re-enable with: add_filter( 'markea_show_admin_bar', '__return_true' );
 */
add_filter( 'show_admin_bar', 'markea_ia_show_admin_bar' );

function markea_ia_show_admin_bar( bool $show ): bool {
	return is_admin() ? $show : (bool) apply_filters( 'markea_show_admin_bar', false );
}

/**
 * Motion flags, set before first paint so content that will be animated is never seen un-animated.
 * - has-motion: JS-driven effects are allowed (not reduced motion, not switched off). Removed after 6 s
 *   if the motion script never reports ready, so content can never stay hidden.
 * - has-loader: the entry loader is on screen (once per session when configured).
 */
add_action( 'wp_head', 'markea_ia_print_motion_flags', 1 );

function markea_ia_print_motion_flags(): void {
	if ( ! markea_motion_enabled() ) {
		return;
	}

	$loader = markea_loader_enabled();
	$once   = $loader && (bool) markea_option( 'loader_once', 1 );
	?>
<script>
(function(d,w){var r=d.documentElement;if(w.matchMedia&&w.matchMedia('(prefers-reduced-motion: reduce)').matches){return;}
r.classList.add('has-motion');
<?php if ( $loader ) : ?>
var seen=false;<?php echo $once ? "try{seen=w.sessionStorage.getItem('markea-loader')==='1';}catch(e){}" : ''; ?>

if(!seen){r.classList.add('has-loader');}
<?php endif; ?>
setTimeout(function(){if(r.getAttribute('data-motion')!=='ready'){r.classList.remove('has-motion','has-loader');}},6000);
})(document,window);
</script>
	<?php
}

/**
 * Entry loader markup (the front page only). Hidden by CSS unless html.has-loader is set.
 */
add_action( 'wp_body_open', 'markea_ia_print_loader', 5 );

function markea_ia_print_loader(): void {
	if ( ! markea_loader_enabled() ) {
		return;
	}

	$text = (string) markea_option( 'loader_text', __( 'Cargando', 'markea-ia' ) );
	?>
<div class="loader" data-loader aria-hidden="true">
	<img class="loader__logo" src="<?php echo esc_url( MARKEA_IA_URI . '/assets/images/markea-logo.svg' ); ?>" width="190" height="26" alt="">
	<div class="loader__bottom">
		<div class="loader__meta">
			<span class="loader__text"><?php echo esc_html( $text ); ?></span>
			<span class="loader__count" data-loader-count>0%</span>
		</div>
		<div class="loader__bar"><span class="loader__fill" data-loader-fill></span></div>
	</div>
</div>
	<?php
}
