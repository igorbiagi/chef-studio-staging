(function () {
	'use strict';

	function prefersReducedMotion() {
		return window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	}

	function animateReviewText(text, expand, fullText, shortText) {
		if (prefersReducedMotion() || text.getAttribute('data-animating') === '1') {
			text.textContent = expand ? fullText : shortText;
			text.classList.toggle('is-expanded', expand);
			text.classList.toggle('is-collapsed', !expand);
			return;
		}

		var startHeight = text.offsetHeight;

		text.setAttribute('data-animating', '1');
		text.style.height = startHeight + 'px';
		text.style.overflow = 'hidden';
		text.style.transition = 'height 240ms ease';

		if (expand) {
			text.classList.remove('is-collapsed');
			text.classList.add('is-expanded');
			text.textContent = fullText;
		} else {
			text.classList.remove('is-expanded');
			text.classList.add('is-collapsed');
			text.textContent = shortText;
		}

		var endHeight = text.scrollHeight;

		window.requestAnimationFrame(function () {
			text.style.height = endHeight + 'px';
		});

		function cleanup(event) {
			if (event && event.propertyName !== 'height') {
				return;
			}

			text.removeEventListener('transitionend', cleanup);
			text.style.height = '';
			text.style.overflow = '';
			text.style.transition = '';
			text.removeAttribute('data-animating');
		}

		text.addEventListener('transitionend', cleanup);
		window.setTimeout(cleanup, 320);
	}

	function initReadMore(root) {
		var cards = root.querySelectorAll('.chef-google-review-card');

		cards.forEach(function (card) {
			var text = card.querySelector('.chef-google-review-text');
			var button = card.querySelector('.chef-google-review-more');

			if (!text || !button) {
				return;
			}

			var fullText = text.getAttribute('data-full-text') || text.textContent || '';
			var shortText = text.getAttribute('data-short-text') || fullText;
			var moreLabel = button.getAttribute('data-more-label') || 'Leggi di più';
			var lessLabel = button.getAttribute('data-less-label') || 'Mostra meno';

			if (fullText === shortText) {
				return;
			}

			text.textContent = shortText;
			text.classList.add('is-collapsed');
			button.hidden = false;
			button.setAttribute('aria-expanded', 'false');

			button.addEventListener('click', function () {
				if (text.getAttribute('data-animating') === '1') {
					return;
				}

				var expanded = button.getAttribute('aria-expanded') === 'true';
				var nextExpanded = !expanded;

				button.textContent = expanded ? moreLabel : lessLabel;
				button.setAttribute('aria-expanded', expanded ? 'false' : 'true');
				animateReviewText(text, nextExpanded, fullText, shortText);
			});
		});
	}

	function getScrollAmount(track) {
		var card = track.querySelector('.chef-google-review-card');

		if (!card) {
			return Math.max(track.clientWidth * 0.85, 240);
		}

		var cardRect = card.getBoundingClientRect();
		var styles = window.getComputedStyle(track);
		var gap = parseFloat(styles.columnGap || styles.gap || '0') || 0;

		return cardRect.width + gap;
	}

	function initSlider(root) {
		if (!root.classList.contains('chef-google-reviews-layout-slider')) {
			return;
		}

		var track = root.querySelector('.chef-google-reviews-track');
		var prev = root.querySelector('.chef-google-reviews-prev');
		var next = root.querySelector('.chef-google-reviews-next');

		if (!track || !prev || !next) {
			return;
		}

		function updateButtons() {
			var maxScroll = track.scrollWidth - track.clientWidth - 2;

			prev.disabled = track.scrollLeft <= 2;
			next.disabled = track.scrollLeft >= maxScroll;
		}

		prev.addEventListener('click', function () {
			track.scrollBy({
				left: -getScrollAmount(track),
				behavior: 'smooth'
			});
		});

		next.addEventListener('click', function () {
			track.scrollBy({
				left: getScrollAmount(track),
				behavior: 'smooth'
			});
		});

		track.addEventListener('scroll', updateButtons, { passive: true });
		window.addEventListener('resize', updateButtons);
		updateButtons();
	}

	function init() {
		var roots = document.querySelectorAll('.chef-google-reviews');

		roots.forEach(function (root) {
			initReadMore(root);
			initSlider(root);
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
}());
