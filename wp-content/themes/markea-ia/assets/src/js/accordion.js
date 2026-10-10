/**
 * Accordion (WAI-ARIA disclosure buttons). One item open at a time; items without text are not buttons.
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
			panel.hidden = ! open;
			item.classList.toggle( 'is-open', open );
		};

		items.forEach( ( item ) => {
			item.querySelector( '[data-accordion-trigger]' )?.addEventListener( 'click', () => {
				const willOpen = item.querySelector( '[data-accordion-trigger]' ).getAttribute( 'aria-expanded' ) !== 'true';
				items.forEach( ( other ) => set( other, other === item && willOpen ) );
			} );
		} );
	} );
}
