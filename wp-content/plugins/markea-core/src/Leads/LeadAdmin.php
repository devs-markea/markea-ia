<?php
declare( strict_types=1 );

namespace Markea\Core\Leads;

use Markea\Core\PostTypes;

defined( 'ABSPATH' ) || exit;

/**
 * Read-only admin screens for leads: list columns and a details meta box.
 */
final class LeadAdmin {

	public function register(): void {
		add_filter( 'manage_' . PostTypes::LEAD . '_posts_columns', array( $this, 'columns' ) );
		add_action( 'manage_' . PostTypes::LEAD . '_posts_custom_column', array( $this, 'column' ), 10, 2 );
		add_action( 'add_meta_boxes', array( $this, 'meta_boxes' ) );
		add_filter( 'bulk_actions-edit-' . PostTypes::LEAD, '__return_empty_array' );
	}

	/**
	 * @param array<string,string> $columns
	 * @return array<string,string>
	 */
	public function columns( array $columns ): array {
		return array(
			'cb'       => $columns['cb'] ?? '',
			'title'    => 'Nombre',
			'email'    => 'Correo',
			'interest' => 'Me interesa',
			'company'  => 'Empresa',
			'channels' => 'Estado del correo',
			'date'     => 'Fecha',
		);
	}

	public function column( string $column, int $post_id ): void {
		switch ( $column ) {
			case 'email':
			case 'interest':
			case 'company':
				echo esc_html( LeadStore::get( $post_id, $column ) );
				break;
			case 'channels':
				echo esc_html( LeadStore::get( $post_id, 'email_status' ) );
				break;
		}
	}

	public function meta_boxes(): void {
		add_meta_box( 'markea_lead_details', 'Detalle del lead', array( $this, 'render' ), PostTypes::LEAD, 'normal', 'high' );
	}

	public function render( \WP_Post $post ): void {
		$rows = array(
			'Nombre'          => $post->post_title,
			'Correo'          => LeadStore::get( $post->ID, 'email' ),
			'Teléfono'        => LeadStore::get( $post->ID, 'phone' ),
			'Empresa'         => LeadStore::get( $post->ID, 'company' ),
			'Me interesa'     => LeadStore::get( $post->ID, 'interest' ),
			'Mensaje'         => $post->post_content,
			'Página de origen' => LeadStore::get( $post->ID, 'page_url' ),
			'Referrer'        => LeadStore::get( $post->ID, 'referrer' ),
			'utm_source'      => LeadStore::get( $post->ID, 'utm_source' ),
			'utm_medium'      => LeadStore::get( $post->ID, 'utm_medium' ),
			'utm_campaign'    => LeadStore::get( $post->ID, 'utm_campaign' ),
			'utm_term'        => LeadStore::get( $post->ID, 'utm_term' ),
			'utm_content'     => LeadStore::get( $post->ID, 'utm_content' ),
			'Estado correo'   => LeadStore::get( $post->ID, 'email_status' ),
		);

		echo '<table class="widefat striped"><tbody>';
		foreach ( $rows as $label => $value ) {
			printf( '<tr><th style="width:180px">%s</th><td>%s</td></tr>', esc_html( $label ), nl2br( esc_html( (string) $value ) ) );
		}
		echo '</tbody></table>';
	}
}
