export function initNav() {
	const toggle = document.querySelector( '[data-nav-toggle]' );
	const nav = document.querySelector( '[data-nav]' );
	if ( ! toggle || ! nav ) {
		return;
	}

	const setOpen = ( open ) => {
		toggle.setAttribute( 'aria-expanded', String( open ) );
		nav.classList.toggle( 'is-open', open );
	};

	toggle.addEventListener( 'click', () => setOpen( toggle.getAttribute( 'aria-expanded' ) !== 'true' ) );
	nav.addEventListener( 'click', ( e ) => {
		if ( e.target.closest( 'a' ) ) {
			setOpen( false );
		}
	} );
	document.addEventListener( 'keydown', ( e ) => {
		if ( e.key === 'Escape' ) {
			setOpen( false );
		}
	} );
}
