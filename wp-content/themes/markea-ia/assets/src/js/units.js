/**
 * Business units: tabs (WAI-ARIA tabs pattern) over a stack of cards, one card per unit.
 * Click, Arrow keys, Home and End select a unit. The stack is a deck: the selected card is on top (depth 0) and the
 * rest follow in tab order (depth 1, 2...). Poses per depth live in CSS ("Unidades" in main.css). With motion on,
 * the card that was on top is dealt off to the right and slips under the deck while the others move up one place.
 */
const DEAL_MS = 900; // keep in sync with the deal animation in main.css

export function initUnits() {
	document.querySelectorAll( '[data-units]' ).forEach( ( root ) => {
		const tabs = [ ...root.querySelectorAll( '[role="tab"]' ) ];
		const cards = [ ...root.querySelectorAll( '.units__card' ) ];
		if ( ! tabs.length || tabs.length !== cards.length ) {
			return;
		}

		let current = 0;
		let timer = 0;

		const stack = ( index ) => {
			cards.forEach( ( card, i ) => {
				const depth = ( i - index + cards.length ) % cards.length;
				card.dataset.depth = String( depth );
				card.toggleAttribute( 'inert', depth !== 0 );
				depth === 0 ? card.removeAttribute( 'aria-hidden' ) : card.setAttribute( 'aria-hidden', 'true' );
			} );
		};

		const deal = ( out ) => {
			window.clearTimeout( timer );
			cards.forEach( ( card ) => card.classList.remove( 'is-dealt' ) );
			void root.offsetWidth; // Restart the animation if it was still running.
			out.classList.add( 'is-dealt' );
			timer = window.setTimeout( () => out.classList.remove( 'is-dealt' ), DEAL_MS );
		};

		const select = ( index, { focus = false } = {} ) => {
			tabs.forEach( ( tab, i ) => {
				const active = i === index;
				tab.setAttribute( 'aria-selected', String( active ) );
				tab.tabIndex = active ? 0 : -1;
			} );

			const animate = index !== current && document.documentElement.getAttribute( 'data-motion' ) === 'ready';
			const out = cards[ current ];
			stack( index );
			if ( animate ) {
				deal( out );
			}
			current = index;

			if ( focus ) {
				tabs[ index ].focus();
			}
		};

		tabs.forEach( ( tab, i ) => {
			tab.addEventListener( 'click', () => select( i ) );
			tab.addEventListener( 'keydown', ( e ) => {
				const last = tabs.length - 1;
				const next = { ArrowRight: i + 1, ArrowDown: i + 1, ArrowLeft: i - 1, ArrowUp: i - 1, Home: 0, End: last }[ e.key ];
				if ( next === undefined ) {
					return;
				}
				e.preventDefault();
				select( ( next + tabs.length ) % tabs.length, { focus: true } );
			} );
		} );

		select( 0 );
	} );
}
