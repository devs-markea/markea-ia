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

		<div class="site-header__actions">
			<?php echo markea_cta( 'header', 'btn btn--link' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<span class="site-header__divider" aria-hidden="true"></span>
			<button
				class="site-header__menu"
				type="button"
				aria-expanded="false"
				aria-controls="site-menu"
				data-menu-toggle
				data-label-open="<?php esc_attr_e( 'Abrir menú', 'markea-ia' ); ?>"
				data-label-close="<?php esc_attr_e( 'Cerrar menú', 'markea-ia' ); ?>"
			>
				<span class="screen-reader-text" data-menu-label><?php esc_html_e( 'Abrir menú', 'markea-ia' ); ?></span>
				<?php echo markea_icon( 'menu', 34 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php echo markea_icon( 'close', 38 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</button>
		</div>
	</div>
</header>

<?php get_template_part( 'template-parts/menu' ); ?>
