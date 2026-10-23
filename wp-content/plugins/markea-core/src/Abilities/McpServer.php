<?php
declare( strict_types=1 );

namespace Markea\Core\Abilities;

defined( 'ABSPATH' ) || exit;

/**
 * Dedicated MCP server for the site (endpoint /wp-json/mcp/markea) that exposes every markea/* ability as its own tool.
 * Needs the official "MCP Adapter" plugin (WordPress/mcp-adapter); without it this does nothing and the abilities
 * stay available through the REST API (wp-json/wp-abilities/v1).
 */
final class McpServer {

	public function register(): void {
		add_action( 'mcp_adapter_init', array( $this, 'create' ) );
	}

	/** @param object $adapter \WP\MCP\Core\McpAdapter */
	public function create( $adapter ): void {
		$transport = '\\WP\\MCP\\Transport\\HttpTransport';
		$errors    = '\\WP\\MCP\\Infrastructure\\ErrorHandling\\ErrorLogMcpErrorHandler';
		$observe   = '\\WP\\MCP\\Infrastructure\\Observability\\NullMcpObservabilityHandler';
		if ( ! class_exists( $transport ) || ! method_exists( $adapter, 'create_server' ) ) {
			return;
		}

		$tools = array_values( array_filter( array_keys( wp_get_abilities() ), static fn( $name ): bool => str_starts_with( (string) $name, 'markea/' ) ) );

		$result = $adapter->create_server(
			'markea',
			'mcp',
			'markea',
			'Markea IA',
			'Gestión del sitio Markea IA: landing, Site Settings, contenido, medios, menús y SEO.',
			MARKEA_CORE_VERSION,
			array( $transport ),
			$errors,
			$observe,
			$tools,
			array(),
			array(),
			// Only roles that can edit pages (Editor and up) may even open the connection; each tool still checks its own capability.
			static fn(): bool => current_user_can( 'edit_pages' )
		);

		if ( is_wp_error( $result ) ) {
			_doing_it_wrong( __METHOD__, esc_html( $result->get_error_message() ), '0.1.0' );
		}
	}
}
