<?php
/**
 * Section 1: Hero. Text on the left, interactive visual (iframe URL from the editor) in a circle on the right.
 *
 * @package Markea_IA
 */

defined( 'ABSPATH' ) || exit;

$row = $args['row'] ?? array();

$title = (string) ( $row['title'] ?? '' );
if ( '' === $title ) {
	return;
}

$embed_url   = (string) ( $row['embed_url'] ?? '' );
$embed_title = (string) ( $row['embed_title'] ?? '' );
?>
<section id="inicio" class="section hero<?php echo esc_attr( markea_tone( $row ) ); ?>">
	<div class="container">
		<div class="row hero__row">
			<div class="col-12 col-lg-7">
				<div class="hero__content">
					<?php echo markea_eyebrow( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<h1 class="hero__title"><?php echo markea_highlight( $title, (string) ( $row['highlight'] ?? '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h1>
					<?php if ( ! empty( $row['subtitle'] ) ) : ?>
						<p class="hero__lead"><?php echo esc_html( $row['subtitle'] ); ?></p>
					<?php endif; ?>
					<p class="hero__actions"><?php echo markea_cta( 'hero' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
				</div>
			</div>
			<div class="col-12 col-lg-5">
				<div class="hero__visual" data-hero-visual<?php echo $embed_url ? '' : ' aria-hidden="true"'; ?>>
					<?php if ( $embed_url ) : ?>
						<iframe
							class="hero__frame"
							title="<?php echo esc_attr( $embed_title ?: __( 'Visual interactivo', 'markea-ia' ) ); ?>"
							data-src="<?php echo esc_url( $embed_url ); ?>"
							allow="fullscreen; autoplay"
							referrerpolicy="strict-origin-when-cross-origin"
						></iframe>
						<noscript><iframe class="hero__frame" title="<?php echo esc_attr( $embed_title ?: __( 'Visual interactivo', 'markea-ia' ) ); ?>" src="<?php echo esc_url( $embed_url ); ?>"></iframe></noscript>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>
