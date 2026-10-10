/**
 * Scroll reveals and hero intro. Elements that animate are hidden by CSS under html.has-motion
 * (see "Motion" in main.css), so the lists below must match the selectors there.
 *
 * Default targets: the direct children of each section's .container (hero excluded).
 * Optional on any element: data-reveal="fade|scale|clip", data-reveal-y="<px>", data-reveal-delay="<s>".
 */
const FROM = {
	up: ( y ) => ( { autoAlpha: 0, y } ),
	fade: () => ( { autoAlpha: 0 } ),
	scale: ( y ) => ( { autoAlpha: 0, y: y / 2, scale: 0.94 } ),
	clip: () => ( { autoAlpha: 0, clipPath: 'inset(0% 0% 100% 0%)' } ),
};

const TO = { autoAlpha: 1, y: 0, scale: 1, clipPath: 'inset(0% 0% 0% 0%)' };

function revealTargets( section ) {
	return [ ...new Set( [ ...section.querySelectorAll( ':scope > .container > *' ), ...section.querySelectorAll( '[data-reveal]' ) ] ) ];
}

function tweenFor( gsap, ease, el, index, small ) {
	const kind = FROM[ el.dataset.reveal ] ? el.dataset.reveal : 'up';
	const distance = Number( el.dataset.revealY ) || 80;
	const from = FROM[ kind ]( small ? distance * 0.3 : distance );
	const to = { ...TO, duration: small ? 0.9 : 1.4, ease, delay: Number( el.dataset.revealDelay ) || 0 };
	// Only animate what the "from" state touches, so untouched properties keep their CSS values.
	Object.keys( TO ).forEach( ( key ) => {
		if ( ! ( key in from ) ) {
			delete to[ key ];
		}
	} );

	return { from, to, at: index * 0.08 };
}

export function initReveals( gsap, ease ) {
	const mm = gsap.matchMedia();

	mm.add( { wide: '(min-width: 992px)', narrow: '(max-width: 991.98px)' }, ( context ) => {
		const small = context.conditions.narrow;

		document.querySelectorAll( '#contenido > .section:not(.hero)' ).forEach( ( section ) => {
			const timeline = gsap.timeline( {
				scrollTrigger: { trigger: section, start: small ? 'top 85%' : 'top 75%', toggleActions: 'play none none reverse' },
			} );

			revealTargets( section ).forEach( ( el, index ) => {
				const { from, to, at } = tweenFor( gsap, ease, el, index, small );
				timeline.fromTo( el, from, to, at );
			} );
		} );

		document.querySelectorAll( '[data-reveal]:not(#contenido > .section *)' ).forEach( ( el ) => {
			const { from, to } = tweenFor( gsap, ease, el, 0, small );
			gsap.fromTo( el, from, { ...to, scrollTrigger: { trigger: el, start: 'top 85%', toggleActions: 'play none none reverse' } } );
		} );
	} );
}

/** Light sections fade out with the scroll as they leave through the top (desktop). Dark sections and the last one stay solid. */
export function initSectionLeave( gsap, ScrollTrigger ) {
	const mm = gsap.matchMedia();

	mm.add( '(min-width: 992px)', () => {
		const sections = [ ...document.querySelectorAll( '#contenido > .section:not(.hero):not(.section--dark)' ) ];
		sections.pop();

		sections.forEach( ( section ) => {
			ScrollTrigger.create( {
				trigger: section,
				start: 'bottom 40%',
				end: 'bottom 5%',
				scrub: true,
				invalidateOnRefresh: true,
				animation: gsap.to( section, { opacity: 0, ease: 'none' } ),
			} );
		} );
	} );
}

export function initHeroIntro( gsap, ease ) {
	const items = document.querySelectorAll( '.hero__content > *, .hero__visual' );
	if ( ! items.length ) {
		return;
	}

	gsap.fromTo( items, { autoAlpha: 0, y: 60 }, { autoAlpha: 1, y: 0, duration: 1.6, ease, stagger: 0.08 } );
}
