<?php
declare( strict_types=1 );

namespace Markea\Core\Abilities;

defined( 'ABSPATH' ) || exit;

/**
 * Change log for everything written through abilities: who, when, what and the previous value, so any edit can
 * be undone by writing the old value back. Kept in a non-autoloaded option (last 200 entries).
 */
final class Audit {

	private const OPTION = 'markea_mcp_audit';
	private const MAX    = 200;
	private const CLIP   = 2000;

	/**
	 * @param array<int,array{field:string,old:mixed,new:mixed}> $changes
	 */
	public static function record( string $ability, string $target, array $changes ): void {
		$user = wp_get_current_user();
		$log  = get_option( self::OPTION, array() );
		$log  = is_array( $log ) ? $log : array();

		$log[] = array(
			'time'    => gmdate( 'c' ),
			'user'    => $user->ID ? $user->user_login : 'cli',
			'ability' => $ability,
			'target'  => $target,
			'changes' => array_map(
				static fn( array $c ): array => array(
					'field' => $c['field'],
					'old'   => self::clip( $c['old'] ),
					'new'   => self::clip( $c['new'] ),
				),
				$changes
			),
		);

		update_option( self::OPTION, array_slice( $log, -self::MAX ), false );
	}

	/** @return array<int,array<string,mixed>> Newest first. */
	public static function latest( int $limit ): array {
		$log = get_option( self::OPTION, array() );

		return is_array( $log ) ? array_slice( array_reverse( $log ), 0, max( 1, $limit ) ) : array();
	}

	/** @param mixed $value */
	private static function clip( $value ): string {
		$text = is_scalar( $value ) || null === $value ? (string) $value : (string) wp_json_encode( $value, JSON_UNESCAPED_UNICODE );

		return mb_strlen( $text ) > self::CLIP ? mb_substr( $text, 0, self::CLIP ) . '…' : $text;
	}
}
