/**
 * Extra motion inspired by the Dribbble reference (Oviora): content that lands inside its card after the card,
 * glows that wander across the "Oferta" cards, a year counter and a hero visual that grows in.
 * Elements hidden here must match the "Motion extras" block in main.css.
 */
const CARDS = '.card--elevated, .offer__card, .how__card, .next__step';

/** Card contents arrive one after another once the section's own reveal has started. */
function initCardContent( gsap, ease ) {
	const mm = gsap.matchMedia();

	mm.add( { wide: '(min-width: 992px)', narrow: '(max-width: 991.98px)' }, ( context ) => {
		const small = context.conditions.narrow;

		document.querySelectorAll( '#contenido > .section:not(.hero)' ).forEach( ( section ) => {
			const cards = section.querySelectorAll( CARDS );
			if ( ! cards.length ) {
				return;
			}

			const timeline = gsap.timeline( {
				scrollTrigger: { trigger: section, start: small ? 'top 85%' : 'top 60%', once: true },
			} );

			cards.forEach( ( card, index ) => {
				timeline.fromTo(
					[ ...card.children ],
					{ autoAlpha: 0, y: 18 },
					{ autoAlpha: 1, y: 0, duration: 0.9, ease, stagger: 0.09 },
					0.45 + index * 0.12
				);
			} );
		} );
	} );
}

/** "Oferta": the cards' glows only move while the cards are on screen. */
function initOfferGlow( ScrollTrigger ) {
	const list = document.querySelector( '.offer__cards' );
	if ( ! list ) {
		return;
	}

	ScrollTrigger.create( {
		trigger: list,
		start: 'top bottom',
		end: 'bottom top',
		onToggle: ( self ) => list.classList.toggle( 'is-live', self.isActive ),
	} );
}

/** "8 anos": the number counts up the first time the title shows. */
function initYearCounter( gsap, ScrollTrigger ) {
	const strong = document.querySelector( '.about__title .u-strong' );
	const match = strong ? strong.textContent.match( /^(\s*)(\d+)(.*)$/s ) : null;
	if ( ! match ) {
		return;
	}

	const [ , lead, digits, rest ] = match;
	const target = Number( digits );
	const state = { value: 0 };
	strong.style.fontVariantNumeric = 'tabular-nums';
	strong.textContent = lead + '0' + rest;

	ScrollTrigger.create( {
		trigger: strong,
		start: 'top 85%',
		once: true,
		onEnter: () =>
			gsap.to( state, {
				value: target,
				duration: 1.6,
				ease: 'power2.out',
				delay: 0.3,
				onUpdate: () => ( strong.textContent = lead + Math.round( state.value ) + rest ),
			} ),
	} );
}

/** Hero visual grows in (the generic intro only fades and lifts it). */
export function initHeroVisual( gsap, ScrollTrigger, ease ) {
	const visual = document.querySelector( '.hero__visual' );
	if ( ! visual ) {
		return;
	}

	gsap.fromTo( visual, { scale: 0.9 }, { scale: 1, duration: 1.4, ease: 'expo.out', delay: 0.15 } );
}

/** Marketing Operating System: clicking (or Enter / Space on) a pillar makes it the active one. */
function initPillars( ScrollTrigger ) {
	const list = document.querySelector( '.mos__pillars' );
	const cards = list ? [ ...list.querySelectorAll( '.mos__card' ) ] : [];
	const desktop = window.matchMedia( '(min-width: 992px)' );
	if ( cards.length < 2 ) {
		return;
	}

	// The active card's illustration waits for the visitor to reach the pillars, then plays once.
	ScrollTrigger.create( { trigger: list, start: 'top 65%', once: true, onEnter: () => list.classList.add( 'is-in' ) } );

	const activate = ( index ) => {
		list.dataset.active = String( index );
		cards.forEach( ( card, i ) => {
			card.classList.toggle( 'is-active', i === index );
			card.setAttribute( 'aria-current', i === index ? 'true' : 'false' );
		} );
	};

	cards.forEach( ( card, i ) => {
		card.tabIndex = 0;
		card.setAttribute( 'aria-current', i === 0 ? 'true' : 'false' );
		card.addEventListener( 'click', () => desktop.matches && activate( i ) );
		card.addEventListener( 'keydown', ( e ) => {
			if ( desktop.matches && ( e.key === 'Enter' || e.key === ' ' ) ) {
				e.preventDefault();
				activate( i );
			}
		} );
	} );
}

/** "Proximos pasos": the corner ellipse of each card follows the cursor (--mx / --my, px inside the card) and returns on leave. */
function initCardLight() {
	if ( ! window.matchMedia( '(hover: hover) and (pointer: fine)' ).matches ) {
		return;
	}

	document.querySelectorAll( '.next__step' ).forEach( ( card ) => {
		card.addEventListener( 'pointermove', ( e ) => {
			const rect = card.getBoundingClientRect();
			card.style.setProperty( '--mx', e.clientX - rect.left + 'px' );
			card.style.setProperty( '--my', e.clientY - rect.top + 'px' );
		} );
		card.addEventListener( 'pointerleave', () => {
			card.style.removeProperty( '--mx' );
			card.style.removeProperty( '--my' );
		} );
	} );
}

/** "Proximos pasos": the timeline disc of the hovered (or focused) step lights up; the first one is the default. */
function initNextSteps() {
	const steps = [ ...document.querySelectorAll( '.next__step' ) ];
	const discs = [ ...document.querySelectorAll( '.next__disc' ) ];
	if ( ! steps.length || steps.length !== discs.length ) {
		return;
	}

	const light = ( index ) => discs.forEach( ( disc, i ) => disc.classList.toggle( 'next__disc--filled', i === index ) );

	steps.forEach( ( step, i ) => {
		step.addEventListener( 'pointerenter', () => light( i ) );
		step.addEventListener( 'pointerleave', () => light( 0 ) );
	} );
}

export function initExtras( gsap, ScrollTrigger, ease ) {
	initCardLight();
	initNextSteps();
	initPillars( ScrollTrigger );
	initCardContent( gsap, ease );
	initOfferGlow( ScrollTrigger );
	initYearCounter( gsap, ScrollTrigger );
}
