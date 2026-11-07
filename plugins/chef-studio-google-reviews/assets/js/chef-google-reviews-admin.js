(function () {
	'use strict';

	function initLogoPicker() {
		var field = document.querySelector('.csgrew-logo-field');
		var selectButton = document.querySelector('.csgrew-select-logo');
		var clearButton = document.querySelector('.csgrew-clear-logo');
		var preview = document.querySelector('.csgrew-logo-preview');
		var frame;

		if (!field || !selectButton || !window.wp || !window.wp.media) {
			return;
		}

		function setPreview(url) {
			if (!preview) {
				return;
			}

			preview.innerHTML = '';

			if (!url) {
				return;
			}

			var image = document.createElement('img');
			image.src = url;
			image.alt = '';
			image.style.height = '64px';
			image.style.width = '64px';
			image.style.objectFit = 'cover';
			image.style.border = '1px solid #dcdcde';
			image.style.borderRadius = '50%';
			image.style.background = '#fff';
			preview.appendChild(image);
		}

		selectButton.addEventListener('click', function () {
			if (frame) {
				frame.open();
				return;
			}

			frame = window.wp.media({
				title: window.csgrewAdmin && window.csgrewAdmin.mediaTitle ? window.csgrewAdmin.mediaTitle : 'Seleziona logo attivita',
				button: {
					text: window.csgrewAdmin && window.csgrewAdmin.mediaButton ? window.csgrewAdmin.mediaButton : 'Usa questo logo'
				},
				library: {
					type: 'image'
				},
				multiple: false
			});

			frame.on('select', function () {
				var attachment = frame.state().get('selection').first().toJSON();
				var previewUrl = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;

				field.value = attachment.id || attachment.url || '';
				setPreview(previewUrl || attachment.url || '');
			});

			frame.open();
		});

		if (clearButton) {
			clearButton.addEventListener('click', function () {
				field.value = '';
				setPreview('');
			});
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initLogoPicker);
	} else {
		initLogoPicker();
	}
}());
