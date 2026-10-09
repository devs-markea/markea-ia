import { track } from './tracking.js';

export function initVideo() {
	document.querySelectorAll( '[data-video]' ).forEach( ( box ) => {
		const play = box.querySelector( '[data-video-play]' );
		const tpl = box.querySelector( 'template[data-video-embed]' );
		if ( ! play || ! tpl ) {
			return;
		}

		play.addEventListener( 'click', () => {
			const frag = tpl.content.cloneNode( true );
			const iframe = frag.querySelector( 'iframe' );
			if ( iframe ) {
				const url = new URL( iframe.src, window.location.href );
				url.searchParams.set( 'autoplay', '1' );
				iframe.src = url.toString();
				iframe.allow = 'autoplay; fullscreen; picture-in-picture';
			}
			box.replaceChildren( frag );
			box.classList.add( 'is-playing' );
			track( 'markea_video_play', { video: 'diagnostico' } );
		}, { once: true } );
	} );
}
