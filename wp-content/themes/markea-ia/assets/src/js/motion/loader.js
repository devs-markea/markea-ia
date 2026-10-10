/**
 * Entry loader: counter and bar fill while the page loads (capped at 5 s so it never traps the visitor),
 * then the screen fades out and the promise resolves so the hero can enter. Resolves right away when there is no loader.
 */
export function initLoader( gsap, ease ) {
	const root = document.documentElement;
	const el = document.querySelector( '[data-loader]' );
	if ( ! el || ! root.classList.contains( 'has-loader' ) ) {
		return Promise.resolve( false );
	}

	const count = el.querySelector( '[data-loader-count]' );
	const fill = el.querySelector( '[data-loader-fill]' );
	const logo = el.querySelector( '.loader__logo' );
	const bottom = el.querySelector( '.loader__bottom' );
	const progress = { value: 0 };

	const render = () => {
		count.textContent = Math.round( progress.value ) + '%';
		fill.style.transform = 'scaleX(' + progress.value / 100 + ')';
	};

	const loaded = new Promise( ( resolve ) => {
		if ( document.readyState === 'complete' ) {
			resolve();
		} else {
			window.addEventListener( 'load', resolve, { once: true } );
			window.setTimeout( resolve, 5000 );
		}
	} );

	return new Promise( ( resolve ) => {
		gsap.fromTo( bottom, { autoAlpha: 0, y: 60 }, { autoAlpha: 1, y: 0, duration: 1.2, ease } );
		gsap.fromTo( logo, { autoAlpha: 0 }, { autoAlpha: 1, duration: 1.2, ease } );

		const climb = gsap.to( progress, { value: 90, duration: 1.6, ease: 'power1.out', onUpdate: render } );

		Promise.all( [ loaded, new Promise( ( r ) => window.setTimeout( r, 1200 ) ) ] ).then( () => {
			climb.kill();
			gsap.to( progress, {
				value: 100,
				duration: 0.6,
				ease: 'power2.out',
				onUpdate: render,
				onComplete: () => {
					try {
						window.sessionStorage.setItem( 'markea-loader', '1' );
					} catch ( e ) {
						// Storage can be blocked; the loader just shows again next time.
					}

					const out = gsap.timeline( {
						defaults: { ease, duration: 1.2 },
						onComplete: () => root.classList.remove( 'has-loader' ),
					} );
					out.to( bottom, { autoAlpha: 0, y: 60 }, 0.2 )
						.to( logo, { autoAlpha: 0 }, 0.2 )
						.to( el, { autoAlpha: 0 }, 0.6 )
						.call( resolve, null, 0.9 );
				},
			} );
		} );
	} );
}
