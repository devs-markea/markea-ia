import '../css/grid.scss';
import '../css/main.css';
import { initTracking } from './tracking.js';
import { initMenu } from './menu.js';
import { initProjectFilters } from './filters.js';
import { initVideo } from './video.js';
import { initLeadForm } from './form.js';
import { initCarousels } from './carousel.js';
import { initHeroVisual } from './hero-visual.js';
import { initUnits } from './units.js';
import { initAccordions } from './accordion.js';

const ready = ( fn ) => {
	if ( document.readyState !== 'loading' ) {
		fn();
	} else {
		document.addEventListener( 'DOMContentLoaded', fn );
	}
};

ready( () => {
	initTracking();
	initMenu();
	initProjectFilters();
	initVideo();
	initLeadForm();
	initCarousels();
	initHeroVisual();
	initUnits();
	initAccordions();
} );
