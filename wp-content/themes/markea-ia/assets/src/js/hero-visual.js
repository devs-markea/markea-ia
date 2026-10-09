/**
 * Loads the hero iframe (e.g. a 3D scene) after the page is interactive, so it never blocks first paint.
 * The iframe keeps its URL in data-src; without JS the <noscript> copy loads it directly.
 */
export function initHeroVisual() {
	const frame = document.querySelector( '[data-hero-visual] iframe[data-src]' );
	if ( ! frame ) {
		return;
	}

	const load = () => {
		frame.src = frame.dataset.src;
		frame.addEventListener( 'load', () => frame.closest( '[data-hero-visual]' )?.classList.add( 'is-loaded' ), { once: true } );
	};

	const schedule = () => ( 'requestIdleCallback' in window ? window.requestIdleCallback( load, { timeout: 2000 } ) : window.setTimeout( load, 300 ) );

	if ( document.readyState === 'complete' ) {
		schedule();
	} else {
		window.addEventListener( 'load', schedule, { once: true } );
	}
}
