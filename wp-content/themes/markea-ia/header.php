<?php
/**
 * Site header.
 *
 * @package Markea_IA
 */

defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#contenido"><?php esc_html_e( 'Saltar al contenido', 'markea-ia' ); ?></a>

<header class="site-header" data-header>
	<div class="container site-header__inner">
		<a class="site-header__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
			<?php
			$markea_logo = markea_logo( 'markea-logo', array( 'loading' => 'eager' ) );
			echo $markea_logo ? $markea_logo : esc_html( get_bloginfo( 'name' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
			?>
		</a>

		<button class="site-header__toggle" type="button" aria-expanded="false" aria-controls="menu-principal" data-nav-toggle>
			<span class="screen-reader-text"><?php esc_html_e( 'Menú', 'markea-ia' ); ?></span>
			<span aria-hidden="true" class="site-header__burger">

            </span>
		</button>

		<div class="site-header__actions">
			<nav id="menu-principal" class="site-nav" aria-label="<?php esc_attr_e( 'Principal', 'markea-ia' ); ?>" data-nav>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'site-nav__list',
						'fallback_cb'    => false,
						'depth'          => 1,
					)
				);
				echo markea_cta( 'header', 'btn btn--primary btn--small' ); // phpcs:ignore WordPress.Security.EscapeOutput
				?>
			</nav>

			<button class="site-header__menu" type="button" aria-label="<?php esc_attr_e( 'Menú', 'markea-ia' ); ?>">
				<svg width="34" height="34" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
					<g clip-path="url(#clip0_177_44)">
						<line x1="-3" y1="13.5" x2="27" y2="13.5" stroke="currentColor"/>
						<line x1="5" y1="23.5" x2="35" y2="23.5" stroke="currentColor"/>
					</g>
					<defs>
						<clipPath id="clip0_177_44">
							<rect width="34" height="34" fill="white"/>
						</clipPath>
					</defs>
				</svg>
			</button>
		</div>
	</div>
</header>
