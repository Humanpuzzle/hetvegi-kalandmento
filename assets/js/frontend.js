// Hétvégi Kalandmentő - Frontend JavaScript

(function() {
    const container = document.querySelector('.hk-container');
    if (!container) return;

    const restUrl = container.dataset.restUrl || (window.hkData && hkData.restUrl);
    const loadingEl = container.querySelector('.hk-loading');
    const errorEl = container.querySelector('.hk-error');
    const programsEl = container.querySelector('.hk-programs');
    const listEl = container.querySelector('.hk-program-list');
    const emptyEl = container.querySelector('.hk-empty');
    const filterEl = container.querySelector('#hk-difficulty-filter');

    let allPrograms = [];

    function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, c => ({
            '&': '&',
            '<': '<',
            '>': '>',
            '"': '"',
            "'": '''
        }[c]));
    }

    function formatDate(s) {
        try {
            return new Date(s).toLocaleString('hu-HU');
        } catch {
            return s;
        }
    }

    function formatPrice(p) {
        return p === 0 ? 'Ingyenes' : p.toLocaleString('hu-HU') + ' Ft';
    }

    function render() {
        const filter = filterEl ? filterEl.value : '';
        const filtered = filter ? allPrograms.filter(p => p.difficulty === filter) : allPrograms;

        loadingEl.hidden = true;
        programsEl.hidden = false;

        if (filtered.length === 0) {
            listEl.innerHTML = '';
            emptyEl.hidden = false;
            return;
        }
        emptyEl.hidden = true;

        listEl.innerHTML = filtered.map(p => `
            <li class="hk-program-card">
                <h3 class="hk-program-title">${escapeHtml(p.title)}</h3>
                <div class="hk-program-meta">
                    <span>📍 ${escapeHtml(p.location)}</span>
                    <span>🕐 ${formatDate(p.start_at)}</span>
                    <span>💰 ${formatPrice(p.price_huf)}</span>
                </div>
                <span class="hk-status hk-status-${p.status}">${escapeHtml(p.status_label)}</span>
                <button class="hk-cta" ${!p.bookable ? 'disabled' : ''}>
                    ${p.bookable ? 'Jelentkezem' : p.status_label}
                </button>
            </li>
        `).join('');
    }

    async function load() {
        try {
            const res = await fetch(restUrl);
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const data = await res.json();
            allPrograms = data.data || [];
            render();
        } catch (e) {
            loadingEl.hidden = true;
            errorEl.hidden = false;
        }
    }

    filterEl?.addEventListener('change', render);
    load();
})();