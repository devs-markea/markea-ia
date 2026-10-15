/**
 * Mobile sticky CTA: shown once the hero is out of view, hidden again over the contact form and footer
 * (where the primary action is already on screen).
 */
export function initStickyCta() {
	const bar = document.querySelector( '.sticky-cta' );
	const hero = document.getElementById( 'inicio' );
	if ( ! bar || ! hero || ! ( 'IntersectionObserver' in window ) ) {
		return;
	}

	const state = { hero: true, end: false };
	const update = () => {
		const show = ! state.hero && ! state.end;
		bar.hidden = false;
		bar.classList.toggle( 'is-visible', show );
		bar.toggleAttribute( 'inert', ! show );
	};

	const watch = ( el, key ) => {
		if ( ! el ) {
			return;
		}
		new IntersectionObserver( ( [ entry ] ) => {
			state[ key ] = entry.isIntersecting;
			update();
		} ).observe( el );
	};

	watch( hero, 'hero' );
	watch( document.getElementById( 'contacto' ), 'end' );
	update();
}
