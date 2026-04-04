const PAGE_SIZE = 50;
const SEARCH_DEBOUNCE_MS = 200;
const SCROLL_LOAD_THRESHOLD_PX = 24;

/** @type {Set<HTMLElement>} */
const openRoots = new Set();
let globalDocumentListenersBound = false;
let globalIdSeq = 0;

function ensureGlobalDocumentListeners() {
    if (globalDocumentListenersBound) {
        return;
    }
    globalDocumentListenersBound = true;

    document.addEventListener(
        'pointerdown',
        e => {
            if (openRoots.size === 0) {
                return;
            }
            for (const root of [...openRoots]) {
                const fn = root._enhancedSelectCloseIfOutside;
                if (typeof fn === 'function') {
                    fn(e);
                }
            }
        },
        true
    );

    document.addEventListener('keydown', e => {
        if (e.key !== 'Escape' || openRoots.size === 0) {
            return;
        }
        for (const root of [...openRoots]) {
            const fn = root._enhancedSelectEscape;
            if (typeof fn === 'function') {
                fn(e);
            }
        }
    });
}

/**
 * @param {HTMLSelectElement} select
 * @returns {boolean}
 */
function shouldEnhance(select) {
    if (select.dataset.enhancedSelectInit === '1') {
        return false;
    }
    if (!select.classList.contains('js-enhanced-select')) {
        return false;
    }
    if (select.name === 'validation') {
        return false;
    }
    if (select.id === 'adminLocale' || select.id === 'publicLocale') {
        return false;
    }
    if (select.classList.contains('locale-nav-select-input')) {
        return false;
    }
    if (select.classList.contains('admin-search-column') || select.classList.contains('admin-search-boolean')) {
        return false;
    }
    if (select.multiple) {
        return false;
    }
    if (select.hasAttribute('data-select-native')) {
        return false;
    }
    return true;
}

/**
 * @param {HTMLSelectElement} selectEl
 * @returns {string}
 */
function toggleClassFromSelect(selectEl) {
    const parts = selectEl.className.split(/\s+/).filter(c => {
        return c && c !== 'js-enhanced-select' && c !== 'visually-hidden' && c !== 'is-invalid';
    });
    parts.push('enhanced-select-toggle', 'text-start', 'd-flex', 'align-items-center', 'w-100');
    return parts.join(' ');
}

/**
 * @param {HTMLSelectElement} select
 * @returns {{ value: string, label: string, disabled: boolean }[]}
 */
function normalizeOptions(select) {
    return Array.from(select.options).map(opt => ({
        value: opt.value,
        label: opt.textContent.replace(/\s+/g, ' ').trim(),
        disabled: opt.disabled,
    }));
}

/**
 * @param {{ value: string, label: string, disabled: boolean }[]} options
 * @param {string} query
 */
function filterByQuery(options, query) {
    const q = query.trim().toLowerCase();
    if (!q) {
        return options.slice();
    }
    return options.filter(o => {
        return o.label.toLowerCase().includes(q) || String(o.value).toLowerCase().includes(q);
    });
}

/**
 * @param {HTMLSelectElement} select
 */
