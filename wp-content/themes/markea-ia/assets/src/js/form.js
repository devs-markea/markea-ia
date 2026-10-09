import { track, getAttribution } from './tracking.js';

export function initLeadForm() {
	const form = document.querySelector( '[data-lead-form]' );
	if ( ! form ) {
		return;
	}

	const status = form.querySelector( '[data-form-status]' );
	const submit = form.querySelector( '[type="submit"]' );

	const setStatus = ( message, type ) => {
		status.textContent = message;
		status.dataset.state = type;
	};

	form.addEventListener( 'submit', async ( e ) => {
		e.preventDefault();

		if ( ! form.checkValidity() ) {
			form.reportValidity();
			return;
		}

		const payload = Object.fromEntries( new FormData( form ).entries() );
		payload.page_url = window.location.href.split( '#' )[ 0 ];
		payload.attribution = getAttribution();

		submit.disabled = true;
		setStatus( '', 'idle' );

		try {
			const res = await fetch( form.dataset.endpoint, {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify( payload ),
			} );

			if ( ! res.ok ) {
				throw new Error( 'HTTP ' + res.status );
			}

			form.reset();
			setStatus( form.dataset.success || 'Gracias, te contactaremos pronto.', 'success' );
			track( 'markea_form_success', { interest: payload.interest } );
		} catch ( err ) {
			setStatus( 'No pudimos enviar el formulario. Intenta de nuevo.', 'error' );
			track( 'markea_form_error', { error: String( err.message || err ) } );
		} finally {
			submit.disabled = false;
		}
	} );
}
