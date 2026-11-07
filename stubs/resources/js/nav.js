// Rail + panel sidebar, mobile drawer and Cmd-K palette. Registered as Alpine.data
// components so the Blade never carries JS in an attribute.

const typing = (el) => el && (el.isContentEditable || /^(INPUT|TEXTAREA|SELECT)$/.test(el.tagName));

const store = {
    get(key) {
        try {
            return sessionStorage.getItem(key);
        } catch {
            return null;
        }
    },
    set(key, value) {
        try {
            sessionStorage.setItem(key, value);
        } catch {
            // Private mode: area memory is a convenience, never required.
        }
    },
};

const pathOf = (url) => {
    try {
        return new URL(url, location.origin).pathname.replace(/\/$/, '') || '/';
    } catch {
        return '';
    }
};

export function registerNav(Alpine) {
    Alpine.data('navShell', () => ({
        pref: 'auto',
        current: '',
        open: null,
        docked: false,
        timer: null,

        init() {
            this.pref = this.$el.dataset.navPanel || 'auto';
            this.current = this.$el.dataset.navCurrent || '';
            this.measure();

            const onResize = () => this.measure();
            window.addEventListener('resize', onResize);

            const onKey = (e) => {
                if (e.key === '[' && !e.metaKey && !e.ctrlKey && !e.altKey && !typing(e.target) && matchMedia('(min-width: 64rem)').matches) {
                    e.preventDefault();
                    this.togglePin();
                }
            };
            window.addEventListener('keydown', onKey);

            document.addEventListener('livewire:navigating', () => {
                window.removeEventListener('resize', onResize);
                window.removeEventListener('keydown', onKey);
            }, { once: true });

            // Area memory: the last page used in each area, per tab.
            if (this.current) {
                store.set('nav:last:' + this.current, location.pathname + location.search);
            }
        },

        measure() {
            const panel = this.$root.querySelector('[data-nav-docked]');
            this.docked = !!panel && getComputedStyle(panel).display !== 'none';
        },

        hasOverlay(key) {
            return !!this.$root.querySelector(`[data-nav-overlay="${key}"]`);
        },

        // Hover with a 200ms intent delay, so sweeping across the rail opens nothing.
        intent(key) {
            clearTimeout(this.timer);
            this.timer = setTimeout(() => this.show(key), 200);
        },

        cancelIntent() {
            clearTimeout(this.timer);
        },

        show(key) {
            // A docked panel already shows the current area; hovering elsewhere
            // while docked opens nothing, so the docked page never jumps.
            this.open = !this.docked && this.hasOverlay(key) ? key : null;
        },

        stay() {
            clearTimeout(this.timer);
        },

        leave() {
            clearTimeout(this.timer);
            this.timer = setTimeout(() => {
                this.open = null;
            }, 150);
        },

        openFromKey(key) {
            this.show(key);
            this.$nextTick(() => {
                const target = this.open
                    ? this.$root.querySelector(`[data-nav-overlay="${key}"]`)
                    : this.docked && key === this.current
                      ? this.$root.querySelector('[data-nav-docked]')
                      : null;
                target?.querySelector('nav a, nav button')?.focus();
            });
        },

        escape() {
            const key = this.open || (this.$root.querySelector('[data-nav-docked]')?.contains(document.activeElement) ? this.current : null);
            this.open = null;
            if (key) {
                this.$root.querySelector(`[data-nav-area="${key}"]`)?.focus();
            }
        },

        togglePin() {
            this.pref = this.docked ? 'unpinned' : 'pinned';
            document.cookie = `nav_panel=${this.pref};path=/;max-age=31536000;SameSite=Lax`;
            this.open = null;
            this.$nextTick(() => this.measure());
        },

        // Click always navigates: to the last page used in the area when it is
        // still one of the area's pages, else to the area's landing.
        go(e) {
            if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || e.button !== 0) {
                return;
            }

            const link = e.currentTarget;
            const key = link.dataset.navArea;
            let target = link.getAttribute('href');
            const last = store.get('nav:last:' + key);

            if (last && key !== this.current) {
                const lastPath = pathOf(last);
                const urls = JSON.parse(link.dataset.navUrls || '[]').map(pathOf).filter(Boolean);

                if (urls.some((p) => lastPath === p || lastPath.startsWith(p + '/'))) {
                    target = last;
                }
            }

            e.preventDefault();
            window.Livewire ? window.Livewire.navigate(target) : (location.href = target);
        },
    }));

    // The mobile drawer is Flux's stashable sidebar; this only adds the focus
    // trap and Esc, which Flux does not provide.
    Alpine.data('navDrawer', () => ({
        openMobile: false,

        init() {
            const sidebar = this.$el.closest('[data-flux-sidebar]');
            if (!sidebar) {
                return;
            }

            const sync = () => {
                this.openMobile = sidebar.hasAttribute('data-flux-sidebar-on-mobile') && !sidebar.hasAttribute('data-flux-sidebar-collapsed-mobile');
            };
            sync();
            new MutationObserver(sync).observe(sidebar, { attributes: true });
        },

        close() {
            if (this.openMobile) {
                this.$root.dispatchEvent(new CustomEvent('flux-sidebar-toggle', { bubbles: true }));
            }
        },
    }));

    Alpine.data('navPalette', () => ({
        open: false,
        query: '',
        active: 0,
        pages: [],
        opener: null,

        init() {
            try {
                this.pages = JSON.parse(document.getElementById('nav-palette-pages')?.textContent || '[]');
            } catch {
                this.pages = [];
            }
        },

        get results() {
            const q = this.query.trim().toLowerCase();
            if (q === '') {
                return this.pages.slice(0, 12);
            }

            return this.pages
                .map((page) => {
                    const label = page.label.toLowerCase();
                    const hay = `${label} ${page.context}`.toLowerCase();
                    const score = label.startsWith(q) ? 0 : label.includes(q) ? 1 : hay.includes(q) ? 2 : -1;
                    return { page, score };
                })
                .filter((r) => r.score >= 0)
                .sort((a, b) => a.score - b.score)
                .map((r) => r.page);
        },

        globalKey(e) {
            if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                this.open ? this.close() : this.show();
            }
        },

        show(detail) {
            this.opener = document.activeElement;
            this.query = typeof detail === 'string' ? detail : '';
            this.active = 0;
            this.open = true;
            this.$nextTick(() => this.$refs.input?.focus());
        },

        close() {
            this.open = false;
            this.$nextTick(() => this.opener?.focus?.());
        },

        move(step) {
            const n = this.results.length;
            if (n === 0) {
                return;
            }
            this.active = (this.active + step + n) % n;
            this.$nextTick(() => document.getElementById('nav-palette-opt-' + this.active)?.scrollIntoView({ block: 'nearest' }));
        },

        go(index = this.active) {
            const page = this.results[index];
            if (!page) {
                return;
            }
            this.open = false;
            if (page.external) {
                window.open(page.url, '_blank', 'noopener');
            } else if (window.Livewire) {
                window.Livewire.navigate(page.url);
            } else {
                location.href = page.url;
            }
        },
    }));
}
