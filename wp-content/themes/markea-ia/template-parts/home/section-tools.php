<?php
/**
 * Section 9: Herramientas. A bar with the statement on the left and a looping row of logo cards.
 * The second copy of the list exists only to make the loop seamless (hidden from assistive tech).
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

$cards = static function ( array $items ): string {
	$out = '';
	foreach ( $items as $item ) {
		$logo = markea_tool_logo( (array) $item );
		$name = (string) ( $item['name'] ?? '' );
		$out .= '<li class="tools__card">' . ( $logo ?: '<span class="tools__name">' . esc_html( $name ) . '</span>' ) . ( $logo && $name ? '<span class="screen-reader-text">' . esc_html( $name ) . '</span>' : '' ) . '</li>';
	}

	return $out;
};
?>
<section id="herramientas" class="section tools<?php echo esc_attr( markea_tone( $row ) ); ?>">
	<?php echo markea_section_anchor( $row ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in the helper. ?>
	<div class="container tools__bar">
		<div class="tools__intro">
			<?php echo markea_icon( 'asterisk', 48 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php if ( $title ) : ?>
				<h2 class="tools__title"><?php echo markea_title( $row ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
			<?php endif; ?>
		</div>

		<?php if ( $items ) : ?>
			<div class="tools__marquee">
				<ul class="tools__track" role="list">
					<?php echo $cards( $items ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</ul>
				<ul class="tools__track" role="presentation" aria-hidden="true">
					<?php echo $cards( $items ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</ul>
			</div>
		<?php endif; ?>
	</div>
</section>
