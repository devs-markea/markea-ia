<?php
declare( strict_types=1 );

namespace Markea\Core\Seed;

use Markea\Core\Fields\F;
use Markea\Core\Fields\LandingFields;
use Markea\Core\PostTypes;

defined( 'ABSPATH' ) || exit;

/**
 * Idempotent initial content. Every seeded post carries _markea_seed_key; existing ones are skipped,
 * and fields that already have a value are never overwritten (unless $force is true).
 * Content that is not in the design source (copy of each section, tracking IDs) is intentionally NOT seeded.
 */
final class Seeder {

	private const SEED_KEY = '_markea_seed_key';

	/** @var string[] */
	private array $log = array();

	public function __construct( private bool $force = false ) {}

	/**
	 * @return string[]
	 */
	public function run(): array {
		if ( ! \Markea\Core\Plugin::has_acf_pro() ) {
			throw new \RuntimeException( 'ACF PRO no está activo: el seeder necesita sus campos.' );
		}

		$this->terms();
		$this->strategists();
		$this->projects();
		$this->settings();
		$this->landing();
		$this->menu();

		return $this->log;
	}

	private function terms(): void {
		foreach ( array( 'IA', 'CRM', 'E-Commerce', 'Automatización' ) as $name ) {
			if ( term_exists( $name, PostTypes::CATEGORY ) ) {
				$this->log[] = "skip term: $name";
				continue;
			}
			wp_insert_term( $name, PostTypes::CATEGORY );
			$this->log[] = "term: $name";
		}
	}

	private function strategists(): void {
		$people = array(
			array( 'Rodrigo Setién', 'AI-Native CEO' ),
			array( 'Dennis Yam', 'AI Tech Lead' ),
			array( 'Beatriz Paulino', 'AI Project Lead' ),
			array( 'Victoria Lozano', 'AI Specialist' ),
		);

		foreach ( $people as $i => [ $name, $role ] ) {
			$id = $this->post( PostTypes::STRATEGIST, 'estratega-' . sanitize_title( $name ), $name, $i + 1 );
			if ( $id ) {
				$this->field( 'role', $role, $id );
			}
		}
	}

	private function projects(): void {
		// Category mapping is an assumption taken from the project names; the team can change it in wp-admin.
		$projects = array(
			array( 'Hub Inbox Bot IA', array( 'IA' ) ),
			array( 'Page Builder with Agent IA', array( 'IA' ) ),
			array( 'Customer IA', array( 'IA' ) ),
			array( 'Conector Personalizado (MCP) CRM Real Estate', array( 'CRM' ) ),
			array( 'Tienda Super Foods', array( 'E-Commerce' ) ),
			array( 'CRM Inmobiliario KEA', array( 'CRM' ) ),
		);

		foreach ( $projects as $i => [ $title, $cats ] ) {
			$key = 'proyecto-' . sanitize_title( $title );
			$id  = $this->post( PostTypes::PROJECT, $key, $title, $i + 1 );
			if ( $id ) {
				wp_set_object_terms( $id, $cats, PostTypes::CATEGORY );
			}

			// Featured image from the design (plugin assets/seed), only when the project has none yet.
			$id = $id ?: $this->find_seeded( $key, PostTypes::PROJECT );
			if ( $id && ! has_post_thumbnail( $id ) ) {
				$this->attach_image( $id, MARKEA_CORE_DIR . 'assets/seed/' . sanitize_title( $title ) . '.jpg', $title );
			}
		}
	}

	private function settings(): void {
		foreach ( LandingContent::settings() as $name => $value ) {
			$current = (string) get_field( F::key( $name ), 'option', false );
			$legacy  = LandingContent::legacy_settings()[ $name ] ?? array();

			if ( in_array( $current, $legacy, true ) ) {
				update_field( F::key( $name ), $value, 'option' );
				$this->log[] = "field (updated): $name";
				continue;
			}

			$this->field( $name, $value, 'option' );
		}
	}

	/**
	 * Create the "Navegación principal" menu once and assign it to the primary location.
	 */
	private function menu(): void {
		$name = 'Navegación principal';
		$menu = wp_get_nav_menu_object( $name );

		if ( $menu ) {
			$this->log[] = "skip menu: $name";
		} else {
			$menu_id = wp_create_nav_menu( $name );
			if ( is_wp_error( $menu_id ) ) {
				throw new \RuntimeException( $menu_id->get_error_message() );
			}

			foreach ( LandingContent::menu() as $position => [ $label, $url ] ) {
				wp_update_nav_menu_item(
					$menu_id,
					0,
					array(
						'menu-item-title'    => $label,
						'menu-item-url'      => $url,
						'menu-item-type'     => 'custom',
						'menu-item-status'   => 'publish',
						'menu-item-position' => $position + 1,
					)
				);
			}

			$menu = wp_get_nav_menu_object( $menu_id );
			$this->log[] = "menu: $name";
		}

		$locations = get_theme_mod( 'nav_menu_locations', array() );
		if ( empty( $locations['primary'] ) ) {
			$locations['primary'] = (int) $menu->term_id;
			set_theme_mod( 'nav_menu_locations', $locations );
			$this->log[] = 'menu location: primary';
		}
	}

