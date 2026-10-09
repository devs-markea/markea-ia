/**
 * Hero visual: a Spline scene (<spline-viewer>) or an iframe, loaded after the page is idle so it never
 * blocks first paint. The URL lives in a data attribute until then.
 *
 * Spline viewer: loaded from Spline's CDN (pinned version) and the "Built with Spline" badge can be hidden
 * (data-hide-badge="1"). Check Spline's plan terms before hiding it in production.
 */
const SPLINE_VIEWER_SRC = 'https://cdn.spline.design/@splinetool/viewer@2.0.75/build/spline-viewer.js';

export function initHeroVisual() {
	const root = document.querySelector( '[data-hero-visual]' );
	if ( ! root ) {
		return;
	}

	const viewer = root.querySelector( 'spline-viewer[data-spline-url]' );
	const frame = root.querySelector( 'iframe[data-src]' );
	if ( ! viewer && ! frame ) {
		return;
	}

	const start = () => ( viewer ? loadSpline( root, viewer ) : loadFrame( root, frame ) );
	const schedule = () => ( 'requestIdleCallback' in window ? window.requestIdleCallback( start, { timeout: 2000 } ) : window.setTimeout( start, 300 ) );

	if ( document.readyState === 'complete' ) {
		schedule();
	} else {
		window.addEventListener( 'load', schedule, { once: true } );
	}
}

function loadFrame( root, frame ) {
	frame.addEventListener( 'load', () => root.classList.add( 'is-loaded' ), { once: true } );
	frame.src = frame.dataset.src;
}

async function loadSpline( root, viewer ) {
	try {
		await import( /* @vite-ignore */ SPLINE_VIEWER_SRC );
		await customElements.whenDefined( 'spline-viewer' );
	} catch ( e ) {
		return; // CDN blocked or offline: the circle stays empty.
	}

	if ( viewer.dataset.hideBadge === '1' ) {
		hideSplineBadge( viewer );
	}

	viewer.addEventListener( 'load', () => root.classList.add( 'is-loaded' ), { once: true } );
	viewer.setAttribute( 'url', viewer.dataset.splineUrl );
}

/**
 * The badge lives in the viewer's open shadow root (<a id="logo">) and is re-created while loading,
 * so remove it now and again whenever the shadow tree changes.
 */
function hideSplineBadge( viewer ) {
	const root = viewer.shadowRoot;
	if ( ! root ) {
		return;
	}

	const remove = () => root.querySelector( '#logo' )?.remove();
	remove();
	new MutationObserver( remove ).observe( root, { childList: true, subtree: true } );
}
