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
			<span aria-hidden="true" class="site-header__burger"></span>
		</button>

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
	</div>
</header>
