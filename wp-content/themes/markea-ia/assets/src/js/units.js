/**
 * Business units: tabs (WAI-ARIA tabs pattern) that swap the card shown in the stack.
 * Click, Arrow keys, Home and End select a unit; the plate behind the card takes the unit's color.
 */
export function initUnits() {
	document.querySelectorAll( '[data-units]' ).forEach( ( root ) => {
		const tabs = [ ...root.querySelectorAll( '[role="tab"]' ) ];
		const panels = [ ...root.querySelectorAll( '[role="tabpanel"]' ) ];
		const stage = root.querySelector( '.units__stage' );
		if ( ! tabs.length || tabs.length !== panels.length ) {
			return;
		}

		const select = ( index, { focus = false } = {} ) => {
			tabs.forEach( ( tab, i ) => {
				const active = i === index;
				tab.setAttribute( 'aria-selected', String( active ) );
				tab.tabIndex = active ? 0 : -1;
				panels[ i ].hidden = ! active;
			} );

			const color = tabs[ index ].dataset.color;
			if ( stage ) {
				color ? stage.style.setProperty( '--unit-plate', color ) : stage.style.removeProperty( '--unit-plate' );
			}
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
