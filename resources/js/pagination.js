// AJAX pagination for classement
// Intercepte les clics sur les liens de pagination à l'intérieur de #classement-list
(function () {
  function attachPaginationHandlers(root = document) {
    const links = root.querySelectorAll('#classement-list a[href]');
    links.forEach(a => {
      if (a.dataset.ajaxHandled) return;
      a.dataset.ajaxHandled = '1';
      a.addEventListener('click', function (e) {
        const href = this.href;
        if (!href) return;
        // only same-origin
        try {
          const url = new URL(href, location.href);
          if (url.origin !== location.origin) return;
        } catch (err) {
          return;
        }
        e.preventDefault();
        loadPage(href, true);
      });
    });
  }

  let loadingOverlay = null;

  function showLoading() {
    if (!loadingOverlay) {
      loadingOverlay = document.createElement('div');
      loadingOverlay.id = 'pag-loading';
      loadingOverlay.style.cssText = 'position:fixed;inset:0;display:flex;align-items:center;justify-content:center;pointer-events:none;';
      loadingOverlay.innerHTML = '<div style="pointer-events:auto;background:rgba(0,0,0,0.6);padding:12px;border-radius:8px;"><svg class="animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" width="28" height="28"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg></div>';
      document.body.appendChild(loadingOverlay);
    }
    loadingOverlay.style.display = 'flex';
  }

  function hideLoading() {
    if (loadingOverlay) loadingOverlay.style.display = 'none';
  }

  async function loadPage(url, push = true) {
    try {
      showLoading();
      const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      if (!res.ok) {
        location.href = url; // fallback
        return;
      }
      const text = await res.text();
      const parser = new DOMParser();
      const doc = parser.parseFromString(text, 'text/html');
      const newList = doc.querySelector('#classement-list');
      if (!newList) {
        // no partial returned, fallback full load
        location.href = url;
        return;
      }
      const current = document.querySelector('#classement-list');
      if (!current) {
        // cannot find injection point
        location.href = url;
        return;
      }
      current.innerHTML = newList.innerHTML;

      // update title if provided
      const newTitle = doc.querySelector('title');
      if (newTitle) document.title = newTitle.textContent;

      if (push) history.pushState({}, '', url);

      // re-init Alpine on the replaced tree if available
      if (window.Alpine && typeof window.Alpine.initTree === 'function') {
        try { window.Alpine.initTree(current); } catch (e) { /* ignore */ }
      }

      attachPaginationHandlers(current);

      // accessibility: focus container
      current.setAttribute('tabindex', '-1');
      current.focus();
    } catch (err) {
      console.error('AJAX pagination failed', err);
      location.href = url;
    } finally {
      hideLoading();
    }
  }

  window.addEventListener('popstate', function () {
    loadPage(location.href, false);
  });

  document.addEventListener('DOMContentLoaded', function () {
    attachPaginationHandlers(document);
  });

})();
