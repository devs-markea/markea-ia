<?php
/**
 * Section 4: Unidades de negocio. Centered header; story with selectable units (tabs) and a stack of
 * tilted cards that shows the selected unit.
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

$markers = array( 'electric', 'purple', 'cyan', 'indigo' ); // Marker colors, in order (Figma: electric, purple, cyan).
?>
<section id="unidades" class="section units<?php echo esc_attr( markea_tone( $row ) ); ?>">
	<div class="container">
		<header class="section__header section__header--center">
			<?php echo markea_eyebrow( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php if ( $title ) : ?>
				<h2 class="section__title"><?php echo markea_title( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
			<?php endif; ?>
		</header>

		<div class="units__story" data-units>
			<div class="units__narrative">
				<?php if ( ! empty( $row['text'] ) ) : ?>
					<p class="units__lead"><?php echo esc_html( $row['text'] ); ?></p>
				<?php endif; ?>

				<?php if ( $items ) : ?>
					<div class="units__tabs" role="tablist" aria-label="<?php echo esc_attr( $title ?: __( 'Unidades de negocio', 'markea-ia' ) ); ?>">
						<?php foreach ( $items as $i => $item ) : ?>
							<button
								type="button"
								role="tab"
								id="unit-tab-<?php echo (int) $i; ?>"
								class="units__tab units__tab--<?php echo esc_attr( $markers[ $i % count( $markers ) ] ); ?>"
								aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>"
								aria-controls="unit-panel-<?php echo (int) $i; ?>"
								tabindex="<?php echo 0 === $i ? '0' : '-1'; ?>"
								data-color="<?php echo esc_attr( (string) ( $item['color'] ?? '' ) ); ?>"
							>
								<span class="units__marker" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
								<span class="units__name"><?php echo esc_html( $item['name'] ?? '' ); ?></span>
							</button>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( $items ) : ?>
				<?php $first_has_image = ! empty( $items[0]['logo'] ); ?>
				<div class="units__stage" data-plain="<?php echo $first_has_image ? '0' : '1'; ?>">
					<span class="units__plate units__plate--base" aria-hidden="true"></span>
					<span class="units__plate units__plate--tilted" aria-hidden="true"></span>
					<?php foreach ( $items as $i => $item ) : ?>
						<?php
						$name  = (string) ( $item['name'] ?? '' );
						$url   = (string) ( $item['url'] ?? '' );
						$image = markea_image( $item['logo'] ?? 0, 'large', array( 'class' => 'units__image', 'sizes' => '(min-width: 992px) 420px, 80vw' ) );
						$inner = $image ?: '<span class="units__fallback">' . esc_html( $name ) . '</span>';
						$bg    = sanitize_hex_color( (string) ( $item['color'] ?? '' ) );
						$style = $bg ? sprintf( '--card-bg:%s;--card-fg:%s', $bg, markea_text_on( $bg ) ) : '';
						?>
						<figure
							class="units__card"
							role="tabpanel"
							id="unit-panel-<?php echo (int) $i; ?>"
							aria-labelledby="unit-tab-<?php echo (int) $i; ?>"
							data-image="<?php echo $image ? '1' : '0'; ?>"
							<?php echo $style ? 'style="' . esc_attr( $style ) . '"' : ''; ?>
							<?php echo 0 === $i ? '' : 'hidden'; ?>
						>
							<?php if ( $url ) : ?>
								<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $name ); ?>"><?php echo $inner; // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
							<?php else : ?>
								<?php echo $inner; // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<?php endif; ?>
						</figure>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
