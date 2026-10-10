/**
 * Business units: tabs (WAI-ARIA tabs pattern) that swap the card shown in the stack.
 * Click, Arrow keys, Home and End select a unit; each unit is a set (colored plate + card) that is dealt as one piece.
 * With motion on, the cards behave like a deck: the top card is dealt off to one side while fading, and the next
 * one rises from underneath (classes is-leaving / is-entering, see "Unidades" in main.css). Otherwise it is a plain swap.
 */
const DEAL_MS = 800; // keep in sync with the card transition in main.css
export function initUnits() {
	document.querySelectorAll( '[data-units]' ).forEach( ( root ) => {
		const tabs = [ ...root.querySelectorAll( '[role="tab"]' ) ];
		const panels = [ ...root.querySelectorAll( '[role="tabpanel"]' ) ];
		if ( ! tabs.length || tabs.length !== panels.length ) {
			return;
		}

		let current = 0;
		let leaving = null;
		let timer = 0;

		const finishDeal = () => {
			window.clearTimeout( timer );
			if ( leaving ) {
				leaving.hidden = true;
				leaving.classList.remove( 'is-leaving' );
				leaving = null;
			}
		};

		const showPanel = ( index ) => {
			finishDeal();

			if ( index === current || document.documentElement.getAttribute( 'data-motion' ) !== 'ready' ) {
				panels.forEach( ( panel, i ) => {
					panel.hidden = i !== index;
				} );
				return;
			}

			const out = panels[ current ];
			const incoming = panels[ index ];

			// The old card leaves toward the side it came from in the tab order; the next one rises from underneath.
			root.style.setProperty( '--deal-dir', index > current ? '-1' : '1' );
			panels.forEach( ( panel, i ) => {
				panel.hidden = panel !== out && i !== index;
			} );
			incoming.classList.add( 'is-entering' );
			void incoming.offsetWidth; // Commit the "under the deck" pose before releasing it.
			incoming.classList.remove( 'is-entering' );
			out.classList.add( 'is-leaving' );

			leaving = out;
			timer = window.setTimeout( finishDeal, DEAL_MS );
		};

		const select = ( index, { focus = false } = {} ) => {
			tabs.forEach( ( tab, i ) => {
				const active = i === index;
				tab.setAttribute( 'aria-selected', String( active ) );
				tab.tabIndex = active ? 0 : -1;
			} );
			showPanel( index );
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
