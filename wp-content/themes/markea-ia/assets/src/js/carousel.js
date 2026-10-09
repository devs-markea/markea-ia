import '@splidejs/splide/css/core';

/**
 * Carousels (Splide 4.1.4). Any element with data-splide is mounted; options come from an
 * optional data-splide-config JSON attribute. Markup (build it with markea_carousel_attrs() in PHP):
 *
 * <div class="splide" data-splide data-splide-config='{"perPage":3}'>
 *   <div class="splide__track"><ul class="splide__list"><li class="splide__slide">...</li></ul></div>
 * </div>
 */
export async function initCarousels() {
	const els = document.querySelectorAll( '[data-splide]' );
	if ( ! els.length ) {
		return; // No carousel on the page: the Splide JS chunk is never downloaded.
	}

	const { default: Splide } = await import( '@splidejs/splide' );
	const reduceMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	els.forEach( ( el ) => {
		let config = {};
		try {
			config = JSON.parse( el.dataset.splideConfig || '{}' );
		} catch ( e ) {
			// Invalid JSON: fall back to defaults.
		}

		new Splide( el, {
			pagination: false,
			gap: '1.25rem',
			speed: reduceMotion ? 0 : 400,
			...config,
		} ).mount();
	} );
}
