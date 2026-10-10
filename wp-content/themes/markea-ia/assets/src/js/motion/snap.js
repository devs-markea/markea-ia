/**
 * Desktop slides. When the visitor scrolls and the next (or previous) section that fits in the window crosses
 * the 70% line, the scroll glides to its start. Directional, like the reference site, so a short wheel gesture
 * always moves forward. Only user-driven scrolling triggers it (anchors and the menu are left alone), and tall
 * sections scroll freely.
 */
export function initSnap( gsap, ScrollTrigger, lenis ) {
	const mm = gsap.matchMedia();

	mm.add( '(min-width: 992px) and (pointer: fine)', () => {
		const header = document.querySelector( '.site-header' );
		const offset = () => ( header ? header.offsetHeight : 0 );
		let busy = false;
		let lastInput = 0;

		const markInput = () => {
			lastInput = performance.now();
		};
		[ 'wheel', 'keydown', 'touchstart' ].forEach( ( type ) => window.addEventListener( type, markInput, { passive: true } ) );

		const glideTo = ( section ) => {
			// Ignore programmatic scrolling (anchors, menu) and anything fired while a glide is running.
			if ( busy || performance.now() - lastInput > 1500 ) {
				return;
			}

			busy = true;
			lenis.scrollTo( Math.round( section.getBoundingClientRect().top + window.scrollY - offset() ), {
				duration: 1.2,
				easing: ( t ) => 1 - Math.pow( 1 - t, 4 ),
				lock: true,
				onComplete: () => {
					busy = false;
				},
			} );
		};

		const triggers = [];
		const build = () => {
			triggers.forEach( ( trigger ) => trigger.kill() );
			triggers.length = 0;

			const room = window.innerHeight - offset();
			document.querySelectorAll( '#contenido > .section' ).forEach( ( section ) => {
				if ( section.offsetHeight <= room * 1.4 ) {
					triggers.push(
						ScrollTrigger.create( {
							trigger: section,
							start: 'top 70%',
							end: 'bottom 30%',
							onEnter: () => glideTo( section ),
							onEnterBack: () => glideTo( section ),
						} )
					);
				}
			} );
		};

		// Positions are read when a glide starts; only which sections qualify depends on the window size.
		let resizeTimer;
		const onResize = () => {
			window.clearTimeout( resizeTimer );
			resizeTimer = window.setTimeout( build, 300 );
		};

		build();
		window.addEventListener( 'resize', onResize );

		return () => {
			window.clearTimeout( resizeTimer );
			window.removeEventListener( 'resize', onResize );
			triggers.forEach( ( trigger ) => trigger.kill() );
			[ 'wheel', 'keydown', 'touchstart' ].forEach( ( type ) => window.removeEventListener( type, markInput ) );
		};
	} );
}
