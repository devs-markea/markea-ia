/**
 * Motion gate. The heavy part (GSAP, ScrollTrigger, Lenis) is a separate chunk that only loads when the
 * head script allowed motion (html.has-motion: not reduced motion and not switched off in Site Settings).
 */
export function initMotion() {
	if ( ! document.documentElement.classList.contains( 'has-motion' ) ) {
		return;
	}

	import( './motion/engine.js' )
		.then( ( { startMotion } ) => startMotion() )
		.catch( () => {
			// Never leave content hidden if the chunk fails to load.
			document.documentElement.classList.remove( 'has-motion', 'has-loader' );
		} );
}
