<?php
declare( strict_types=1 );

namespace Markea\Core\Fields;

defined( 'ABSPATH' ) || exit;

/**
 * Global settings (ACF Options Page). The tracking tab is admin-only: fields whose key starts with
 * field_markea_admin_ are not rendered for users without manage_options, so they are never submitted
 * and never overwritten when an editor saves the page.
 */
final class SiteSettings {

	public const SLUG = 'markea-settings';

	public function register(): void {
		acf_add_options_page(
			array(
				'page_title'  => 'Site Settings',
				'menu_title'  => 'Site Settings',
				'menu_slug'   => self::SLUG,
				'capability'  => 'edit_pages',
				'icon_url'    => 'dashicons-admin-generic',
				'position'    => 24,
				'redirect'    => false,
				'autoload'    => false,
				'update_button' => 'Guardar ajustes',
			)
		);

		acf_add_local_field_group(
			array(
				'key'      => 'group_markea_settings',
				'title'    => 'Ajustes del sitio',
				'fields'   => $this->fields(),
				'location' => array( array( array( 'param' => 'options_page', 'operator' => '==', 'value' => self::SLUG ) ) ),
			)
		);

		add_filter( 'acf/prepare_field', array( $this, 'restrict_admin_fields' ) );
	}

	/**
	 * @param array<string,mixed>|false $field
	 * @return array<string,mixed>|false
	 */
	public function restrict_admin_fields( $field ) {
		if ( is_array( $field ) && str_starts_with( (string) ( $field['key'] ?? '' ), 'field_markea_admin_' ) && ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		return $field;
	}

	/** @return array<int,array<string,mixed>> */
	private function fields(): array {
		$admin = static function ( array $field ): array {
			$field['key'] = str_replace( 'field_markea_', 'field_markea_admin_', $field['key'] );

			return $field;
		};

		return array(
			$admin( F::tab( 'set_tracking', 'Tracking y métricas' ) ),
			$admin( F::text( 'gtm_id', 'ID de Google Tag Manager', array( 'placeholder' => 'GTM-XXXXXXX', 'instructions' => 'Si usas GTM, configura GA4 dentro de GTM y deja vacío el campo de GA4 para no contar doble.' ) ) ),
			$admin( F::text( 'ga4_id', 'ID de Google Analytics 4', array( 'placeholder' => 'G-XXXXXXXXXX' ) ) ),
			$admin( F::text( 'meta_pixel_id', 'ID de Meta Pixel', array( 'instructions' => 'Activar solo cuando el aviso de cookies esté definido.' ) ) ),
			$admin( F::text( 'hubspot_portal_id', 'ID de portal de HubSpot', array( 'instructions' => 'Activar solo cuando el aviso de cookies esté definido.' ) ) ),
			$admin( F::toggle( 'tracking_prod_only', 'Cargar solo en producción', array( 'default_value' => 1, 'message' => 'Evita contaminar la analítica desde local y staging.' ) ) ),

			F::tab( 'set_contact', 'Contacto' ),
			F::email( 'contact_email', 'Correo' ),
			F::text( 'contact_phone', 'Teléfono' ),
			F::textarea( 'contact_address', 'Dirección', array( 'rows' => 2 ) ),

			F::tab( 'set_social', 'Redes' ),
			F::url( 'social_linkedin', 'LinkedIn' ),
			F::url( 'social_instagram', 'Instagram' ),
			F::url( 'social_facebook', 'Facebook' ),

			F::tab( 'set_brand', 'Marca' ),
			F::image( 'brand_logo', 'Logo' ),
			F::image( 'brand_favicon', 'Favicon', array( 'instructions' => 'Cuadrado, mínimo 512 px. También se puede usar el icono del sitio de WordPress.' ) ),
			F::gallery( 'brand_partner_badges', 'Badges de partners', array( 'instructions' => 'Google Partner, HubSpot, Meta Partner.' ) ),
			F::wysiwyg( 'brand_legal_text', 'Texto legal del footer' ),

			F::tab( 'set_form', 'Formulario' ),
			F::email( 'form_recipient', 'Correo destino', array( 'instructions' => 'Si queda vacío se usa el correo del administrador del sitio.' ) ),
			F::text( 'hubspot_form_guid', 'GUID del formulario de HubSpot', array( 'instructions' => 'Opcional. Requiere el ID de portal en Tracking y métricas.' ) ),
			F::text( 'form_success_message', 'Mensaje de éxito' ),
			F::wysiwyg( 'form_consent_text', 'Texto de consentimiento', array( 'instructions' => 'Si se llena, la casilla es obligatoria. Pendiente de definir con legal.' ) ),

			F::tab( 'set_newsletter', 'Newsletter' ),
			F::url( 'newsletter_action', 'Destino del formulario del footer', array( 'instructions' => 'A definir. Mientras esté vacío no se muestra el formulario.' ) ),
		);
	}
}
