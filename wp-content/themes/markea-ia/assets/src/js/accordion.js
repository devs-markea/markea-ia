/**
 * Accordion (WAI-ARIA disclosure buttons). One item open at a time; items without text are not buttons.
 * Opening and closing animate in CSS (.relevance__panel); this only toggles hidden around the transition.
 * Markup: [data-accordion] > [data-accordion-item] > h3 > button[data-accordion-trigger][aria-controls] + panel[hidden].
 */
export function initAccordions() {
	document.querySelectorAll( '[data-accordion]' ).forEach( ( root ) => {
		const items = [ ...root.querySelectorAll( '[data-accordion-item]' ) ];

		const set = ( item, open ) => {
			const trigger = item.querySelector( '[data-accordion-trigger]' );
			const panel = trigger && document.getElementById( trigger.getAttribute( 'aria-controls' ) );
			if ( ! trigger || ! panel ) {
				return;
			}
			trigger.setAttribute( 'aria-expanded', String( open ) );
			if ( open === item.classList.contains( 'is-open' ) ) {
				return;
			}

			// The panel animates its height (grid rows 0fr -> 1fr, see main.css), so it must be rendered while it
			// moves: unhide before opening, hide after the close transition ends (never mid-way).
			clearTimeout( panel._hideTimer );
			if ( open ) {
				panel.hidden = false;
				void panel.offsetHeight; // Start the transition from the collapsed state.
				item.classList.add( 'is-open' );
				return;
			}

			item.classList.remove( 'is-open' );
			const still = parseFloat( getComputedStyle( panel ).transitionDuration ) === 0;
			panel._hideTimer = setTimeout( () => {
				panel.hidden = ! item.classList.contains( 'is-open' );
			}, still ? 0 : 600 );
		};

		items.forEach( ( item ) => {
			item.querySelector( '[data-accordion-trigger]' )?.addEventListener( 'click', () => {
				const willOpen = item.querySelector( '[data-accordion-trigger]' ).getAttribute( 'aria-expanded' ) !== 'true';
				items.forEach( ( other ) => set( other, other === item && willOpen ) );
			} );
		} );
	} );
}
