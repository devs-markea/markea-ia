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
			array( 'Rodrigo Setién', 'AI Native CEO' ),
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
			array( 'Conector Personalizado (MCP) CRM Real Estate', array( 'IA', 'CRM' ) ),
			array( 'Tienda Super Foods', array( 'E-Commerce' ) ),
			array( 'CRM Inmobiliario KEA', array( 'CRM' ) ),
		);

		foreach ( $projects as $i => [ $title, $cats ] ) {
			$id = $this->post( PostTypes::PROJECT, 'proyecto-' . sanitize_title( $title ), $title, $i + 1 );
			if ( $id ) {
				wp_set_object_terms( $id, $cats, PostTypes::CATEGORY );
			}
		}
	}

	private function settings(): void {
		$values = array(
			'contact_email'        => 'hola@markea.agency',
			'contact_address'      => 'Av. Nader, Cancún, Quintana Roo',
			'form_success_message' => 'Gracias, te contactaremos pronto.',
		);

		foreach ( $values as $name => $value ) {
			$this->field( $name, $value, 'option' );
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

		// Only what the brief states explicitly. Section copy comes from the design source and is loaded separately.
		// Sections are Flexible Content layouts; the team can add and reorder them in wp-admin.
		$this->field(
			'sections',
			array(
				array( 'acf_fc_layout' => 'hero', 'cta_label' => 'Hablemos', 'cta_url' => '#contacto' ),
				array(
					'acf_fc_layout' => 'about',
					'years'         => '8 años',
					'items'         => array(
						array( 'title' => 'Marcas propias', 'text' => '' ),
						array( 'title' => 'Clientes externos', 'text' => '' ),
					),
				),
				array( 'acf_fc_layout' => 'projects' ),
				array( 'acf_fc_layout' => 'strategists' ),
			),
			$id
		);

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
