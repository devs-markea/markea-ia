/**
 * Primary buttons and filter pills: a circle grows from the point where the pointer enters and shrinks toward where it leaves.
 * Pointer devices only; touch keeps the plain hover color.
 */
export function initButtons() {
	if ( ! window.matchMedia( '(hover: hover) and (pointer: fine)' ).matches ) {
		return;
	}

	document.querySelectorAll( '.btn--primary, .filters__btn' ).forEach( ( button ) => {
		const circle = document.createElement( 'span' );
		circle.className = 'btn__circle';
		circle.setAttribute( 'aria-hidden', 'true' );
		button.prepend( circle );
		button.classList.add( 'btn--fx' );

		const place = ( e ) => {
			const rect = button.getBoundingClientRect();
			const offCenter = Math.abs( ( ( e.clientX - ( rect.left + rect.width / 2 ) ) / ( rect.width / 2 ) ) * 50 );
			circle.style.left = ( ( ( e.clientX - rect.left ) / rect.width ) * 100 ).toFixed( 1 ) + '%';
			circle.style.top = ( ( ( e.clientY - rect.top ) / rect.height ) * 100 ).toFixed( 1 ) + '%';
			circle.style.width = 200 + offCenter * 2 + '%';
		};

		button.addEventListener( 'mouseenter', place );
		button.addEventListener( 'mouseleave', place );
	} );
}
