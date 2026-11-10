const themeKey = 'simlab-theme';
const themeChoices = ['system', 'light', 'dark'];
const systemTheme = window.matchMedia('(prefers-color-scheme: dark)');
const root = document.documentElement;

function applyTheme(preference) {
    const selected = themeChoices.includes(preference) ? preference : 'system';
    const dark = selected === 'dark' || (selected === 'system' && systemTheme.matches);

    root.dataset.themePreference = selected;
    root.dataset.bsTheme = dark ? 'dark' : 'light';

    document.querySelectorAll('[data-theme-choice]').forEach((button) => {
        button.setAttribute('aria-pressed', String(button.dataset.themeChoice === selected));
    });

    const icon = document.querySelector('[data-theme-menu] .bi');
    if (icon) {
        icon.className = `bi bi-${selected === 'system' ? 'circle-half' : dark ? 'moon-stars' : 'sun'}`;
    }

    document.querySelector('meta[name="theme-color"]')?.setAttribute('content', dark ? '#111923' : '#2858b8');
}

applyTheme(root.dataset.themePreference);

document.querySelectorAll('[data-theme-choice]').forEach((button) => {
    button.addEventListener('click', () => {
        const preference = button.dataset.themeChoice;
        applyTheme(preference);

        try {
            localStorage.setItem(themeKey, preference);
        } catch {
            // The selected theme still works when browser storage is unavailable.
        }
    });
});

systemTheme.addEventListener('change', () => {
    if (root.dataset.themePreference === 'system') {
        applyTheme('system');
    }
});

window.addEventListener('storage', (event) => {
    if (event.key === themeKey || event.key === null) {
        applyTheme(event.newValue);
    }
});

const loginForm = document.querySelector('[data-login-form]');

if (loginForm) {
    const username = loginForm.querySelector('#usernameInput');
    const role = loginForm.querySelector('#roleInput');
    const password = loginForm.querySelector('#passwordInput');
    const passwordToggle = loginForm.querySelector('[data-password-toggle]');
    const submit = loginForm.querySelector('[data-login-submit]');
    const presetButtons = document.querySelectorAll('[data-preset-username]');
    const presetFeedback = document.querySelector('[data-preset-feedback]');

    passwordToggle.hidden = false;
    document.querySelector('[data-presets]').hidden = false;

    passwordToggle.addEventListener('click', () => {
        const reveal = password.type === 'password';
        password.type = reveal ? 'text' : 'password';
        passwordToggle.setAttribute('aria-pressed', String(reveal));
        passwordToggle.setAttribute('aria-label', reveal ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
        passwordToggle.querySelector('.bi').className = `bi bi-${reveal ? 'eye-slash' : 'eye'}`;
    });

    function syncPresets() {
        presetButtons.forEach((button) => {
            const selected = username.value === button.dataset.presetUsername && role.value === button.dataset.presetRole;
            button.setAttribute('aria-pressed', String(selected));
        });
    }

    presetButtons.forEach((button) => {
        button.addEventListener('click', () => {
            username.value = button.dataset.presetUsername;
            role.value = button.dataset.presetRole;
            password.value = '';
            syncPresets();
            presetFeedback.textContent = `Peran ${role.value} dipilih. Nama pengguna ${username.value} siap digunakan.`;
        });
    });

    username.addEventListener('input', syncPresets);
    role.addEventListener('change', syncPresets);
    syncPresets();

    loginForm.addEventListener('submit', () => {
        submit.disabled = true;
        loginForm.setAttribute('aria-busy', 'true');
        submit.querySelector('span').textContent = 'Membuka ruang kerja…';
        submit.querySelector('.bi').className = 'bi bi-hourglass-split';
    });

    window.addEventListener('pageshow', () => {
        submit.disabled = false;
        loginForm.removeAttribute('aria-busy');
        submit.querySelector('span').textContent = 'Masuk ke SIM-LAB';
        submit.querySelector('.bi').className = 'bi bi-arrow-right';
    });
}

const inventoryForm = document.querySelector('[data-inventory-form]');

if (inventoryForm) {
    const search = inventoryForm.querySelector('#inventorySearch');
    const category = inventoryForm.querySelector('#inventoryCategory');
    const status = inventoryForm.querySelector('#inventoryStatus');
    const emptyState = document.querySelector('[data-inventory-empty]');
    const exportLinks = document.querySelectorAll('[data-export-inventory]');
    const rows = Array.from(document.querySelectorAll('[data-inventory-row]'), (element) => ({
        element,
        search: element.dataset.search,
        category: element.dataset.category,
        status: element.dataset.status,
        stock: Number(element.dataset.stock),
        available: Number(element.dataset.available),
    }));

    function writeFilters(url, filters) {
        Object.entries(filters).forEach(([key, value]) => {
            if (value) {
                url.searchParams.set(key, value);
            } else {
                url.searchParams.delete(key);
            }
        });

        return url;
    }

    function applyFilters(updateUrl = true) {
        const filters = { q: search.value.trim(), kategori: category.value, status: status.value };
        const query = filters.q.toLowerCase();
        let count = 0;
        let stock = 0;
        let available = 0;

        rows.forEach((row) => {
            const matches = (!query || row.search.includes(query))
                && (!filters.kategori || row.category === filters.kategori)
                && (!filters.status || row.status === filters.status);

            row.element.hidden = !matches;

            if (matches) {
                count += 1;
                stock += row.stock;
                available += row.available;
            }
        });

        document.querySelectorAll('[data-result-count]').forEach((element) => { element.textContent = String(count); });
        document.querySelector('[data-result-stock]').textContent = String(stock);
        document.querySelector('[data-result-available]').textContent = String(available);
        emptyState.hidden = count > 0;

        exportLinks.forEach((link) => {
            link.href = writeFilters(new URL(link.dataset.exportUrl, window.location.origin), filters).href;
        });

        if (updateUrl) {
            const url = writeFilters(new URL(window.location.href), filters);
            window.history.replaceState(window.history.state, '', url);
        }
    }

    search.addEventListener('input', () => applyFilters());
    category.addEventListener('change', () => applyFilters());
    status.addEventListener('change', () => applyFilters());

    inventoryForm.addEventListener('submit', (event) => {
        event.preventDefault();
        applyFilters();
    });

    document.querySelectorAll('[data-reset-inventory]').forEach((link) => {
        link.addEventListener('click', (event) => {
            event.preventDefault();
            search.value = '';
            category.value = '';
            status.value = '';
            applyFilters();
            search.focus();
        });
    });

    window.addEventListener('popstate', () => {
        const params = new URL(window.location.href).searchParams;
        search.value = params.get('q') || '';
        category.value = params.get('kategori') || '';
        status.value = params.get('status') || '';
        applyFilters(false);
    });

    applyFilters(false);
    inventoryForm.querySelector('[data-filter-submit]').hidden = true;
}

const mobileNavigation = document.querySelector('#navbarMainContent');
const mobileNavigationButton = document.querySelector('[data-mobile-navigation]');

if (mobileNavigation && mobileNavigationButton) {
    mobileNavigation.addEventListener('show.bs.collapse', () => {
        mobileNavigationButton.setAttribute('aria-label', 'Tutup navigasi');
        mobileNavigationButton.querySelector('.bi').className = 'bi bi-x-lg';
    });

    mobileNavigation.addEventListener('hide.bs.collapse', () => {
        mobileNavigationButton.setAttribute('aria-label', 'Buka navigasi');
        mobileNavigationButton.querySelector('.bi').className = 'bi bi-list';
    });
}
