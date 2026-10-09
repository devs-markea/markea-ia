import '../css/grid.scss';
import '../css/main.css';
import { initTracking } from './tracking.js';
import { initNav } from './nav.js';
import { initProjectFilters } from './filters.js';
import { initVideo } from './video.js';
import { initLeadForm } from './form.js';
import { initCarousels } from './carousel.js';

const ready = ( fn ) => {
	if ( document.readyState !== 'loading' ) {
		fn();
	} else {
		document.addEventListener( 'DOMContentLoaded', fn );
	}
};

ready( () => {
	initTracking();
	initNav();
	initProjectFilters();
	initVideo();
	initLeadForm();
	initCarousels();
} );
