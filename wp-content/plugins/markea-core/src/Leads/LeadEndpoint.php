<?php
declare( strict_types=1 );

namespace Markea\Core\Leads;

use Markea\Core\PostTypes;

defined( 'ABSPATH' ) || exit;

/**
 * POST /wp-json/markea/v1/leads
 *
 * Public on purpose (anonymous visitors submit the form). Protected by honeypot, per-IP rate limit,
 * strict validation and sanitization. The lead is always stored first; email and HubSpot are best effort.
 * Other plugins (e.g. a forms plugin) can react to the `markea_lead_created` action.
 */
final class LeadEndpoint {

	private const MAX_PER_WINDOW = 5;
	private const WINDOW         = 10 * MINUTE_IN_SECONDS;

	public function register(): void {
		add_action( 'rest_api_init', array( $this, 'routes' ) );
	}

	public function routes(): void {
		register_rest_route(
			'markea/v1',
			'/leads',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'handle' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	public function handle( \WP_REST_Request $request ) {
		$params = (array) $request->get_json_params();

		// Honeypot: pretend success so bots learn nothing.
		if ( ! empty( $params['website'] ) ) {
			return new \WP_REST_Response( array( 'ok' => true ), 201 );
		}

		if ( $this->rate_limited() ) {
			return new \WP_Error( 'markea_rate_limited', 'Demasiados envíos. Intenta más tarde.', array( 'status' => 429 ) );
		}

		$data = $this->sanitize( $params );

		if ( '' === $data['name'] || ! is_email( $data['email'] ) ) {
			return new \WP_Error( 'markea_invalid', 'Nombre y correo válido son obligatorios.', array( 'status' => 422 ) );
		}

		if ( '' !== (string) $this->option( 'form_consent_text' ) && empty( $params['consent'] ) ) {
			return new \WP_Error( 'markea_consent', 'Debes aceptar el aviso de privacidad.', array( 'status' => 422 ) );
		}

		$lead_id = LeadStore::create( $data );
		if ( ! $lead_id ) {
			return new \WP_Error( 'markea_store_failed', 'No se pudo guardar el envío.', array( 'status' => 500 ) );
		}

		LeadStore::set_status( $lead_id, 'email', $this->send_email( $data ) );
		LeadStore::set_status( $lead_id, 'hubspot', $this->send_hubspot( $data, $request ) );

		/**
		 * Fires after a lead is stored.
		 *
		 * @param int                  $lead_id Lead post ID.
		 * @param array<string,mixed>  $data    Sanitized lead data.
		 */
		do_action( 'markea_lead_created', $lead_id, $data );

		return new \WP_REST_Response( array( 'ok' => true ), 201 );
	}

	/**
	 * @param array<string,mixed> $params
	 * @return array<string,mixed>
	 */
	private function sanitize( array $params ): array {
		$attr = isset( $params['attribution'] ) && is_array( $params['attribution'] ) ? $params['attribution'] : array();
		$utm  = array();
		foreach ( array( 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content' ) as $key ) {
			$utm[ $key ] = isset( $attr[ $key ] ) ? sanitize_text_field( mb_substr( (string) $attr[ $key ], 0, 200 ) ) : '';
		}

		return array(
			'name'        => sanitize_text_field( mb_substr( (string) ( $params['name'] ?? '' ), 0, 150 ) ),
			'email'       => sanitize_email( (string) ( $params['email'] ?? '' ) ),
			'company'     => sanitize_text_field( mb_substr( (string) ( $params['company'] ?? '' ), 0, 150 ) ),
			'phone'       => sanitize_text_field( mb_substr( (string) ( $params['phone'] ?? '' ), 0, 40 ) ),
			'interest'    => sanitize_text_field( mb_substr( (string) ( $params['interest'] ?? '' ), 0, 150 ) ),
			'message'     => sanitize_textarea_field( mb_substr( (string) ( $params['message'] ?? '' ), 0, 4000 ) ),
			'page_url'    => esc_url_raw( (string) ( $params['page_url'] ?? '' ) ),
			'referrer'    => esc_url_raw( (string) ( $attr['referrer'] ?? '' ) ),
			'landing_url' => esc_url_raw( (string) ( $attr['landing_url'] ?? '' ) ),
			'utm'         => $utm,
		);
	}

	private function rate_limited(): bool {
		$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		$key = 'markea_rl_' . md5( $ip );
		$n   = (int) get_transient( $key );

		if ( $n >= self::MAX_PER_WINDOW ) {
			return true;
		}

		set_transient( $key, $n + 1, self::WINDOW );

		return false;
	}

	/**
	 * @param array<string,mixed> $data
	 */
	private function send_email( array $data ): string {
		$to = (string) $this->option( 'form_recipient' );
		$to = is_email( $to ) ? $to : (string) get_option( 'admin_email' );

		$lines = array(
			'Nombre: ' . $data['name'],
			'Correo: ' . $data['email'],
			'Empresa: ' . $data['company'],
			'Teléfono: ' . $data['phone'],
			'Me interesa: ' . $data['interest'],
			'Página: ' . $data['page_url'],
			'',
			$data['message'],
		);

		$headers = array( 'Reply-To: ' . $data['name'] . ' <' . $data['email'] . '>' );
		$subject = sprintf( 'Nuevo lead: %s', $data['name'] );

		return wp_mail( $to, $subject, implode( "\n", $lines ), $headers ) ? 'sent' : 'failed';
	}

	/**
	 * @param array<string,mixed> $data
	 */
	private function send_hubspot( array $data, \WP_REST_Request $request ): string {
		$portal = (string) $this->option( 'hubspot_portal_id' );
		$guid   = (string) $this->option( 'hubspot_form_guid' );

		if ( 1 !== preg_match( '/^\d+$/', $portal ) || 1 !== preg_match( '/^[0-9a-f-]{36}$/i', $guid ) ) {
			return 'skipped';
		}

		$fields = array(
			array( 'name' => 'email', 'value' => $data['email'] ),
			array( 'name' => 'firstname', 'value' => $data['name'] ),
			array( 'name' => 'company', 'value' => $data['company'] ),
			array( 'name' => 'phone', 'value' => $data['phone'] ),
			array( 'name' => 'message', 'value' => trim( $data['interest'] . "\n" . $data['message'] ) ),
		);

		$context = array( 'pageUri' => $data['page_url'], 'pageName' => get_bloginfo( 'name' ) );
		$cookie  = isset( $_COOKIE['hubspotutk'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['hubspotutk'] ) ) : '';
		if ( $cookie ) {
			$context['hutk'] = $cookie;
		}

		$response = wp_remote_post(
			sprintf( 'https://api.hsforms.com/submissions/v3/integration/submit/%s/%s', rawurlencode( $portal ), rawurlencode( $guid ) ),
			array(
				'timeout' => 8,
				'headers' => array( 'Content-Type' => 'application/json' ),
				'body'    => wp_json_encode( array( 'fields' => $fields, 'context' => $context ) ),
			)
		);

		return ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) ? 'sent' : 'failed';
	}

	/**
	 * @return mixed
	 */
	private function option( string $name ) {
		$key = 'markea_' . $name;

		return function_exists( 'get_field' ) ? get_field( $key, 'option' ) : get_option( 'options_' . $key );
	}
}
