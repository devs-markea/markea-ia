<?php
/**
 * Section 4: Unidades de negocio.
 *
 * @package Markea_IA
 */

defined( 'ABSPATH' ) || exit;

$title = (string) markea_field( 'units_title' );
$items = markea_rows( 'units_items' );
if ( ! $items ) {
	return;
}
?>
<section id="unidades" class="section units">
	<div class="container">
		<?php if ( $title ) : ?>
			<h2 class="section__title"><?php echo esc_html( $title ); ?></h2>
		<?php endif; ?>
		<ul class="units__list" role="list">
			<?php foreach ( $items as $item ) : ?>
				<?php
				$logo = markea_image( $item['logo'] ?? 0, 'markea-logo' );
				$name = (string) ( $item['name'] ?? '' );
				$url  = (string) ( $item['url'] ?? '' );
				$body = $logo ? $logo . '<span class="screen-reader-text">' . esc_html( $name ) . '</span>' : esc_html( $name );
				?>
				<li class="units__item">
					<?php if ( $url ) : ?>
						<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo $body; // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
					<?php else : ?>
						<?php echo $body; // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
