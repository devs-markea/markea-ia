<?php
declare( strict_types=1 );

namespace Markea\Core\Leads;

use Markea\Core\PostTypes;

defined( 'ABSPATH' ) || exit;

/**
 * Persists leads as private `lead` posts. Meta keys are prefixed _markea_lead_.
 */
final class LeadStore {

	public const META_PREFIX = '_markea_lead_';

	/**
	 * @param array<string,mixed> $data
	 */
	public static function create( array $data ): int {
		$id = wp_insert_post(
			array(
				'post_type'    => PostTypes::LEAD,
				'post_status'  => 'publish',
				'post_title'   => $data['name'],
				'post_content' => $data['message'],
			),
			true
		);

		if ( is_wp_error( $id ) || ! $id ) {
			return 0;
		}

		foreach ( array( 'email', 'company', 'phone', 'interest', 'page_url', 'referrer', 'landing_url' ) as $key ) {
			update_post_meta( $id, self::META_PREFIX . $key, $data[ $key ] );
		}
		foreach ( $data['utm'] as $key => $value ) {
			update_post_meta( $id, self::META_PREFIX . $key, $value );
		}

		self::set_status( $id, 'email', 'pending' );
		self::set_status( $id, 'hubspot', 'pending' );

		return (int) $id;
	}

	public static function set_status( int $lead_id, string $channel, string $status ): void {
		update_post_meta( $lead_id, self::META_PREFIX . $channel . '_status', $status );
	}

	public static function get( int $lead_id, string $key ): string {
		return (string) get_post_meta( $lead_id, self::META_PREFIX . $key, true );
	}
}
