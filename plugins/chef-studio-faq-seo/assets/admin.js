( function () {
	'use strict';

	function onReady( callback ) {
		if ( document.readyState === 'loading' ) {
			document.addEventListener( 'DOMContentLoaded', callback );
			return;
		}

		callback();
	}

	onReady( function () {
		if ( ! window.csfaqAdmin ) {
			return;
		}

		var importField = document.querySelector( '[data-name="' + window.csfaqAdmin.importFieldName + '"]' );
		var importTextarea = importField ? importField.querySelector( 'textarea' ) : null;
		var modeField = document.querySelector( '[data-name="' + window.csfaqAdmin.importModeFieldName + '"]' );
		var hintId = 'csfaq-import-hint';

		function updateImportState() {
			if ( ! importField || ! importTextarea ) {
				return;
			}

			importField.classList.toggle( 'csfaq-has-import-data', importTextarea.value.trim().length > 0 );
		}

		function getModeValue() {
			if ( ! modeField ) {
				return '';
			}

			var checkedRadio = modeField.querySelector( 'input[type="radio"]:checked' );

			if ( checkedRadio ) {
				return checkedRadio.value;
			}

			var selectField = modeField.querySelector( 'select' );

			return selectField ? selectField.value : '';
		}

		function updateModeHint() {
			if ( ! modeField ) {
				return;
			}

			var hint = document.getElementById( hintId );

			if ( ! hint ) {
				hint = document.createElement( 'p' );
				hint.id = hintId;
				hint.className = 'description csfaq-import-hint';
				modeField.appendChild( hint );
			}

			hint.textContent = getModeValue() === 'replace' ? window.csfaqAdmin.replaceHint : window.csfaqAdmin.appendHint;
		}

		if ( importTextarea ) {
			importTextarea.addEventListener( 'input', updateImportState );
			updateImportState();
		}

		if ( modeField ) {
			modeField.addEventListener( 'change', updateModeHint );
			updateModeHint();
		}

		document.querySelectorAll( '[data-csfaq-copy-target]' ).forEach( function ( button ) {
			button.addEventListener( 'click', function () {
				var targetId = button.getAttribute( 'data-csfaq-copy-target' );
				var target = targetId ? document.getElementById( targetId ) : null;
				var feedback = button.parentNode ? button.parentNode.querySelector( '.csfaq-copy-feedback' ) : null;

				if ( ! target ) {
					return;
				}

				target.focus();
				target.select();

				if ( navigator.clipboard && window.isSecureContext ) {
					navigator.clipboard.writeText( target.value ).then(
						function () {
							if ( feedback ) {
								feedback.textContent = window.csfaqAdmin.copySuccess;
							}
						},
						function () {
							if ( feedback ) {
								feedback.textContent = window.csfaqAdmin.copyFailure;
							}
						}
					);
					return;
				}

				if ( feedback ) {
					feedback.textContent = window.csfaqAdmin.copyFailure;
				}
			} );
		} );
	} );
}() );
