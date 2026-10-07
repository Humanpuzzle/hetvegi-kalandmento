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

    function createProgramCard(p) {
        const li = document.createElement('li');
        li.className = 'hk-program-card';

        const title = document.createElement('h3');
        title.className = 'hk-program-title';
        title.textContent = p.title;

        const meta = document.createElement('div');
        meta.className = 'hk-program-meta';

        const location = document.createElement('span');
        location.textContent = '📍 ' + p.location;

        const timeEl = document.createElement('time');
        timeEl.dateTime = p.start_at;
        timeEl.textContent = formatDate(p.start_at);

        const price = document.createElement('span');
        price.textContent = '💰 ' + formatPrice(p.price_huf);

        meta.append(location, timeEl, price);

        const status = document.createElement('span');
        status.className = 'hk-status hk-status-' + p.status;
        status.textContent = p.status_label;

        const cta = document.createElement('button');
        cta.className = 'hk-cta';
        cta.disabled = !p.bookable;
        cta.textContent = p.bookable ? 'Jelentkezem' : p.status_label;

        li.append(title, meta, status, cta);
        return li;
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

        listEl.innerHTML = '';
        filtered.forEach(p => listEl.appendChild(createProgramCard(p)));
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