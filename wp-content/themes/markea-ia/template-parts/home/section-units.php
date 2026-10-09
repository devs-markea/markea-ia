<?php
/**
 * Section 4: Unidades de negocio.
 *
 * @package Markea_IA
 */

defined( 'ABSPATH' ) || exit;

$row = $args['row'] ?? array();

$title = (string) ( $row['title'] ?? '' );
$items = ( $row['items'] ?? array() );
if ( '' === $title && ! $items ) {
	return;
}
?>
<section id="unidades" class="section units<?php echo esc_attr( markea_tone( $row ) ); ?>">
	<div class="container">
		<?php echo markea_eyebrow( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<?php if ( $title ) : ?>
			<h2 class="section__title"><?php echo markea_title( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
		<?php endif; ?>
		<?php echo markea_lead( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<ol class="units__list" role="list">
			<?php foreach ( $items as $i => $item ) : ?>
				<?php
				$logo = markea_image( $item['logo'] ?? 0, 'markea-logo' );
				$name = (string) ( $item['name'] ?? '' );
				$url  = (string) ( $item['url'] ?? '' );
				$body = ( $logo ? $logo . '<span class="screen-reader-text">' . esc_html( $name ) . '</span>' : '<span class="units__marker" aria-hidden="true">' . esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ) . '</span><span class="units__name">' . esc_html( $name ) . '</span>' );
				?>
				<li class="units__item">
					<?php if ( $url ) : ?>
						<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo $body; // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
					<?php else : ?>
						<?php echo $body; // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>
