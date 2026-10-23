<?php
declare( strict_types=1 );

namespace Markea\Core\Abilities;

defined( 'ABSPATH' ) || exit;

/**
 * Lets MCP clients that cannot set the Authorization header (claude.ai custom connectors only allow approved header
 * names) send the same Basic credential in X-API-Key. It is copied into PHP_AUTH_USER / PHP_AUTH_PW only on the MCP
 * and abilities routes, and WordPress itself then validates it as an application password (nothing is validated here,
 * so Application Passwords stay the only credential: revocable, per user, capped by the user's role).
 *
 * Value: "Basic <base64 of user:application-password>" (the bare base64 also works).
 */
final class HeaderAuth {

	public function register(): void {
		// Priority 5: before WordPress reads PHP_AUTH_* for application passwords (priority 20).
		add_filter( 'determine_current_user', array( $this, 'translate' ), 5 );
	}

	/**
	 * @param int|false $user_id
	 * @return int|false
	 */
	public function translate( $user_id ) {
		if ( $user_id || empty( $_SERVER['HTTP_X_API_KEY'] ) || ! empty( $_SERVER['PHP_AUTH_USER'] ) || ! $this->is_agent_route() ) {
			return $user_id;
		}

		$value   = trim( (string) wp_unslash( $_SERVER['HTTP_X_API_KEY'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- decoded and checked below.
		$decoded = base64_decode( (string) preg_replace( '/^Basic\s+/i', '', $value ), true );
		if ( false === $decoded || ! str_contains( $decoded, ':' ) ) {
			return $user_id;
		}

		[ $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'] ] = explode( ':', $decoded, 2 );

		return $user_id;
	}

	/** Only the MCP server and the abilities REST routes honor the header. */
	private function is_agent_route(): bool {
		$prefix = '/' . rest_get_url_prefix() . '/';
		$uri    = (string) wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		$path   = (string) wp_parse_url( $uri, PHP_URL_PATH );
		$query  = rawurldecode( (string) wp_parse_url( $uri, PHP_URL_QUERY ) );

		foreach ( array( 'mcp/markea', 'wp-abilities/v1/' ) as $route ) {
			if ( str_contains( $path, $prefix . $route ) || str_contains( $query, 'rest_route=/' . $route ) ) {
				return true;
			}
		}

		return false;
	}
}
