<?php
declare( strict_types=1 );

namespace Markea\Core\Abilities;

defined( 'ABSPATH' ) || exit;

/**
 * Site management exposed as WordPress Abilities (core Abilities API), so an MCP adapter, the REST API
 * (wp-abilities/v1) or any agent can edit the site without wp-admin.
 *
 * Policy (decided with the site owner): edit, publish and move to trash; never delete for good; nothing about
 * users, plugins, themes, code or tracking. Every ability checks the current user's native WordPress
 * capabilities, so a role can only do what it can already do in wp-admin. Every write is logged (see Audit).
 */
final class Abilities {

	public const CATEGORIES = array(
		'markea-site'     => array( 'Sitio', 'Información general del sitio, caché y bitácora de cambios.' ),
		'markea-landing'  => array( 'Landing', 'Secciones de la landing (campos ACF de la página de inicio).' ),
		'markea-settings' => array( 'Site Settings', 'Opciones del sitio (contacto, redes, marca, menú, footer).' ),
		'markea-content'  => array( 'Contenido', 'Páginas, entradas, proyectos, estrategas, campos y SEO.' ),
		'markea-media'    => array( 'Medios', 'Biblioteca de medios.' ),
		'markea-menus'    => array( 'Menús', 'Menús de navegación.' ),
	);

	public function register(): void {
		add_action( 'wp_abilities_api_categories_init', array( $this, 'register_categories' ) );
		add_action( 'wp_abilities_api_init', array( $this, 'register_abilities' ) );
		( new McpServer() )->register();
		( new HeaderAuth() )->register();
	}

	public function register_categories(): void {
		foreach ( self::CATEGORIES as $slug => $data ) {
			wp_register_ability_category( $slug, array( 'label' => $data[0], 'description' => $data[1] ) );
		}
	}

	public function register_abilities(): void {
		( new SiteAbilities() )->register();
		( new ContentAbilities() )->register();
		( new MediaAbilities() )->register();
		( new MenuAbilities() )->register();

		// ACF-dependent areas.
		if ( function_exists( 'acf_get_field' ) ) {
			( new LandingAbilities() )->register();
			( new SettingsAbilities() )->register();
		}
	}

	/**
	 * Registers one ability. $kind sets the REST/MCP hints: read (no side effects), write (idempotent edit) or
	 * create (additive).
	 *
	 * @param array<string,mixed> $args label, description, category, input_schema, output_schema, execute_callback, permission_callback.
	 */
	public static function define( string $name, string $kind, array $args ): void {
		$annotations = array(
			'readonly'    => 'read' === $kind,
			'destructive' => false,
			'idempotent'  => 'create' !== $kind,
		);

		wp_register_ability(
			$name,
			array_merge(
				$args,
				array(
					'meta' => array(
						'annotations'  => $annotations,
						'public'       => true,
						'show_in_rest' => true,
						'mcp'          => array( 'public' => true ),
					),
				)
			)
		);
	}

	/** Permission callback factory: the current user must have the capability (checked per call). */
	public static function can( string $capability ): callable {
		return static fn(): bool => current_user_can( $capability );
	}

	/** @param array<string,mixed> $properties @param array<int,string> $required @return array<string,mixed> */
	public static function object_schema( array $properties, array $required = array() ): array {
		return array(
			'type'                 => 'object',
			'properties'           => $properties,
			'required'             => $required,
			'additionalProperties' => false,
		);
	}
}
