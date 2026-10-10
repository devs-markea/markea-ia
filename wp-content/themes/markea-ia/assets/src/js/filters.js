import { track } from './tracking.js';

const LEAVE_MS = 600; // keep in sync with the leave transition in "Motion" (main.css)

/**
 * Category pills. With motion on, the visible cards shrink and fade out (600 ms), the list swaps, and the
 * matching cards ease back in (800 ms), as on the reference site. Without motion (reduced motion, switched off,
 * or the motion script not ready) the swap is instant.
 */
export function initProjectFilters() {
	const root = document.querySelector( '[data-projects]' );
	if ( ! root ) {
		return;
	}

	const buttons = root.querySelectorAll( '[data-filter]' );
	const items = [ ...root.querySelectorAll( '[data-categories]' ) ];
	let current = '*';
	let pending = null;
	let busy = false;

	const matches = ( item, filter ) => filter === '*' || item.dataset.categories.split( ' ' ).includes( filter );
	const animated = () => document.documentElement.getAttribute( 'data-motion' ) === 'ready';

	const swap = ( filter ) => {
		items.forEach( ( item ) => {
			item.hidden = ! matches( item, filter );
		} );
	};

	const enter = ( filter ) => {
		const shown = items.filter( ( item ) => matches( item, filter ) );

		// Start from the "out" pose without a transition, then let it ease to rest.
		shown.forEach( ( item ) => {
			item.hidden = false;
			item.classList.add( 'is-leaving', 'is-instant' );
		} );
		items.filter( ( item ) => ! shown.includes( item ) ).forEach( ( item ) => {
			item.hidden = true;
			item.classList.remove( 'is-leaving' );
		} );
		void root.offsetWidth; // Commit the start pose.
		shown.forEach( ( item ) => item.classList.remove( 'is-leaving', 'is-instant' ) );
	};

	const apply = ( filter ) => {
		current = filter;

		if ( ! animated() ) {
			swap( filter );
			return;
		}

		busy = true;
		items.filter( ( item ) => ! item.hidden ).forEach( ( item ) => item.classList.add( 'is-leaving' ) );

		window.setTimeout( () => {
			enter( filter );
			busy = false;

			// A pill clicked during the leave: go straight to the latest choice.
			if ( pending !== null && pending !== current ) {
				const next = pending;
				pending = null;
				apply( next );
			}
			pending = null;
		}, LEAVE_MS );
	};

	buttons.forEach( ( btn ) => {
		btn.addEventListener( 'click', () => {
			const filter = btn.dataset.filter;

			buttons.forEach( ( b ) => {
				const active = b === btn;
				b.classList.toggle( 'is-active', active );
				b.setAttribute( 'aria-pressed', String( active ) );
			} );

			track( 'markea_project_filter', { filter } );

			if ( busy ) {
				pending = filter;
			} else if ( filter !== current ) {
				apply( filter );
			}
		} );
	} );
}
