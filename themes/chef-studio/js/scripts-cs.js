document.addEventListener("DOMContentLoaded", function () {
  console.log("JS caricato correttamente.");

	

	
/////// scroll orizzontale per bottoni con pallini

const buttonRight = document.getElementById('slideRight');
const buttonLeft = document.getElementById('slideLeft');
const container = document.getElementById('container-scroll-buttons');
const content = document.getElementById('content-scroll-buttons');
const dotsContainer = document.getElementById('dots-container');

// se uno dei riferimenti non esiste, esci senza rompere il resto
if (buttonRight && buttonLeft && container && content && dotsContainer) {
  const desktopGallery = window.matchMedia('(min-width: 769px)');
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  let uiFrame = null;

  function getScrollAmount() {
    const firstItem = content.children[0];
    if (!firstItem) return container.clientWidth;

    return firstItem.getBoundingClientRect().width || container.clientWidth;
  }

  function getMaxScrollLeft() {
    return Math.max(0, container.scrollWidth - container.clientWidth);
  }

  function canScrollGallery() {
    return getMaxScrollLeft() > 1;
  }

  function setButtonState(button, disabled) {
    button.disabled = disabled;
    button.classList.toggle('is-disabled', disabled);
    button.setAttribute('aria-disabled', disabled ? 'true' : 'false');
  }

  function updateButtonStates() {
    const maxScrollLeft = getMaxScrollLeft();
    const atStart = container.scrollLeft <= 1;
    const atEnd = container.scrollLeft >= maxScrollLeft - 1;

    setButtonState(buttonLeft, !canScrollGallery() || atStart);
    setButtonState(buttonRight, !canScrollGallery() || atEnd);
  }

  function updateDots() {
    if (window.innerWidth > 768) {
      dotsContainer.innerHTML = '';
      return;
    }

    if (!content.children.length) {
      dotsContainer.innerHTML = '';
      return;
    }

    const totalItems = content.children.length;
    const itemWidth = content.children[0].offsetWidth || 1;
    const visibleItems = Math.max(1, Math.round(container.offsetWidth / itemWidth));
    const totalDots = Math.max(1, Math.ceil(totalItems / visibleItems));
    const currentIndex = Math.min(
      totalDots - 1,
      Math.max(0, Math.round(container.scrollLeft / container.offsetWidth))
    );

    dotsContainer.innerHTML = '';
    for (let i = 0; i < totalDots; i++) {
      const dot = document.createElement('span');
      dot.classList.add('dot');
      if (i === currentIndex) dot.classList.add('active');
      dotsContainer.appendChild(dot);
    }
  }

  function updateGalleryUi() {
    updateButtonStates();
    updateDots();
  }

  function scheduleGalleryUiUpdate() {
    if (uiFrame) return;

    uiFrame = window.requestAnimationFrame(function () {
      uiFrame = null;
      updateGalleryUi();
    });
  }

  container.addEventListener('scroll', scheduleGalleryUiUpdate, { passive: true });
  window.addEventListener('resize', scheduleGalleryUiUpdate);

  container.addEventListener('wheel', function (event) {
    if (!desktopGallery.matches || !canScrollGallery()) return;

    if (Math.abs(event.deltaX) <= Math.abs(event.deltaY)) return;

    let delta = event.deltaX;
    if (!delta) return;

    if (event.deltaMode === 1) delta *= 16;
    if (event.deltaMode === 2) delta *= container.clientWidth;

    const maxScrollLeft = getMaxScrollLeft();
    const nextScrollLeft = Math.min(maxScrollLeft, Math.max(0, container.scrollLeft + delta));

    if (Math.abs(nextScrollLeft - container.scrollLeft) < 1) return;

    event.preventDefault();
    container.scrollLeft = nextScrollLeft;
    scheduleGalleryUiUpdate();
  }, { passive: false });

  let isPointerDown = false;
  let hasDragged = false;
  let suppressClick = false;
  let activePointerId = null;
  let startX = 0;
  let startScrollLeft = 0;

  container.addEventListener('pointerdown', function (event) {
    if (!desktopGallery.matches || !canScrollGallery() || event.button !== 0) return;

    isPointerDown = true;
    hasDragged = false;
    activePointerId = event.pointerId;
    startX = event.clientX;
    startScrollLeft = container.scrollLeft;

    if (container.setPointerCapture) {
      container.setPointerCapture(activePointerId);
    }
  });

  container.addEventListener('pointermove', function (event) {
    if (!isPointerDown || event.pointerId !== activePointerId) return;

    const distance = event.clientX - startX;
    if (!hasDragged && Math.abs(distance) < 5) return;

    hasDragged = true;
    container.classList.add('is-dragging');
    event.preventDefault();
    container.scrollLeft = startScrollLeft - distance;
    scheduleGalleryUiUpdate();
  });

  function endPointerDrag(event) {
    if (!isPointerDown || (event && event.pointerId !== activePointerId)) return;

    isPointerDown = false;
    container.classList.remove('is-dragging');

    if (hasDragged) {
      suppressClick = true;
      window.setTimeout(function () {
        suppressClick = false;
      }, 0);
    }

    if (
      activePointerId !== null &&
      container.hasPointerCapture &&
      container.hasPointerCapture(activePointerId)
    ) {
      container.releasePointerCapture(activePointerId);
    }

    activePointerId = null;
  }

  container.addEventListener('pointerup', endPointerDrag);
  container.addEventListener('pointercancel', endPointerDrag);
  container.addEventListener('lostpointercapture', endPointerDrag);

  container.addEventListener('click', function (event) {
    if (!suppressClick) return;

    event.preventDefault();
    event.stopPropagation();
    suppressClick = false;
  }, true);

  buttonRight.onclick = function () {
    if (buttonRight.disabled) return;

    container.scrollBy({
      left: getScrollAmount(),
      behavior: prefersReducedMotion.matches ? 'auto' : 'smooth'
    });
  };

  buttonLeft.onclick = function () {
    if (buttonLeft.disabled) return;

    container.scrollBy({
      left: -getScrollAmount(),
      behavior: prefersReducedMotion.matches ? 'auto' : 'smooth'
    });
  };

  updateGalleryUi();
	}
	
	

	  
		
		
	
		
	
	// progresso caroselli orizzontali
	function chefEnsureUnifiedScrollProgress() {
	  const candidateSelector = [
	    '.contenuto-scroll [data-chef-proposta-scroll]',
	    '.contenuto-scroll .anteprime-3 > ul',
	    '.contenuto-scroll .anteprime-4 > ul',
	    '.contenuto-scroll .anteprime-6 > ul',
	    '.contenuto-scroll .anteprime-blog-grandi > ul',
	    '.contenuto-scroll .anteprime-grid > ul',
	    '.contenuto-scroll .anteprime-sezione > ul',
	    '.contenuto-scroll .servizi-4-2-premium > ul',
	    '.contenuto-scroll .categorie-h-scroll ul',
	    '.categorie-h-scroll ul'
	  ].join(', ');

	  document.querySelectorAll(candidateSelector).forEach(function (scroller) {
	    if (!scroller || scroller.nodeType !== 1 || scroller.dataset.chefPropostaProgressReady === '1') {
	      return;
	    }

	    if (scroller.children.length <= 1 && scroller.scrollWidth <= scroller.clientWidth + 1) {
	      return;
	    }

	    scroller.dataset.chefPropostaProgressReady = '1';
	    scroller.setAttribute('data-chef-proposta-scroll', '');

	    let progressBar = scroller.nextElementSibling && scroller.nextElementSibling.classList && scroller.nextElementSibling.classList.contains('chef-proposta-scroll-progress')
	      ? scroller.nextElementSibling
	      : null;

	    if (!progressBar) {
	      progressBar = document.createElement('div');
	      progressBar.setAttribute('aria-hidden', 'true');
	      scroller.insertAdjacentElement('afterend', progressBar);
	    }

	    progressBar.classList.add('chef-proposta-scroll-progress', 'chef-proposta-scroll-progress-main', 'servizi-4-2-scroll-progress', 'is-scroll-intent');

	    let progress = progressBar.querySelector('[data-chef-proposta-progress]');

	    if (!progress) {
	      progress = document.createElement('span');
	      progress.setAttribute('data-chef-proposta-progress', '');
	      progressBar.appendChild(progress);
	    }

	    if (!progress.style.getPropertyValue('--chef-proposta-progress')) {
	      progress.style.setProperty('--chef-proposta-progress', '22.00%');
	    }

	    if (!progress.style.width) {
	      progress.style.width = '22%';
	    }
	  });
	}

	chefEnsureUnifiedScrollProgress();

	const chefPropostaScrolls = document.querySelectorAll('[data-chef-proposta-scroll]');

	if (chefPropostaScrolls.length) {
	  chefPropostaScrolls.forEach(function (scroller) {
	    const nextProgress = scroller.nextElementSibling && scroller.nextElementSibling.classList.contains('chef-proposta-scroll-progress')
	      ? scroller.nextElementSibling.querySelector('[data-chef-proposta-progress]')
	      : null;
		    const layout = scroller.closest('.chef-proposta-mobile-layout');
		    const progress = nextProgress || (layout ? layout.querySelector('[data-chef-proposta-progress]') : null);
		    const progressBar = progress ? progress.closest('.chef-proposta-scroll-progress') : null;
		    const intentStopThreshold = 16;
		    const intentResetThreshold = 2;
		    let hasPassedIntentThreshold = false;
		    let propostaTicking = false;
	
		    if (!progress) return;
	
		    function updatePropostaProgress() {
		      const maxScrollLeft = Math.max(0, scroller.scrollWidth - scroller.clientWidth);
		      const ratio = maxScrollLeft > 1 ? Math.min(1, Math.max(0, scroller.scrollLeft / maxScrollLeft)) : 1;
		      const width = maxScrollLeft > 1 ? 22 + (ratio * 78) : 100;

		      if (scroller.scrollLeft > intentStopThreshold) {
		        hasPassedIntentThreshold = true;
		      } else if (scroller.scrollLeft <= intentResetThreshold) {
		        hasPassedIntentThreshold = false;
		      }
	
		      progress.style.setProperty('--chef-proposta-progress', width.toFixed(2) + '%');
		      progress.style.width = width.toFixed(2) + '%';

		      if (progressBar) {
		        progressBar.classList.toggle('is-scroll-status', hasPassedIntentThreshold);
		        progressBar.classList.toggle('is-scroll-intent', !hasPassedIntentThreshold && maxScrollLeft > 1);
		      }

		      propostaTicking = false;
		    }

	    function schedulePropostaProgress() {
	      if (propostaTicking) return;

	      propostaTicking = true;
	      window.requestAnimationFrame(updatePropostaProgress);
	    }

	    scroller.addEventListener('scroll', schedulePropostaProgress, { passive: true });
	    window.addEventListener('resize', schedulePropostaProgress);

	    updatePropostaProgress();
	    window.setTimeout(updatePropostaProgress, 250);
	  });
	}

		

		
	// barra progressione articolo
	const progressBar = document.getElementById('et-progress-bar');

if (progressBar) {
  let progressTicking = false;

  function updateProgressBar() {
    const scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
    const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    const progress = scrollHeight > 0 ? (scrollTop / scrollHeight) * 100 : 0;

    progressBar.style.width = progress + '%';
    progressTicking = false;
  }

  window.addEventListener('scroll', function () {
    if (!progressTicking) {
      window.requestAnimationFrame(updateProgressBar);
      progressTicking = true;
    }
  }, { passive: true });

  updateProgressBar();
}

	

// menu desktop: rende piu' tollerante il passaggio dalla voce padre alla tendina
const chefDesktopMenu = document.getElementById('menu-visibility');
const chefDesktopMenuRoot = chefDesktopMenu ? chefDesktopMenu.querySelector('.menu-header > ul') : null;
const chefDesktopMenuMedia = window.matchMedia('(min-width: 1250px)');
let chefDesktopSubmenuTimer = null;
let chefDesktopActiveItem = null;

function chefDesktopDirectSubmenu(item) {
  return Array.prototype.find.call(item.children, function (child) {
    return child.matches && child.matches('ul.sub-menu');
  }) || null;
}

function chefCloseDesktopSubmenu(item) {
  if (!item) return;

  item.classList.remove('chef-desktop-submenu-open');

  if (chefDesktopActiveItem === item) {
    chefDesktopActiveItem = null;
  }
}

function chefOpenDesktopSubmenu(item) {
  if (!chefDesktopMenuMedia.matches || !item) return;

  window.clearTimeout(chefDesktopSubmenuTimer);

  if (chefDesktopActiveItem && chefDesktopActiveItem !== item) {
    chefCloseDesktopSubmenu(chefDesktopActiveItem);
  }

  item.classList.add('chef-desktop-submenu-open');
  chefDesktopActiveItem = item;
}

function chefScheduleCloseDesktopSubmenu(item) {
  window.clearTimeout(chefDesktopSubmenuTimer);

  chefDesktopSubmenuTimer = window.setTimeout(function () {
    const submenu = chefDesktopDirectSubmenu(item);
    const isStillActive = item.matches(':hover') ||
      item.contains(document.activeElement) ||
      (submenu && submenu.matches(':hover'));

    if (!isStillActive) {
      chefCloseDesktopSubmenu(item);
    }
  }, 180);
}

function chefCloseAllDesktopSubmenus() {
  window.clearTimeout(chefDesktopSubmenuTimer);

  if (!chefDesktopMenuRoot) return;

  chefDesktopMenuRoot.querySelectorAll('.chef-desktop-submenu-open').forEach(function (item) {
    item.classList.remove('chef-desktop-submenu-open');
  });

  chefDesktopActiveItem = null;
}

if (chefDesktopMenuRoot) {
  Array.prototype.slice.call(chefDesktopMenuRoot.children).forEach(function (item) {
    if (!item.matches || !item.matches('li.has-children, li.menu-item-has-children')) return;

    const submenu = chefDesktopDirectSubmenu(item);

    if (!submenu) return;

    item.addEventListener('pointerenter', function () {
      chefOpenDesktopSubmenu(item);
    });

    item.addEventListener('pointerleave', function () {
      chefScheduleCloseDesktopSubmenu(item);
    });

    submenu.addEventListener('pointerenter', function () {
      chefOpenDesktopSubmenu(item);
    });

    submenu.addEventListener('pointerleave', function () {
      chefScheduleCloseDesktopSubmenu(item);
    });

    item.addEventListener('focusin', function () {
      chefOpenDesktopSubmenu(item);
    });

    item.addEventListener('focusout', function () {
      window.setTimeout(function () {
        if (!item.contains(document.activeElement)) {
          chefScheduleCloseDesktopSubmenu(item);
        }
      }, 0);
    });
  });

  if (chefDesktopMenuMedia.addEventListener) {
    chefDesktopMenuMedia.addEventListener('change', function (event) {
      if (!event.matches) {
        chefCloseAllDesktopSubmenus();
      }
    });
  } else if (chefDesktopMenuMedia.addListener) {
    chefDesktopMenuMedia.addListener(function (event) {
      if (!event.matches) {
        chefCloseAllDesktopSubmenus();
      }
    });
  }
}




	
// menu mobile a pannelli
const hamburger = document.getElementById('hamburger');
const chefMobileMenu = document.getElementById('menu-visibility');
const chefMobileMenuRoot = chefMobileMenu ? chefMobileMenu.querySelector('.menu-header > ul') : null;
const chefMobileMenuHeadingBack = chefMobileMenu ? chefMobileMenu.querySelector('.chef-mobile-menu-heading-back') : null;
const chefMobileMenuHeadingLabel = chefMobileMenu ? chefMobileMenu.querySelector('.chef-mobile-menu-heading-label') : null;

if (hamburger) {
  hamburger.addEventListener('change', function () {
    document.body.classList.toggle('menu-open', this.checked);

    if (!this.checked) {
      document.body.classList.remove('chef-mobile-menu-submenu-open');
    }

    if (this.checked && typeof window.chefResetMobileMenu === 'function') {
      window.chefResetMobileMenu();
    }
  });
}

if (hamburger && chefMobileMenu && chefMobileMenuRoot) {
  document.body.classList.add('chef-mobile-menu-enhanced');

  const chefMobileMenuContact = chefMobileMenu.querySelector(':scope > .chef-mobile-menu-contact');

  if (chefMobileMenuContact && !chefMobileMenuRoot.querySelector(':scope > .chef-mobile-menu-contact-item')) {
    const contactItem = document.createElement('li');
    contactItem.className = 'chef-mobile-menu-contact-item';
    contactItem.appendChild(chefMobileMenuContact);
    chefMobileMenuRoot.appendChild(contactItem);
  }

  const chefMobilePanels = Array.prototype.slice.call(chefMobileMenu.querySelectorAll('.menu-header ul'));
  let chefActiveMobilePanel = chefMobileMenuRoot;
  let chefTouchStartX = 0;
  let chefTouchStartY = 0;
  let chefTouchStartTarget = null;

  function chefDirectSubmenu(item) {
    return Array.prototype.find.call(item.children, function (child) {
      return child.matches && child.matches('ul.sub-menu');
    }) || null;
  }

  function chefPanelParent(panel) {
    const parentItem = panel.parentElement && panel.parentElement.closest('li');
    return parentItem ? parentItem.parentElement : null;
  }

  function chefUsefulHref(anchor) {
    if (!anchor) return '';

    const href = (anchor.getAttribute('href') || '').trim();
    return href && href !== '#' ? href : '';
  }

  Array.prototype.slice.call(chefMobileMenuRoot.children).forEach(function (item) {
    if (!item.matches || !item.matches('li')) return;

    const link = item.querySelector(':scope > a');
    const linkText = link ? link.textContent.trim().toLowerCase() : '';
    const linkHref = link ? (link.getAttribute('href') || '').toLowerCase() : '';

    if (linkText.indexOf('contatt') !== -1 || linkHref.indexOf('contatt') !== -1) {
      item.classList.add('chef-mobile-contact-menu-item');
    }
  });

  function chefSetMobilePanel(panel) {
    if (!panel) return;

    chefMobilePanels.forEach(function (candidate) {
      candidate.classList.remove('chef-mobile-panel-active', 'chef-mobile-panel-parent');
      candidate.setAttribute('aria-hidden', 'true');
    });

    panel.classList.add('chef-mobile-panel-active');
    panel.setAttribute('aria-hidden', 'false');
    chefActiveMobilePanel = panel;

    let parent = chefPanelParent(panel);
    while (parent && parent.matches && parent.matches('ul')) {
      parent.classList.add('chef-mobile-panel-parent');
      parent = chefPanelParent(parent);
    }

    chefMobileMenu.querySelectorAll('.chef-mobile-submenu-toggle').forEach(function (button) {
      const item = button.closest('li');
      const submenu = item ? chefDirectSubmenu(item) : null;
      button.setAttribute('aria-expanded', submenu && submenu.contains(panel) ? 'true' : 'false');
    });

    const isRootPanel = panel === chefMobileMenuRoot;
    chefMobileMenu.classList.toggle('chef-mobile-menu-is-submenu', !isRootPanel);
    document.body.classList.toggle('chef-mobile-menu-submenu-open', !isRootPanel);

    if (chefMobileMenuHeadingLabel) {
      chefMobileMenuHeadingLabel.textContent = isRootPanel ? 'Menu' : 'Indietro';
    }
  }

  function chefGoBackMobileMenu() {
    if (chefActiveMobilePanel === chefMobileMenuRoot) {
      hamburger.checked = false;
      hamburger.dispatchEvent(new Event('change'));
      return;
    }

    chefSetMobilePanel(chefPanelParent(chefActiveMobilePanel) || chefMobileMenuRoot);
  }

  chefMobilePanels.forEach(function (panel, panelIndex) {
    panel.classList.add('chef-mobile-panel');
    panel.setAttribute('aria-hidden', panel === chefMobileMenuRoot ? 'false' : 'true');

    if (panel !== chefMobileMenuRoot && !panel.querySelector(':scope > .chef-mobile-submenu-all')) {
      const parentItem = panel.parentElement && panel.parentElement.closest('li');
      const parentLink = parentItem ? parentItem.querySelector(':scope > a') : null;
      const parentHref = chefUsefulHref(parentLink);
      const parentTitle = parentLink ? parentLink.textContent.trim() : '';

      if (parentHref) {
        const allItem = document.createElement('li');
        allItem.className = 'chef-mobile-submenu-all';

        const allLink = document.createElement('a');
        allLink.href = parentHref;
        allLink.textContent = parentTitle || 'Vedi tutti';

        allItem.appendChild(allLink);
        panel.insertBefore(allItem, panel.firstChild);
      }
    }

    panel.dataset.chefMobilePanel = String(panelIndex);
  });

  chefMobileMenu.querySelectorAll('.menu-header li').forEach(function (item) {
    const submenu = chefDirectSubmenu(item);
    const link = item.querySelector(':scope > a');

    if (!submenu || !link || item.querySelector(':scope > .chef-mobile-submenu-toggle')) {
      return;
    }

    const button = document.createElement('button');
    const submenuTitle = link.textContent.trim() || 'Sotto-menu';

    button.type = 'button';
    button.className = 'chef-mobile-submenu-toggle';
    button.setAttribute('aria-expanded', 'false');
    button.setAttribute('aria-label', 'Apri ' + submenuTitle);
    button.addEventListener('click', function () {
      chefSetMobilePanel(submenu);
    });

    link.addEventListener('click', function (event) {
      if (!hamburger.checked || !window.matchMedia('(max-width: 1249px)').matches) return;

      event.preventDefault();
      chefSetMobilePanel(submenu);
    });

    link.insertAdjacentElement('afterend', button);
  });

  window.chefResetMobileMenu = function () {
    chefSetMobilePanel(chefMobileMenuRoot);
  };

  chefSetMobilePanel(chefMobileMenuRoot);

  if (chefMobileMenuHeadingBack) {
    chefMobileMenuHeadingBack.addEventListener('click', chefGoBackMobileMenu);
  }

  document.addEventListener('keydown', function (event) {
    if (!hamburger.checked || event.key !== 'Escape') return;
    chefGoBackMobileMenu();
  });

  chefMobileMenu.addEventListener('touchstart', function (event) {
    if (!event.touches || event.touches.length !== 1) return;

    chefTouchStartX = event.touches[0].clientX;
    chefTouchStartY = event.touches[0].clientY;
    chefTouchStartTarget = event.target;
  }, { passive: true });

  chefMobileMenu.addEventListener('touchend', function (event) {
    if (!event.changedTouches || event.changedTouches.length !== 1) return;

    const deltaX = event.changedTouches[0].clientX - chefTouchStartX;
    const deltaY = event.changedTouches[0].clientY - chefTouchStartY;

    if (Math.abs(deltaY) > 60 || Math.abs(deltaX) < 62) return;

    if (deltaX > 0) {
      chefGoBackMobileMenu();
      return;
    }

    const targetItem = chefTouchStartTarget && chefTouchStartTarget.closest
      ? chefTouchStartTarget.closest('li.has-children, li.menu-item-has-children')
      : null;
    const targetToggle = targetItem ? targetItem.querySelector(':scope > .chef-mobile-submenu-toggle') : null;

    if (targetToggle && targetItem.parentElement === chefActiveMobilePanel) {
      targetToggle.click();
    }
  }, { passive: true });
}

// Gravity Form 5 puo essere usato sia nella hero sia nella CTA finale.
// In quel caso Gravity stampa ID duplicati: qui manteniamo la copia CTA stabile dopo i render.
const chefCtaFormFiveActiveContextKey = 'chefCtaFormFiveActiveContext';

function chefSetCtaFormFiveActiveContext(context) {
  try {
    window.sessionStorage.setItem(chefCtaFormFiveActiveContextKey, context);
  } catch (error) {
    // sessionStorage puo essere non disponibile in modalita privacy.
  }
}

function chefGetCtaFormFiveActiveContext() {
  try {
    return window.sessionStorage.getItem(chefCtaFormFiveActiveContextKey) || '';
  } catch (error) {
    return '';
  }
}

function chefClearCtaFormFiveActiveContext() {
  try {
    window.sessionStorage.removeItem(chefCtaFormFiveActiveContextKey);
  } catch (error) {
    // sessionStorage puo essere non disponibile in modalita privacy.
  }
}

function chefFindCtaFormFiveScrollTarget() {
  const root = document.querySelector('.chef-cta-form-section .cta-form-gravity-form-5');

  if (!root) {
    return null;
  }

  return root.closest('.local-seo-form-card') || root.closest('.chef-cta-form-section') || root;
}

function chefClearCtaFormFiveHash() {
  if (chefGetCtaFormFiveActiveContext() !== 'cta') {
    return;
  }

  const formHashes = ['#form-ancora', '#form-anchor', '#gf_5'];

  if (formHashes.indexOf(window.location.hash) === -1 || !window.history || !window.history.replaceState) {
    return;
  }

  window.history.replaceState(null, document.title, window.location.pathname + window.location.search);
}

function chefGetCtaFormFivePageState(root) {
  const form = root.querySelector('#gform_5');

  if (!form) {
    return null;
  }

  const pages = Array.prototype.slice.call(form.querySelectorAll('.gform_page'));
  const visiblePage = pages.find(function (page) {
    return window.getComputedStyle(page).display !== 'none';
  });
  let currentPage = 1;

  if (visiblePage) {
    const match = visiblePage.id.match(/_(\d+)$/);
    currentPage = match ? parseInt(match[1], 10) : 1;
  }

  const pageCountNode = form.querySelector('.gf_step_page_count');
  let totalPages = pageCountNode ? parseInt(pageCountNode.textContent, 10) : pages.length;

  if (!totalPages || totalPages < currentPage) {
    totalPages = Math.max(currentPage, 2);
  }

  if (totalPages > 2) {
    totalPages = 2;
  }

  currentPage = Math.min(currentPage, totalPages);

  return {
    currentPage: currentPage,
    totalPages: totalPages,
    percent: Math.round((currentPage / totalPages) * 100)
  };
}

function chefStripCtaFormFiveActionHash(form) {
  if (!form || !form.action) {
    return;
  }

  const nextAction = form.action.replace(/#.*$/, '');

  if (form.action !== nextAction) {
    form.action = nextAction;
  }
}

function chefScrollToCtaFormFive() {
  if (chefGetCtaFormFiveActiveContext() !== 'cta') {
    return;
  }

  chefClearCtaFormFiveHash();

  const target = chefFindCtaFormFiveScrollTarget();

  if (!target) {
    return;
  }

  const offset = window.matchMedia && window.matchMedia('(max-width: 980px)').matches ? 78 : 96;
  const top = target.getBoundingClientRect().top + window.pageYOffset - offset;

  window.scrollTo({
    top: Math.max(0, top),
    behavior: 'auto'
  });
}

function chefScheduleCtaFormFiveScroll() {
  if (chefGetCtaFormFiveActiveContext() !== 'cta') {
    return;
  }

  [80, 260, 700, 1300].forEach(function (delay) {
    window.setTimeout(chefScrollToCtaFormFive, delay);
  });

  window.setTimeout(chefClearCtaFormFiveActiveContext, 1700);
}

function chefMarkCtaFormFiveInteraction(target) {
  if (!target || !target.closest) {
    return;
  }

  if (target.closest('.chef-cta-form-section .cta-form-gravity-form-5')) {
    chefSetCtaFormFiveActiveContext('cta');
    return;
  }

  if (target.closest('.hero-landing-ads-form-card .hero-landing-ads-gravity-form')) {
    chefSetCtaFormFiveActiveContext('hero');
  }
}

function chefSyncCtaFormFive() {
  const roots = document.querySelectorAll('.chef-cta-form-section .cta-form-gravity-form-5');

  roots.forEach(function (root) {
    const wrapper = root.querySelector('#gform_wrapper_5');
    const form = root.querySelector('#gform_5');
    const offlineCheckbox = root.querySelector('#field_5_17 input[type="checkbox"]');
    const offlineLabel = root.querySelector('#field_5_17 .gform-field-label--type-inline');
    const offlineFieldLabel = root.querySelector('#field_5_17 > legend, #field_5_17 > .gfield_label, #field_5_17 > .gfield_label_before_complex');
    const projectType = root.querySelector('#field_5_20, #field_5_15');

    if (wrapper) {
      wrapper.style.display = 'block';
    }

    if (form) {
      form.style.opacity = '';
      chefStripCtaFormFiveActionHash(form);
    }

    if (offlineLabel && offlineLabel.textContent.trim() === 'Il progetto non è ancora online.') {
      offlineLabel.textContent = 'Non ho ancora il sito online';
    }

    if (offlineFieldLabel) {
      offlineFieldLabel.style.display = 'none';
    }

    if (offlineCheckbox && projectType) {
      projectType.style.display = offlineCheckbox.checked ? '' : 'none';
    }

    const pageState = chefGetCtaFormFivePageState(root);

    if (pageState) {
      const card = root.closest('.local-seo-form-card');
      const stepBadge = card ? card.querySelector('[data-cta-form-step]') : null;
      const progressBar = root.querySelector('.gf_progressbar_percentage');
      const stepLabel = pageState.currentPage + ' di ' + pageState.totalPages;

      if (stepBadge && stepBadge.textContent.trim() !== stepLabel) {
        stepBadge.textContent = stepLabel;
      }

      if (progressBar) {
        const width = pageState.percent + '%';
        const className = (progressBar.className.replace(/\bpercentbar_\d+\b/g, '').trim() + ' percentbar_' + pageState.percent).trim();

        if (progressBar.style.width !== width) {
          progressBar.style.width = width;
        }

        if (progressBar.className !== className) {
          progressBar.className = className;
        }
      }
    }

    if (root.dataset.chefCtaFormFiveReady === '1') {
      return;
    }

    root.dataset.chefCtaFormFiveReady = '1';

    root.querySelectorAll('label[for]').forEach(function (label) {
      const originalId = label.getAttribute('for');
      const target = originalId ? root.querySelector('[id="' + originalId + '"]') : null;

      if (!target || target.dataset.chefCtaOriginalId) {
        return;
      }

      const uniqueId = originalId + '_cta';
      target.dataset.chefCtaOriginalId = originalId;
      target.id = uniqueId;
      label.setAttribute('for', uniqueId);
    });
  });
}

function chefBindCtaFormFive() {
  if (!document.querySelector('.chef-cta-form-section .cta-form-gravity-form-5')) {
    return;
  }

  chefSyncCtaFormFive();
  chefScheduleCtaFormFiveScroll();

  document.addEventListener('click', function (event) {
    const control = event.target && event.target.closest
      ? event.target.closest('button, input[type="submit"], input[type="button"]')
      : null;

    if (!control) {
      return;
    }

    chefMarkCtaFormFiveInteraction(control);
  }, true);

  document.addEventListener('submit', function (event) {
    chefMarkCtaFormFiveInteraction(event.target);
    chefStripCtaFormFiveActionHash(event.target);
  }, true);

  document.addEventListener('change', function (event) {
    if (!event.target || !event.target.closest('.chef-cta-form-section .cta-form-gravity-form-5')) {
      return;
    }

    window.requestAnimationFrame(chefSyncCtaFormFive);
  }, true);

  document.addEventListener('gform/post_render', function () {
    chefSyncCtaFormFive();
    chefScheduleCtaFormFiveScroll();
  });

  document.addEventListener('gform_post_render', function () {
    chefSyncCtaFormFive();
    chefScheduleCtaFormFiveScroll();
  });

  window.addEventListener('load', function () {
    chefSyncCtaFormFive();
    chefScheduleCtaFormFiveScroll();
  });

  if (window.jQuery) {
    window.jQuery(document).on('gform_page_loaded gform_post_render gform_post_conditional_logic', function () {
      chefSyncCtaFormFive();
      chefScheduleCtaFormFiveScroll();
    });
  }

  window.setTimeout(function () {
    chefSyncCtaFormFive();
    chefScheduleCtaFormFiveScroll();
  }, 300);

  window.setTimeout(function () {
    chefSyncCtaFormFive();
    chefScheduleCtaFormFiveScroll();
  }, 900);
}

chefBindCtaFormFive();

function chefBindCtaFormStableScroll(formId) {
  const formIdString = String(formId);
  const rootSelectors = [
    '.chef-cta-form-section .cta-form-gravity-form-' + formIdString,
    '.hero-landing-ads-form-card .hero-landing-ads-gravity-form-' + formIdString
  ];
  const rootSelector = rootSelectors.join(', ');
  const controlSelector = rootSelectors.map(function (selector) {
    return [
      selector + ' .gform_next_button',
      selector + ' .gform_previous_button',
      selector + ' .gform_button',
      selector + ' input[type="submit"]',
      selector + ' button[type="submit"]'
    ].join(', ');
  }).join(', ');

  if (!document.querySelector(rootSelector)) {
    return;
  }

  let pendingScrollY = null;
  let pendingUntil = 0;

  function hasActiveCtaFormTarget(target) {
    return !!(target && target.closest && target.closest(rootSelector));
  }

  function captureScrollPosition(target) {
    if (!hasActiveCtaFormTarget(target)) {
      return;
    }

    pendingScrollY = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
    pendingUntil = Date.now() + 13000;
    scheduleScrollRestore();
  }

  function cancelScrollGuard() {
    pendingScrollY = null;
    pendingUntil = 0;
  }

  function restoreScrollPosition() {
    if (pendingScrollY === null || Date.now() > pendingUntil) {
      cancelScrollGuard();
      return;
    }

    const currentScrollY = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;

    if (Math.abs(currentScrollY - pendingScrollY) > 1) {
      const previousScrollBehavior = document.documentElement.style.scrollBehavior;
      document.documentElement.style.scrollBehavior = 'auto';
      document.documentElement.scrollTop = pendingScrollY;
      document.body.scrollTop = pendingScrollY;
      document.documentElement.style.scrollBehavior = previousScrollBehavior;
    }
  }

  function scheduleScrollRestore() {
    [0, 40, 120, 260, 520, 900, 1400, 2200, 3200, 4500, 6500, 9000, 11500].forEach(function (delay) {
      window.setTimeout(restoreScrollPosition, delay);
    });
  }

  window.addEventListener('scroll', restoreScrollPosition, { passive: true });

  ['wheel', 'touchstart', 'pointerdown'].forEach(function (eventName) {
    window.addEventListener(eventName, cancelScrollGuard, { passive: true });
  });

  window.addEventListener('keydown', function (event) {
    if (['ArrowUp', 'ArrowDown', 'PageUp', 'PageDown', 'Home', 'End', ' '].indexOf(event.key) !== -1) {
      cancelScrollGuard();
    }
  });

  window['gform_confirmation_anchor_' + formIdString] = false;

  document.addEventListener('click', function (event) {
    const control = event.target && event.target.closest
      ? event.target.closest(controlSelector)
      : null;

    if (control) {
      captureScrollPosition(control);
    }
  }, true);

  document.addEventListener('submit', function (event) {
    if (event.target && event.target.matches && event.target.matches('#gform_' + formIdString)) {
      captureScrollPosition(event.target);
    }
  }, true);

  document.addEventListener('gform/post_render', function (event) {
    if (event.detail && event.detail.formId && String(event.detail.formId) !== formIdString) {
      return;
    }

    scheduleScrollRestore();
  });

  document.addEventListener('gform_post_render', scheduleScrollRestore);

  if (window.jQuery) {
    window.jQuery(document).on('gform_page_loaded gform_post_render gform_confirmation_loaded gform_post_conditional_logic', function (event, formId) {
      if (formId && String(formId) !== formIdString) {
        return;
      }

      scheduleScrollRestore();
    });
  }
}

// Lo scroll automatico dei form viene disabilitato lato PHP tramite il filtro
// gform_confirmation_anchor. Il precedente blocco temporizzato non viene
// inizializzato perché ostacolava lo scroll volontario durante richieste lente.
	
	
	
	
	
	
	
	
	



	/*
	// parallasse hero disattivato
	const heroImages = document.querySelectorAll('.hero-img');

	if (heroImages.length && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
	  let ticking = false;

	  function clamp(value, min, max) {
	    return Math.min(Math.max(value, min), max);
	  }

	  function updateParallax() {
	    const viewportH = window.innerHeight;

	    heroImages.forEach(function (img) {
	      const rect = img.getBoundingClientRect();

	      if (rect.bottom < 0 || rect.top > viewportH) return;

	      const progress = (viewportH - rect.top) / (viewportH + rect.height);
	      const normalized = clamp((progress - 0.5) * 2, -1, 1);
	      const moveY = normalized * 60;

	      img.style.transform = 'translate3d(0, ' + moveY.toFixed(2) + 'px, 0)';
	    });

	    ticking = false;
	  }

	  function requestParallaxTick() {
	    if (!ticking) {
	      window.requestAnimationFrame(updateParallax);
	      ticking = true;
	    }
	  }

	  updateParallax();

	  window.addEventListener('scroll', requestParallaxTick, { passive: true });
	  window.addEventListener('resize', requestParallaxTick);
	}
	*/


	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
// blocchi codice negli articoli
const articleCodeBlocks = document.querySelectorAll('.articolo pre, .articolo code > textarea');

function cleanLegacyCodeBlock(text) {
  return (text || '')
    .replace(/\r\n/g, '\n')
    .replace(/(?:&lt;br\s*\/?&gt;\s*)+/gi, '\n')
    .replace(/(?:<br\s*\/?>\s*)+/gi, '\n')
    .replace(/&lt;\/p&gt;/gi, '\n')
    .replace(/&lt;p&gt;/gi, '')
    .replace(/<\/p>/gi, '\n')
    .replace(/<p>/gi, '')
    .replace(/&nbsp;/gi, ' ')
    .replace(/\n[ \t]+\n/g, '\n\n')
    .replace(/\n{3,}/g, '\n\n')
    .trim();
}

function inferCodeLanguage(text) {
  if (/^(?:Redirect(?:Match)?|Rewrite(?:Engine|Cond|Rule)|Options|ErrorDocument)\b/im.test(text)) {
    return 'HTACCESS';
  }

  if (/@media\b|(?:^|\n)\s*[.#]?[a-z0-9_-]+(?:\s+[.#]?[a-z0-9_-]+)*\s*\{/i.test(text)) {
    return 'CSS';
  }

  if (/<[a-z][\s\S]*>/i.test(text)) {
    return 'HTML';
  }

  return 'CODICE';
}

function createArticleCodeBlock(source, codeText, code, pre, insertTarget, removeInsertTarget) {
  if (!insertTarget || !insertTarget.parentNode) return;

  const wrapper = document.createElement('div');
  wrapper.className = 'chef-code-block';

  const header = document.createElement('div');
  header.className = 'chef-code-block-header';

  const language = (
    source.dataset.language ||
    code.dataset.language ||
    ((code.className || '').match(/(?:language|lang)-([a-z0-9-]+)/i) || [])[1] ||
    inferCodeLanguage(codeText)
  ).replace('-', ' ').toUpperCase();

  const label = document.createElement('span');
  label.className = 'chef-code-language';
  label.textContent = language;

  const copyButton = document.createElement('button');
  copyButton.type = 'button';
  copyButton.className = 'chef-code-copy';
  copyButton.textContent = 'Copia codice';

  copyButton.addEventListener('click', function () {
    const text = code.textContent || '';

    function showCopied() {
      copyButton.textContent = 'Copiato';
      window.setTimeout(function () {
        copyButton.textContent = 'Copia codice';
      }, 1800);
    }

    if (navigator.clipboard && window.isSecureContext) {
      navigator.clipboard.writeText(text).then(showCopied).catch(function () {});
      return;
    }

    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.setAttribute('readonly', '');
    textarea.style.position = 'fixed';
    textarea.style.left = '-9999px';
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand('copy');
    textarea.remove();
    showCopied();
  });

  header.appendChild(label);
  header.appendChild(copyButton);

  insertTarget.parentNode.insertBefore(wrapper, insertTarget);
  wrapper.appendChild(header);
  wrapper.appendChild(pre);

  if (removeInsertTarget) {
    insertTarget.remove();
  }
}

articleCodeBlocks.forEach(function (source) {
  if (source.closest('.chef-code-block')) return;

  const isTextarea = source.tagName.toLowerCase() === 'textarea';
  const originalCode = isTextarea ? source.value : (source.querySelector('code') || source).textContent;
  const codeText = isTextarea ? cleanLegacyCodeBlock(originalCode) : originalCode.trim();
  const code = isTextarea ? document.createElement('code') : (source.querySelector('code') || source);
  const pre = isTextarea ? document.createElement('pre') : source;
  const insertTarget = isTextarea ? (source.closest('code') || source) : source;

  if (!codeText) return;

  if (isTextarea) {
    code.textContent = codeText;
    pre.appendChild(code);
  }

  createArticleCodeBlock(source, codeText, code, pre, insertTarget, isTextarea);
});

document.querySelectorAll('.articolo code').forEach(function (source) {
  if (source.closest('pre, .chef-code-block') || source.querySelector('textarea')) return;

  const codeText = cleanLegacyCodeBlock(source.textContent).trim();
  const parent = source.parentElement;

  if (!codeText || !parent) return;

  const isOnlyCodeInParent = parent.textContent.trim() === codeText && parent.querySelectorAll('code').length === 1;
  const isDirectContentCode = parent.classList.contains('contenuto-standard') || parent.classList.contains('articolo');
  const looksLikeBlockCode = /^(?:Redirect(?:Match)?|Rewrite(?:Engine|Cond|Rule)|Options|ErrorDocument)\b|<[a-z][\s\S]*>|@media\b|(?:^|\n)\s*[.#]?[a-z0-9_-]+(?:\s+[.#]?[a-z0-9_-]+)*\s*\{/i.test(codeText) || codeText.length > 70;

  if (!looksLikeBlockCode || (!isOnlyCodeInParent && !isDirectContentCode)) return;

  const code = document.createElement('code');
  const pre = document.createElement('pre');
  const insertTarget = isOnlyCodeInParent && parent.tagName.toLowerCase() === 'p' ? parent : source;

  code.textContent = codeText;
  pre.appendChild(code);
  createArticleCodeBlock(source, codeText, code, pre, insertTarget, true);
});

}); 

/////// chat dimostrativa (pagine siti, sezione .rsw-phone): i messaggi compaiono uno alla volta quando la sezione è visibile
(function () {
  var feed = document.querySelector('.rsw-phone .rsw-feed');
  if (!feed || !('IntersectionObserver' in window)) return;
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  var msgs = Array.prototype.slice.call(feed.querySelectorAll('.rsw-msg'));
  msgs.forEach(function (m) { m.hidden = true; });
  var dots = document.createElement('span');
  dots.className = 'rsw-dots';
  dots.innerHTML = '<b></b><b></b><b></b>';
  function done() {
    var phone = feed.closest('.rsw-phone');
    var titolo = phone.hasAttribute('data-ok-titolo') ? phone.getAttribute('data-ok-titolo') : 'Cliente servito, ordine chiuso';
    var testo = phone.hasAttribute('data-ok-testo') ? phone.getAttribute('data-ok-testo') : 'Ha risposto l\'assistente AI, in pochi secondi. Anche di notte.';
    if (!titolo && !testo) return;
    var ok = document.createElement('div');
    ok.className = 'rsw-ok';
    var i = document.createElement('i'); i.textContent = '✓'; ok.appendChild(i);
    if (titolo) { var b = document.createElement('b'); b.textContent = titolo; ok.appendChild(b); }
    if (testo) { var sp = document.createElement('span'); sp.textContent = testo; ok.appendChild(sp); }
    phone.appendChild(ok);
  }
  function play(i) {
    if (i >= msgs.length) { setTimeout(done, 900); return; }
    var m = msgs[i];
    var bot = m.classList.contains('rsw-bot');
    if (bot) {
      feed.appendChild(dots);
      dots.hidden = false;
    }
    setTimeout(function () {
      dots.hidden = true;
      m.hidden = false;
      feed.appendChild(m);
      setTimeout(function () { play(i + 1); }, bot ? 1500 : 700);
    }, bot ? 1200 : 0);
  }
  var io = new IntersectionObserver(function (entries) {
    if (entries[0].isIntersecting) { io.disconnect(); setTimeout(function () { play(0); }, 400); }
  }, { threshold: 0.4 });
  io.observe(feed);
})();

/* Ingrandimento immagini nella pagina (link con classe rsw-cwv-img o rsw-zoom): si apre sopra la pagina, non in una nuova scheda */
document.addEventListener('click', function (e) {
  var a = e.target.closest ? e.target.closest('a.rsw-cwv-img, a.rsw-zoom') : null;
  if (!a || typeof HTMLDialogElement === 'undefined') return;
  e.preventDefault();
  // griglie con due sole schermate: sono già grandi, il link non fa niente
  var g = a.closest('.rsw-cwv');
  if (g && g.querySelectorAll(':scope > .rsw-cwv-c').length === 2) return;
  // su smartphone le schermate si vedono già grandi: niente finestra
  if (window.matchMedia('(max-width: 768px)').matches) return;
  var d = document.getElementById('rsw-zoom-dialog');
  if (!d) {
    d = document.createElement('dialog');
    d.id = 'rsw-zoom-dialog';
    d.innerHTML = '<button type="button" class="rsw-zoom-chiudi" aria-label="Chiudi">×</button><img alt="">';
    document.body.appendChild(d);
    d.addEventListener('click', function (ev) { if (ev.target === d || ev.target.classList.contains('rsw-zoom-chiudi')) d.close(); });
  }
  var img = a.querySelector('img');
  var big = d.querySelector('img');
  // svuota l'immagine precedente e mostra la nuova solo quando è caricata
  big.classList.add('carica');
  big.onload = function () { big.classList.remove('carica'); };
  big.removeAttribute('src');
  big.alt = img ? img.alt : '';
  d.showModal();
  big.src = a.getAttribute('href');
  if (big.complete && big.naturalWidth) big.classList.remove('carica');
});

// Pagine servizio: percorso a tappe, schede con numeri e mappa si accendono quando entrano nello schermo.
// Solo transform e opacity (niente CLS); con "riduci animazioni" restano ferme e già accese.
(function () {
  // Si osserva ogni scheda da sola: su telefono sono in colonna e si accendono una alla volta mentre scorri
  var el = document.querySelectorAll('.rsw-tl, .rsw-cwv-c, .rsw-mappa-c, .rsw-mappa-n, .rsw-mappa-giu, .rsw-bar-r');
  if (!el.length || !('IntersectionObserver' in window)) return;
  if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  document.documentElement.classList.add('js-rsw');
  // Numeri delle schede: la parte numerica sale da 0 al valore (es. +50%, 3 su 4)
  function conta(b) {
    var t = b.textContent, m = t.match(/\d+([.,]\d+)?/);
    if (!m) return;
    var fine = parseFloat(m[0].replace(',', '.')), dec = m[1] ? m[1].length - 1 : 0;
    if (fine < 3) return;
    var prima = t.slice(0, m.index), dopo = t.slice(m.index + m[0].length), t0 = null, durata = 1400;
    function passo(ts) {
      if (t0 === null) t0 = ts;
      var k = Math.min(1, (ts - t0) / durata), v = fine * (1 - Math.pow(1 - k, 3));
      b.textContent = prima + v.toFixed(dec).replace('.', ',') + dopo;
      if (k < 1) requestAnimationFrame(passo); else b.textContent = t;
    }
    requestAnimationFrame(passo);
  }
  var io = new IntersectionObserver(function (voci) {
    voci.forEach(function (v) {
      if (!v.isIntersecting) return;
      v.target.classList.add('rsw-vivo');
      var f = v.target.nextElementSibling;
      if (f && f.classList.contains('rsw-tl-fine')) f.classList.add('rsw-vivo');
      if (v.target.classList.contains('rsw-cwv-c')) { var n = v.target.querySelector(':scope > b'); if (n) setTimeout(function () { conta(n); }, 200); }
      io.unobserve(v.target);
    });
  }, { threshold: 0.3, rootMargin: '0px 0px -8% 0px' });
  el.forEach(function (e) { io.observe(e); });
})();

// Velocità: lo scroll-snap dei caroselli si accende solo al primo tocco, rotella o tasto
// (se è attivo dal primo disegno Chrome smette di misurare l'LCP).
(function () {
  var html = document.documentElement;
  function accendi() {
    html.classList.add('chef-snap');
    ['pointerdown', 'touchstart', 'wheel', 'keydown'].forEach(function (ev) {
      window.removeEventListener(ev, accendi, true);
    });
  }
  ['pointerdown', 'touchstart', 'wheel', 'keydown'].forEach(function (ev) {
    window.addEventListener(ev, accendi, { passive: true, capture: true });
  });
})();

// Home (layout hero_percorso, percorsi_obiettivi, percorso_cliente): animazioni leggere.
// Solo classi e testo; con prefers-reduced-motion tutto resta fermo e visibile.
(function () {
  var hero = document.querySelector('.hp-viaggio');
  var schede = document.querySelectorAll('.hp-percorsi');
  var tappe = document.querySelectorAll('.hp-vc, .hp-rv');
  if (!hero && !schede.length && !tappe.length) return;
  document.documentElement.classList.add('hp-js');
  var fermo = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;

  // Hero: la ricerca si scrive da sola, poi si accendono pagina e richiesta (ciclo di 9 secondi).
  if (hero) {
    var q = hero.querySelector('.hp-q');
    var testo = q ? q.getAttribute('data-testo') || '' : '';
    // Più ricerche (data-ricerche): girano in ordine, partendo da una a caso.
    var lista = [], n = 0;
    try { lista = JSON.parse(hero.getAttribute('data-ricerche') || '[]'); } catch (e) { lista = []; }
    if (lista.length > 1) n = Math.floor(Math.random() * lista.length);
    var aiT = hero.querySelector('.hp-ai-t'), notT = hero.querySelector('.hp-not-t');
    var prossima = function () {
      if (lista.length < 2) return;
      var r = lista[n % lista.length]; n++;
      testo = r.q; if (aiT) aiT.innerHTML = r.r; if (notT) notT.textContent = r.n;
    };
    var t = hero.querySelectorAll('.hp-tappa');
    var punto = hero.querySelector('.hp-filo i');
    var accendi = function (n) {
      for (var i = 0; i < t.length; i++) t[i].classList.toggle('hp-on', i === n);
      // il puntino scende alla tappa accesa e si illumina quando arriva
      hero.setAttribute('data-passo', n);
      if (punto) { punto.classList.remove('hp-luce'); setTimeout(function () { punto.classList.add('hp-luce'); }, 750); }
    };
    if (fermo) {
      for (var i = 0; i < t.length; i++) t[i].classList.add('hp-on');
    } else if (q && t.length === 3) {
      var timer = [], visibile = true;
      var ciclo = function () {
        timer.forEach(clearTimeout); timer = [];
        if (!visibile) return;
        prossima(); accendi(0); q.textContent = '';
        for (var c = 1; c <= testo.length; c++) {
          (function (c) { timer.push(setTimeout(function () { q.textContent = testo.slice(0, c); }, c * 55)); })(c);
        }
        timer.push(setTimeout(function () { accendi(1); }, 3000));
        timer.push(setTimeout(function () { accendi(2); }, 6000));
        timer.push(setTimeout(ciclo, 9000));
      };
      // Il ciclo gira solo quando la hero è visibile.
      if ('IntersectionObserver' in window) {
        new IntersectionObserver(function (es) {
          var ora = es[0].isIntersecting;
          if (ora && !visibile) { visibile = true; ciclo(); }
          visibile = ora;
        }).observe(hero);
      }
      ciclo();
    }
  }

  // Percorsi: schede con tastiera (frecce) e clic.
  schede.forEach(function (box) {
    var tab = box.querySelectorAll('.hp-tab');
    var mostra = function (b, fuoco) {
      tab.forEach(function (x) { x.setAttribute('aria-selected', x === b ? 'true' : 'false'); x.tabIndex = x === b ? 0 : -1; });
      box.querySelectorAll('.hp-pane').forEach(function (p) { p.classList.toggle('hp-on', p.id === b.getAttribute('aria-controls')); });
      if (fuoco) b.focus();
    };
    // Su telefono le schede diventano una fisarmonica: ogni pannello si apre sotto la sua scheda.
    var tabs = box.querySelector('.hp-tabs'), pannello = box.querySelector('.hp-pannello');
    var mq = window.matchMedia ? matchMedia('(max-width: 700px)') : null;
    var disponi = function () {
      var fis = !!(mq && mq.matches);
      box.classList.toggle('hp-fis', fis);
      tab.forEach(function (b) {
        var p = document.getElementById(b.getAttribute('aria-controls'));
        if (!p) return;
        if (fis) b.after(p); else pannello.appendChild(p);
      });
    };
    if (mq && tabs && pannello) { disponi(); if (mq.addEventListener) mq.addEventListener('change', disponi); }
    tab.forEach(function (b, i) {
      b.addEventListener('click', function () {
        if (box.classList.contains('hp-fis')) setTimeout(function () {
          var y = b.getBoundingClientRect().top;
          if (y < 70 || y > window.innerHeight * 0.6) window.scrollBy({ top: y - 80, behavior: fermo ? 'auto' : 'smooth' });
        }, 30);
      });
      b.tabIndex = i === 0 ? 0 : -1;
      b.addEventListener('click', function () { mostra(b); });
      b.addEventListener('keydown', function (e) {
        var d = e.key === 'ArrowDown' || e.key === 'ArrowRight' ? 1 : e.key === 'ArrowUp' || e.key === 'ArrowLeft' ? -1 : 0;
        if (d) { e.preventDefault(); mostra(tab[(i + d + tab.length) % tab.length], true); }
      });
    });
  });

  // Tappe del cliente e comparse allo scroll.
  var apri = function (el) {
    if (el.classList.contains('hp-vc')) {
      el.style.setProperty('--hp-p', 1);
      Array.prototype.forEach.call(el.children, function (li, i) { setTimeout(function () { li.classList.add('hp-on'); }, fermo ? 0 : 250 + i * 300); });
    } else el.classList.add('hp-in');
  };
  if (!('IntersectionObserver' in window)) { tappe.forEach(apri); return; }
  var io = new IntersectionObserver(function (es) {
    es.forEach(function (e) { if (e.isIntersecting) { apri(e.target); io.unobserve(e.target); } });
  }, { threshold: 0.2 });
  tappe.forEach(function (el) { io.observe(el); });
})();
