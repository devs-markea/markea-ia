<?php
declare( strict_types=1 );

namespace Markea\Core;

defined( 'ABSPATH' ) || exit;

/**
 * Prints GTM, GA4 and Meta Pixel from Site Settings (no deploy needed to change IDs).
 * By default nothing loads outside production (wp_get_environment_type()).
 */
final class Tracking {

	public function register(): void {
		add_action( 'wp_head', array( $this, 'head' ), 1 );
		add_action( 'markea_after_body_open', array( $this, 'body' ) );
	}

	public function head(): void {
		if ( ! $this->enabled() ) {
			return;
		}

		echo "<script>window.dataLayer=window.dataLayer||[];</script>\n";

		if ( $gtm = $this->id( 'gtm_id', '/^GTM-[A-Z0-9]{4,10}$/' ) ) {
			printf(
				"<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer',%s);</script>\n",
				wp_json_encode( $gtm )
			);
		}

		if ( $ga4 = $this->id( 'ga4_id', '/^G-[A-Z0-9]{6,12}$/' ) ) {
			printf( "<script async src=\"https://www.googletagmanager.com/gtag/js?id=%s\"></script>\n", esc_attr( $ga4 ) );
			printf( "<script>function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config',%s);</script>\n", wp_json_encode( $ga4 ) );
		}

		if ( $this->consent_granted() ) {
			if ( $pixel = $this->id( 'meta_pixel_id', '/^\d{8,20}$/' ) ) {
				printf(
					"<script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init',%s);fbq('track','PageView');</script>\n",
					wp_json_encode( $pixel )
				);
			}

		}
	}

	public function body(): void {
		if ( $this->enabled() && ( $gtm = $this->id( 'gtm_id', '/^GTM-[A-Z0-9]{4,10}$/' ) ) ) {
			printf(
				'<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=%s" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>' . "\n",
				esc_attr( $gtm )
			);
		}
	}

	private function enabled(): bool {
		if ( is_admin() || is_customize_preview() ) {
			return false;
		}

		$prod_only = $this->option( 'tracking_prod_only', true );
		$allowed   = ! $prod_only || 'production' === wp_get_environment_type();

		return (bool) apply_filters( 'markea_tracking_enabled', $allowed );
	}

	/**
	 * Gate for Meta Pixel until the cookie notice is defined. Hook a consent plugin here.
	 */
	private function consent_granted(): bool {
		return (bool) apply_filters( 'markea_tracking_consent_granted', true );
	}

	private function id( string $name, string $pattern ): string {
		$value = strtoupper( trim( (string) $this->option( $name, '' ) ) );

		return 1 === preg_match( $pattern, $value ) ? $value : '';
	}

	/**
	 * @param mixed $default
	 * @return mixed
	 */
	private function option( string $name, $default ) {
		$key   = 'markea_' . $name;
		$value = function_exists( 'get_field' ) ? get_field( $key, 'option' ) : get_option( 'options_' . $key );

		return ( null === $value || '' === $value ) ? $default : $value;
	}
}
