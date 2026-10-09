<?php
/**
 * Cards section: problema.
 *
 * @package Markea_IA
 */

defined( 'ABSPATH' ) || exit;

$title = (string) markea_field( 'problem_title' );
$rows  = markea_rows( 'problem_items' );
if ( '' === $title && ! $rows ) {
	return;
}
?>
<section id="problema" class="section problem">
	<div class="container">
		<?php if ( $title ) : ?>
			<h2 class="section__title"><?php echo esc_html( $title ); ?></h2>
		<?php endif; ?>
		<?php if ( $rows ) : ?>
			<ol class="cards cards--3" role="list">
				<?php foreach ( $rows as $i => $row ) : ?>
					<li class="card">
						<span class="card__index" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<?php if ( ! empty( $row['title'] ) ) : ?>
							<h3 class="card__title"><?php echo esc_html( $row['title'] ); ?></h3>
						<?php endif; ?>
						<?php if ( ! empty( $row['text'] ) ) : ?>
							<p class="card__text"><?php echo esc_html( $row['text'] ); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>
	</div>
</section>
