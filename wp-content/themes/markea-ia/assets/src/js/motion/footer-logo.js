/**
 * Footer wordmark: each letter slides in from the left inside its own clipped box, one after another,
 * scrubbed to the scroll while the footer comes into view (same idea as the reference site).
 */
export function initFooterLogo( gsap, ScrollTrigger ) {
	const wrapper = document.querySelector( '.site-footer__wordmark' );
	const letters = document.querySelectorAll( '[data-footer-letter] svg' );
	if ( ! wrapper || ! letters.length ) {
		return;
	}

	ScrollTrigger.create( {
		trigger: wrapper,
		start: 'top bottom',
		end: 'bottom bottom',
		scrub: 1.5,
		animation: gsap.from( letters, { xPercent: -100, stagger: 0.1, ease: 'none', clearProps: 'xPercent' } ),
	} );
}