function enhanceSelect(select) {
    if (!shouldEnhance(select)) {
        return;
    }

    const parent = select.parentNode;
    if (!parent) {
        return;
    }

    const searchPlaceholder = select.getAttribute('data-enhanced-search-placeholder') || 'Search';
    const noResultsText = select.getAttribute('data-enhanced-no-results') || 'No results';

    const root = document.createElement('div');
    root.className = 'enhanced-select-root';

    parent.insertBefore(root, select);
    root.appendChild(select);

    const feedbackEl =
        root.nextElementSibling?.classList?.contains('invalid-feedback') === true ? root.nextElementSibling : null;

    select.classList.add('visually-hidden');
    select.tabIndex = -1;
    select.setAttribute('aria-hidden', 'true');

    const toggle = document.createElement('button');
    toggle.type = 'button';
    toggle.className = toggleClassFromSelect(select);
    toggle.classList.toggle('is-invalid', select.classList.contains('is-invalid'));
    if (select.id) {
        toggle.id = select.id;
        select.removeAttribute('id');
    } else {
        globalIdSeq += 1;
        toggle.id = `enhanced-select-${globalIdSeq}`;
    }
    const listboxId = `${toggle.id}-listbox`;
    toggle.setAttribute('role', 'combobox');
    toggle.setAttribute('aria-haspopup', 'listbox');
    toggle.setAttribute('aria-expanded', 'false');
    toggle.setAttribute('aria-controls', listboxId);
    toggle.disabled = select.disabled;

    const panel = document.createElement('div');
    panel.className = 'enhanced-select-panel';
    panel.hidden = true;

    const searchInput = document.createElement('input');
    searchInput.type = 'search';
    searchInput.className = 'form-control form-control-sm enhanced-select-search border-0 border-bottom rounded-0';
    searchInput.placeholder = searchPlaceholder;
    searchInput.autocomplete = 'off';

    const listEl = document.createElement('div');
    listEl.className = 'enhanced-select-panel-options';
    listEl.id = listboxId;
    listEl.setAttribute('role', 'listbox');

    panel.appendChild(searchInput);
    panel.appendChild(listEl);

    root.appendChild(toggle);
    if (feedbackEl) {
        root.appendChild(feedbackEl);
    }
    root.appendChild(panel);

    select.dataset.enhancedSelectInit = '1';

    let allOptions = normalizeOptions(select);
    /** @type {{ value: string, label: string, disabled: boolean }[]} */
    let filtered = allOptions.slice();
    let rendered = 0;
    let open = false;
    /** @type {ReturnType<typeof setTimeout> | null} */
    let debounceTimer = null;

    function syncToggleState() {
        toggle.className = toggleClassFromSelect(select);
        toggle.classList.toggle('is-invalid', select.classList.contains('is-invalid'));
        toggle.disabled = select.disabled;
    }

    function syncButtonLabel() {
        const opt = select.options[select.selectedIndex];
        const text = opt ? opt.textContent.replace(/\s+/g, ' ').trim() : '';
        toggle.replaceChildren();
        const span = document.createElement('span');
        span.className = 'text-truncate flex-grow-1 text-start enhanced-select-toggle-label';
        span.textContent = text || '\u00a0';
        const icon = document.createElement('i');
        icon.className = 'bi bi-chevron-down enhanced-select-toggle-caret';
        icon.setAttribute('aria-hidden', 'true');
        toggle.appendChild(span);
        toggle.appendChild(icon);
    }

    /**
     * @param {{ value: string, label: string, disabled: boolean }} opt
     */
    function renderOptionRow(opt) {
        const row = document.createElement('button');
        row.type = 'button';
        row.className = 'enhanced-select-option';
        row.setAttribute('role', 'option');
        row.setAttribute('aria-selected', String(String(select.value) === String(opt.value)));
        row.textContent = opt.label;
        row.disabled = opt.disabled;
        if (String(select.value) === String(opt.value)) {
            row.classList.add('active');
        }
        row.addEventListener('click', e => {
            e.preventDefault();
            e.stopPropagation();
            if (opt.disabled) {
                return;
            }
            select.value = opt.value;
            select.dispatchEvent(new Event('input', { bubbles: true }));
            select.dispatchEvent(new Event('change', { bubbles: true }));
            closePanel();
            syncButtonLabel();
        });
        listEl.appendChild(row);
    }

    function appendBatch() {
        const next = filtered.slice(rendered, rendered + PAGE_SIZE);
        for (const opt of next) {
            renderOptionRow(opt);
        }
        rendered += next.length;
    }

    function applySearch() {
        filtered = filterByQuery(allOptions, searchInput.value);
        listEl.replaceChildren();
        rendered = 0;
        if (filtered.length === 0) {
            const empty = document.createElement('div');
            empty.className = 'text-muted small px-3 py-2';
            empty.textContent = noResultsText;
            listEl.appendChild(empty);
            return;
        }
        appendBatch();
    }

    function onListScroll() {
        if (rendered >= filtered.length) {
            return;
        }
        if (listEl.scrollTop + listEl.clientHeight >= listEl.scrollHeight - SCROLL_LOAD_THRESHOLD_PX) {
            appendBatch();
        }
    }

    function openPanel() {
        if (select.disabled) {
            return;
        }
        ensureGlobalDocumentListeners();
        open = true;
        openRoots.add(root);
        panel.hidden = false;
        toggle.setAttribute('aria-expanded', 'true');
        searchInput.value = '';
        allOptions = normalizeOptions(select);
        syncToggleState();
        applySearch();
        window.requestAnimationFrame(() => {
            searchInput.focus();
        });
    }

    function closePanel() {
        open = false;
        openRoots.delete(root);
        panel.hidden = true;
        toggle.setAttribute('aria-expanded', 'false');
        if (debounceTimer !== null) {
            clearTimeout(debounceTimer);
            debounceTimer = null;
        }
    }

    root._enhancedSelectCloseIfOutside = e => {
        if (!open) {
            return;
        }
        if (e.target instanceof Node && !root.contains(e.target)) {
            closePanel();
        }
    };

    root._enhancedSelectEscape = () => {
        if (open) {
            closePanel();
        }
    };

    toggle.addEventListener('click', e => {
        e.preventDefault();
        e.stopPropagation();
        if (open) {
            closePanel();
        } else {
            openPanel();
        }
    });

    searchInput.addEventListener('click', e => {
        e.stopPropagation();
    });

    searchInput.addEventListener('input', () => {
        if (debounceTimer !== null) {
            clearTimeout(debounceTimer);
        }
        debounceTimer = setTimeout(() => {
            debounceTimer = null;
            applySearch();
        }, SEARCH_DEBOUNCE_MS);
    });

    listEl.addEventListener('scroll', onListScroll);

    const mo = new MutationObserver(() => {
        allOptions = normalizeOptions(select);
        syncToggleState();
        syncButtonLabel();
        if (open) {
            applySearch();
        }
    });
    mo.observe(select, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: ['class', 'disabled'],
    });

    syncButtonLabel();
}

function initAll() {
    document.querySelectorAll('select.js-enhanced-select').forEach(el => {
        if (el instanceof HTMLSelectElement) {
            enhanceSelect(el);
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll);
} else {
    initAll();
}
