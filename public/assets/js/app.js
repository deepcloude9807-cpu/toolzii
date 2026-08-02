/* ToolzyNet — front-end interactions (AJAX search, newsletter, filters, theme) */
(function () {
    'use strict';
    const BASE = window.TOOLZY_BASE || '';
    const CSRF = window.CSRF || '';

    /* ---------- Theme toggle ---------- */
    const themeToggle = document.getElementById('themeToggle');
    const root = document.documentElement;
    const stored = localStorage.getItem('toolzy-theme');
    if (stored) root.setAttribute('data-bs-theme', stored);
    if (themeToggle) {
        themeToggle.addEventListener('click', function () {
            const next = root.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
            root.setAttribute('data-bs-theme', next);
            localStorage.setItem('toolzy-theme', next);
        });
    }

    /* ---------- AJAX autocomplete ---------- */
    const search = document.getElementById('globalSearch');
    const panel = document.getElementById('searchSuggest');
    let debounce;
    if (search && panel) {
        search.addEventListener('input', function () {
            const term = this.value.trim();
            clearTimeout(debounce);
            if (term.length < 2) { panel.style.display = 'none'; return; }
            debounce = setTimeout(function () {
                fetch(BASE + '/api/search?q=' + encodeURIComponent(term), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(r => r.json())
                    .then(data => {
                        if (!data.results || !data.results.length) { panel.style.display = 'none'; return; }
                        panel.innerHTML = data.results.map(r =>
                            '<a href="' + r.url + '"><i class="fa-solid fa-magnifying-glass me-2 text-muted"></i>' + escapeHtml(r.name) + '</a>'
                        ).join('');
                        panel.style.display = 'block';
                    }).catch(() => { panel.style.display = 'none'; });
            }, 220);
        });
        document.addEventListener('click', function (e) {
            if (!panel.contains(e.target) && e.target !== search) panel.style.display = 'none';
        });
    }

    /* ---------- Newsletter (AJAX) ---------- */
    function bindNewsletter(formId, msgId) {
        const form = document.getElementById(formId);
        if (!form) return;
        const msg = document.getElementById(msgId);
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const email = form.querySelector('[name=email]').value;
            fetch(BASE + '/newsletter/subscribe', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': CSRF },
                body: 'email=' + encodeURIComponent(email) + '&_csrf=' + encodeURIComponent(CSRF)
            }).then(r => r.json()).then(data => {
                if (msg) { msg.textContent = data.message; msg.className = 'form-text ' + (data.ok ? 'text-success' : 'text-danger'); }
                if (data.ok) form.reset();
            }).catch(() => { if (msg) msg.textContent = 'Something went wrong.'; });
        });
    }
    bindNewsletter('newsletterForm', 'newsletterMsg');
    bindNewsletter('newsletterFormBig', 'newsletterMsgBig');

    /* ---------- AJAX category filter ---------- */
    const filterForm = document.getElementById('filterForm');
    const grid = document.getElementById('productGrid');
    if (filterForm && grid) {
        filterForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const params = new URLSearchParams(new FormData(filterForm));
            params.set('category_id', filterForm.dataset.category || '');
            grid.style.opacity = '.4';
            fetch(BASE + '/api/products/filter?' + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(data => {
                    grid.innerHTML = data.html || '<div class="col-12"><div class="empty-state"><p>No products found.</p></div></div>';
                    const count = document.getElementById('resultCount');
                    if (count) count.textContent = data.total + ' products';
                    grid.style.opacity = '1';
                }).catch(() => { grid.style.opacity = '1'; });
        });
    }

    function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    }
})();
