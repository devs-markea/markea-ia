/**
 * Menu overlay (disclosure + modal behavior).
 * - Toggle button: aria-expanded, label swap, icon swap (CSS).
 * - Open: page content becomes inert, scrolling is locked, focus stays on the button's flow.
 * - Close: Escape, click outside the card, click on a link, or the toggle; focus returns to the toggle.
 * - Scroll spy: marks the menu link of the section in view with aria-current.
 */
export function initMenu() {
	const toggle = document.querySelector( '[data-menu-toggle]' );
	const menu = document.querySelector( '[data-menu]' );
	if ( ! toggle || ! menu ) {
		return;
	}

	const label = toggle.querySelector( '[data-menu-label]' );
	const root = document.documentElement;
	// Everything behind the overlay except the header (which holds the toggle).
	const behind = () => [ document.getElementById( 'contenido' ), document.querySelector( '.site-footer' ) ].filter( Boolean );

	const setOpen = ( open, { restoreFocus = true } = {} ) => {
		toggle.setAttribute( 'aria-expanded', String( open ) );
		menu.classList.toggle( 'is-open', open );
		root.classList.toggle( 'is-menu-open', open );
		document.dispatchEvent( new CustomEvent( 'markea:menu', { detail: { open } } ) );
		if ( label ) {
			label.textContent = open ? toggle.dataset.labelClose : toggle.dataset.labelOpen;
		}
		behind().forEach( ( el ) => ( open ? el.setAttribute( 'inert', '' ) : el.removeAttribute( 'inert' ) ) );

		if ( open ) {
			menu.querySelector( 'a, button' )?.focus( { preventScroll: true } );
		} else if ( restoreFocus ) {
			toggle.focus( { preventScroll: true } );
		}
	};

	// Stagger index for the entrance transition (see Motion in main.css).
	menu.querySelectorAll( '.site-menu__intro > *, .site-menu__list li, .site-menu__footer > *' ).forEach( ( el, i ) => el.style.setProperty( '--i', String( i ) ) );

	const isOpen = () => menu.classList.contains( 'is-open' );

	toggle.addEventListener( 'click', () => setOpen( ! isOpen() ) );

	document.addEventListener( 'keydown', ( e ) => {
		if ( e.key === 'Escape' && isOpen() ) {
			setOpen( false );
		}
	} );

	menu.addEventListener( 'click', ( e ) => {
		const onBackdrop = e.target === menu;
		const onLink = e.target.closest( 'a[href]' );
		if ( onBackdrop || onLink ) {
			// A link to an anchor on this page scrolls after the overlay closes.
			setOpen( false, { restoreFocus: onBackdrop } );
		}
	} );

	initScrollSpy( menu );
}

function initScrollSpy( menu ) {
	if ( ! ( 'IntersectionObserver' in window ) ) {
		return;
	}

	const links = [ ...menu.querySelectorAll( '.site-menu__list a[href*="#"]' ) ];
	const byId = new Map();
	links.forEach( ( a ) => {
		const id = new URL( a.href, window.location.href ).hash.slice( 1 );
		const section = id && document.getElementById( id );
		if ( section ) {
			byId.set( section, a );
		}
	} );
	if ( ! byId.size ) {
		return;
	}

	const observer = new IntersectionObserver(
		( entries ) => {
			entries.forEach( ( entry ) => {
				if ( entry.isIntersecting ) {
					links.forEach( ( a ) => a.removeAttribute( 'aria-current' ) );
					byId.get( entry.target ).setAttribute( 'aria-current', 'true' );
				}
			} );
		},
		{ rootMargin: '-40% 0px -55% 0px' }
	);
	byId.forEach( ( _a, section ) => observer.observe( section ) );
}