	private function landing(): void {
		$id = $this->find_seeded( 'landing' );

		if ( ! $id ) {
			$id = wp_insert_post(
				array(
					'post_type'   => 'page',
					'post_status' => 'publish',
					'post_title'  => 'Inicio Markea IA',
					'post_name'   => 'inicio-markea-ia',
				),
				true
			);
			if ( is_wp_error( $id ) ) {
				throw new \RuntimeException( $id->get_error_message() );
			}
			update_post_meta( $id, self::SEED_KEY, 'landing' );
			update_post_meta( $id, '_wp_page_template', LandingFields::TEMPLATE );
			$this->log[] = "page: Inicio Markea IA (#$id)";
		} else {
			$this->log[] = "skip page: Inicio Markea IA (#$id)";
		}

		// Copy from the Figma design. Existing rows are merged: empty fields are filled, fields the team wrote are kept.
		$current = get_field( F::key( 'sections' ), $id, false );
		$merged  = $this->merge_sections( is_array( $current ) ? $current : array(), LandingContent::sections() );
		update_field( F::key( 'sections' ), $merged, $id );
		$this->log[] = 'sections: ' . implode( ', ', array_column( $merged, 'acf_fc_layout' ) );

		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $id );
		$this->log[] = "front page => #$id";
	}

	/**
	 * Create a post once, keyed by _markea_seed_key.
	 */
	private function post( string $type, string $key, string $title, int $order ): int {
		$existing = $this->find_seeded( $key, $type );
		if ( $existing ) {
			$this->log[] = "skip $type: $title";

			return 0;
		}

		$id = wp_insert_post(
			array(
				'post_type'   => $type,
				'post_status' => 'publish',
				'post_title'  => $title,
				'menu_order'  => $order,
			),
			true
		);

		if ( is_wp_error( $id ) ) {
			throw new \RuntimeException( $id->get_error_message() );
		}

		update_post_meta( $id, self::SEED_KEY, $key );
		$this->log[] = "$type: $title";

		return (int) $id;
	}

	/**
	 * Merge seed rows into the existing Flexible Content rows.
	 * Seed order wins; rows of layouts that are not in the seed (added by the team) are kept at the end.
	 *
	 * @param array<int,array<string,mixed>> $existing Current rows.
	 * @param array<int,array<string,mixed>> $seed     Seed rows.
	 * @return array<int,array<string,mixed>>
	 */
	private function merge_sections( array $existing, array $seed ): array {
		// Unformatted rows come keyed by field key (field_markea_<layout>_<name>); use plain names to compare with the seed.
		$by_layout = array();
		foreach ( $existing as $row ) {
			$layout = (string) ( $row['acf_fc_layout'] ?? '' );
			$prefix = F::key( $layout ) . '_';
			$named  = array();
			foreach ( $row as $key => $value ) {
				$named[ str_starts_with( (string) $key, $prefix ) ? substr( (string) $key, strlen( $prefix ) ) : $key ] = $value;
			}
			$by_layout[ $layout ][] = $named;
		}

		$out = array();
		foreach ( $seed as $seed_row ) {
			$layout = $seed_row['acf_fc_layout'];
			$row    = isset( $by_layout[ $layout ] ) ? array_shift( $by_layout[ $layout ] ) : array( 'acf_fc_layout' => $layout );

			foreach ( $seed_row as $key => $value ) {
				if ( $this->force || ! isset( $row[ $key ] ) || '' === $row[ $key ] || array() === $row[ $key ] || null === $row[ $key ] ) {
					$row[ $key ] = $value;
				}
			}
			$out[] = $row;
		}

		foreach ( $by_layout as $rows ) {
			foreach ( $rows as $row ) {
				$out[] = $row;
			}
		}

		return $out;
	}

	/**
	 * Copy a bundled image into the media library and set it as the featured image.
	 */
	private function attach_image( int $post_id, string $file, string $alt ): void {
		if ( ! is_readable( $file ) ) {
			return;
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$tmp = wp_tempnam( $file );
		copy( $file, $tmp );

		$attachment_id = media_handle_sideload( array( 'name' => basename( $file ), 'tmp_name' => $tmp ), $post_id, $alt );
		if ( is_wp_error( $attachment_id ) ) {
			@unlink( $tmp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors, WordPress.WP.AlternativeFunctions
			$this->log[] = 'image failed: ' . $alt;

			return;
		}

		update_post_meta( $attachment_id, '_wp_attachment_image_alt', $alt );
		set_post_thumbnail( $post_id, (int) $attachment_id );
		$this->log[] = 'image: ' . $alt;
	}

	private function find_seeded( string $key, string $type = 'page' ): int {
		$found = get_posts(
			array(
				'post_type'      => $type,
				'post_status'    => 'any',
				'meta_key'       => self::SEED_KEY,
				'meta_value'     => $key,
				'fields'         => 'ids',
				'posts_per_page' => 1,
				'no_found_rows'  => true,
			)
		);

		return $found ? (int) $found[0] : 0;
	}

	/**
	 * Set a field only if it is empty (or $force).
	 *
	 * @param mixed      $value
	 * @param int|string $post_id Post ID or 'option'.
	 */
	private function field( string $name, $value, $post_id ): void {
		$key     = F::key( $name );
		$current = get_field( $key, $post_id, false );

		if ( ! $this->force && ! empty( $current ) ) {
			return;
		}

		update_field( $key, $value, $post_id );
		$this->log[] = "field: $name";
	}
}
