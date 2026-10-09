const UTM_KEYS = [ 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content' ];
const STORAGE_KEY = 'markea_attribution';

/**
 * Push an event to the GTM dataLayer.
 */
export function track( event, params = {} ) {
	window.dataLayer = window.dataLayer || [];
	window.dataLayer.push( { event, ...params } );
}

/**
 * First-touch attribution (UTM, referrer, landing URL), kept for the session.
 */
export function getAttribution() {
	try {
		return JSON.parse( sessionStorage.getItem( STORAGE_KEY ) || '{}' );
	} catch ( e ) {
		return {};
	}
}

function captureAttribution() {
	try {
		if ( sessionStorage.getItem( STORAGE_KEY ) ) {
			return;
		}
		const params = new URLSearchParams( window.location.search );
		const data = { referrer: document.referrer, landing_url: window.location.href.split( '#' )[ 0 ] };
		UTM_KEYS.forEach( ( key ) => {
			if ( params.get( key ) ) {
				data[ key ] = params.get( key );
			}
		} );
		sessionStorage.setItem( STORAGE_KEY, JSON.stringify( data ) );
	} catch ( e ) {
		// Storage can be blocked; attribution is best effort.
	}
}

/**
 * Delegated click tracking driven by data-track attributes in the markup.
 */
export function initTracking() {
	captureAttribution();

	document.addEventListener( 'click', ( e ) => {
		const el = e.target.closest( '[data-track]' );
		if ( ! el ) {
			return;
		}

		switch ( el.dataset.track ) {
			case 'cta':
				track( 'markea_cta_click', { cta_label: el.dataset.trackLabel, cta_location: el.dataset.trackLocation } );
				break;
			case 'contact':
				track( 'markea_contact_click', { contact_type: el.dataset.trackType, network: el.dataset.trackNetwork || undefined } );
				break;
		}
	} );
}
