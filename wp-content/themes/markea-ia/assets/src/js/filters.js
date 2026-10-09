import { track } from './tracking.js';

export function initProjectFilters() {
	const root = document.querySelector( '[data-projects]' );
	if ( ! root ) {
		return;
	}

	const buttons = root.querySelectorAll( '[data-filter]' );
	const items = root.querySelectorAll( '[data-categories]' );

	buttons.forEach( ( btn ) => {
		btn.addEventListener( 'click', () => {
			const filter = btn.dataset.filter;

			buttons.forEach( ( b ) => {
				const active = b === btn;
				b.classList.toggle( 'is-active', active );
				b.setAttribute( 'aria-pressed', String( active ) );
			} );

			items.forEach( ( item ) => {
				const match = filter === '*' || item.dataset.categories.split( ' ' ).includes( filter );
				item.hidden = ! match;
			} );

			track( 'markea_project_filter', { filter } );
		} );
	} );
}
