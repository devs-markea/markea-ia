import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { CustomEase } from 'gsap/CustomEase';
import Lenis from 'lenis';
import { initLoader } from './loader.js';
import { initHeroIntro, initReveals, initSectionLeave } from './reveal.js';
import { initButtons } from './buttons.js';

// One easing for everything: slow start, long soft landing (same curve as --ease-markea in CSS).
export const EASE = 'markea';

export function startMotion() {
	gsap.registerPlugin( ScrollTrigger, CustomEase );
	CustomEase.create( EASE, '0.6, 0.08, 0.02, 0.99' );

	const root = document.documentElement;
	const header = document.querySelector( '.site-header' );

	// Smooth scroll, driven by GSAP's ticker so ScrollTrigger stays in sync.
	const lenis = new Lenis( {
		lerp: 0.1,
		wheelMultiplier: 0.8,
		anchors: { offset: -( header ? header.offsetHeight : 0 ) },
	} );
	lenis.on( 'scroll', ScrollTrigger.update );
	gsap.ticker.add( ( time ) => lenis.raf( time * 1000 ) );
	gsap.ticker.lagSmoothing( 0 );

	// The menu overlay owns the scroll while it is open.
	document.addEventListener( 'markea:menu', ( e ) => ( e.detail.open ? lenis.stop() : lenis.start() ) );

	initButtons();
	initReveals( gsap, EASE );
	initSectionLeave( gsap, ScrollTrigger );

	const hasLoader = root.classList.contains( 'has-loader' );
	if ( hasLoader ) {
		lenis.stop();
	}

	initLoader( gsap, EASE ).then( () => {
		if ( hasLoader ) {
			lenis.start();
		}
		initHeroIntro( gsap, EASE );
		ScrollTrigger.refresh();
	} );

	root.setAttribute( 'data-motion', 'ready' );
}
