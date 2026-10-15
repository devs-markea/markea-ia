/**
 * Carousels (Swiper 11). Any element with data-swiper becomes a carousel; options come from an optional
 * data-swiper-config JSON attribute, and data-swiper-mq limits it to a media query (the markup is a normal grid
 * outside it, so the Swiper chunk is never downloaded on desktop). Markup (attributes via markea_carousel_attrs()):
 *
 * <div data-swiper data-swiper-mq="(max-width: 767.98px)">
 *   <ul><li>...</li></ul>
 * </div>
 *
 * The first child list becomes .swiper-wrapper and its items .swiper-slide only while the carousel is active.
 */
const ACTIVE = 'is-swiper';

function mount( root, Swiper, Pagination, config, reduceMotion ) {
	const list = root.firstElementChild;
	if ( ! list || list.children.length < 2 ) {
		return null;
	}

	const gutter = parseFloat( getComputedStyle( root.closest( '.container' ) || root ).paddingLeft ) || 0;
	const dots = document.createElement( 'div' );
	dots.className = 'swiper-pagination';

	root.classList.add( 'swiper', ACTIVE );
	list.classList.add( 'swiper-wrapper' );
	[ ...list.children ].forEach( ( item ) => item.classList.add( 'swiper-slide' ) );
	root.append( dots );

	const swiper = new Swiper( root, {
		modules: [ Pagination ],
		slidesPerView: 1.12,
		spaceBetween: 16,
		slidesOffsetBefore: gutter,
		slidesOffsetAfter: gutter,
		speed: reduceMotion ? 0 : 500,
		resistanceRatio: 0.6,
		pagination: { el: dots, clickable: true },
		...config,
	} );

	return () => {
		swiper.destroy( true, true );
		dots.remove();
		root.classList.remove( 'swiper', ACTIVE );
		list.classList.remove( 'swiper-wrapper' );
		[ ...list.children ].forEach( ( item ) => {
			item.classList.remove( 'swiper-slide' );
			item.removeAttribute( 'style' );
		} );
	};
}

export async function initCarousels() {
	const els = document.querySelectorAll( '[data-swiper]' );
	if ( ! els.length ) {
		return;
	}

	const queries = [ ...els ].map( ( el ) => ( {
		el,
		mq: window.matchMedia( el.dataset.swiperMq || 'all' ),
	} ) );
	if ( ! queries.some( ( { mq } ) => mq.matches ) ) {
		// Not active at load: still listen, and load Swiper only when a query starts matching.
		queries.forEach( ( { mq } ) => mq.addEventListener( 'change', () => mq.matches && start( queries ) ) );
		return;
	}
	start( queries );
}

let loading = null;

async function start( queries ) {
	loading ||= Promise.all( [
		import( 'swiper' ),
		import( 'swiper/modules' ),
		import( 'swiper/css' ),
		import( 'swiper/css/pagination' ),
	] );
	const [ { default: Swiper }, { Pagination } ] = await loading;
	const reduceMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	queries.forEach( ( { el, mq } ) => {
		if ( el.__markeaSwiper ) {
			return;
		}
		el.__markeaSwiper = true;

		let config = {};
		try {
			config = JSON.parse( el.dataset.swiperConfig || '{}' );
		} catch ( e ) {
			// Invalid JSON: fall back to defaults.
		}

		let destroy = null;
		const sync = () => {
			if ( mq.matches && ! destroy ) {
				destroy = mount( el, Swiper, Pagination, config, reduceMotion );
			} else if ( ! mq.matches && destroy ) {
				destroy();
				destroy = null;
			} else {
				return;
			}
			// The page height changed: scroll-trigger positions (motion engine) must be recomputed.
			document.dispatchEvent( new CustomEvent( 'markea:layout' ) );
		};
		mq.addEventListener( 'change', sync );
		sync();
	} );
}
