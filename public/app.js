// =======================================================
// Tel-U Shop Frontend Script (no Vite, single JS)
// Cocok dengan layouts.app + app.css yang kita pakai
// =======================================================

// --- Helper ---
const $ = (sel, ctx = document) => ctx.querySelector(sel);
const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));

const fadeIn = (el, dur = 300) => {
    if (!el) return;
    el.style.opacity = 0;
    el.style.display = '';
    el.style.transition = `opacity ${dur}ms ease`;
    requestAnimationFrame(() => (el.style.opacity = 1));
};
const fadeOut = (el, dur = 300) => {
    if (!el) return;
    el.style.transition = `opacity ${dur}ms ease`;
    el.style.opacity = 0;
    setTimeout(() => (el.style.display = 'none'), dur);
};

// --- Ripple effect ---
function attachRipple(el) {
    el.addEventListener('click', e => {
        const rect = el.getBoundingClientRect();
        const r = Math.max(rect.width, rect.height);
        const ripple = document.createElement('span');
        ripple.className = 'ripple-effect';
        ripple.style.width = ripple.style.height = `${r}px`;
        ripple.style.left = `${e.clientX - rect.left - r / 2}px`;
        ripple.style.top = `${e.clientY - rect.top - r / 2}px`;
        el.appendChild(ripple);
        setTimeout(() => ripple.remove(), 500);
    });
}

// --- Theme (light/dark) ---
const THEME_KEY = 'telu-theme';
function applyTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme); // CSS pakai [data-theme]
}
function initTheme() {
    const saved = localStorage.getItem(THEME_KEY);
    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    applyTheme(saved || (prefersDark ? 'dark' : 'light'));
}
function toggleTheme() {
    const curr = document.documentElement.getAttribute('data-theme') || 'light';
    const next = curr === 'dark' ? 'light' : 'dark';
    applyTheme(next);
    localStorage.setItem(THEME_KEY, next);
    // sinkronkan toggle UI (jika ada)
    const toggles = $$('[data-theme-toggle]');
    toggles.forEach(t => {
        if ('checked' in t) t.checked = (next === 'dark');
        if (t.dataset.themeLabel) t.textContent = next === 'dark' ? 'Light' : 'Dark';
    });
}

// --- Bottom-nav active ---
function setActiveNav() {
    const here = window.location.pathname.replace(/\/+$/, '') || '/';
    $$('a[data-nav-link]').forEach(a => {
        const path = new URL(a.href, window.location.origin).pathname.replace(/\/+$/, '') || '/';
        a.classList.toggle('active', path === here);
    });
}

// --- Keyboard safe (mobile) ---
function setupKeyboardSafe() {
    const onResize = () => {
        const vp = window.visualViewport;
        const keyboardOpen = vp ? (vp.height < window.innerHeight - 80) : (window.innerHeight < 500);
        document.body.classList.toggle('keyboard-open', keyboardOpen);
    };
    if (window.visualViewport) {
        visualViewport.addEventListener('resize', onResize);
    } else {
        window.addEventListener('resize', onResize);
    }
    onResize();
}

// --- Form enhancer (anti double submit) ---
function enhanceForms() {
    $$('form').forEach(form => {
        form.addEventListener('submit', () => {
            const submitters = $$('button[type=submit], input[type=submit]', form);
            submitters.forEach(b => {
                b.disabled = true;
                b.dataset.originalText = b.textContent || b.value || '';
                if (b.tagName === 'BUTTON') b.textContent = 'Processing…';
                if (b.tagName === 'INPUT') b.value = 'Processing…';
                b.classList.add('btn-loading');
            });
            // fallback restore jika halaman tidak pindah
            setTimeout(() => {
                submitters.forEach(b => {
                    b.disabled = false;
                    const t = b.dataset.originalText || '';
                    if (b.tagName === 'BUTTON') b.textContent = t;
                    if (b.tagName === 'INPUT') b.value = t;
                    b.classList.remove('btn-loading');
                });
            }, 8000);
            // blur keyboard
            const active = document.activeElement;
            if (active && typeof active.blur === 'function') active.blur();
        });
    });
}

// --- FAB behaviour ---
function enhanceFab() {
    const fab = $('.fab');
    if (!fab) return;
    attachRipple(fab);
    fab.addEventListener('click', () => {
        fab.animate([{ transform: 'scale(1)' }, { transform: 'scale(.92)' }, { transform: 'scale(1)' }], { duration: 220, easing: 'ease-out' });
        const to = fab.dataset.to || fab.getAttribute('data-to') || fab.dataset.fabScan || '';
        if (to) window.location.assign(to);
    });
}

// --- Init ---
document.addEventListener('DOMContentLoaded', () => {
    console.log('✅ Tel-U Shop UI loaded');

    // Theme init + toggle
    initTheme();
    $$('[data-theme-toggle]').forEach(t => {
        // support button/checkbox
        if (t.type === 'checkbox') {
            t.checked = (document.documentElement.getAttribute('data-theme') === 'dark');
            t.addEventListener('change', toggleTheme);
        } else {
            t.addEventListener('click', toggleTheme);
        }
        attachRipple(t);
    });

    // Ripple untuk elemen bertanda
    $$('[data-ripple]').forEach(attachRipple);

    // Flash auto-hide
    $$('.alert[data-autohide]').forEach(el => {
        const ms = parseInt(el.dataset.autohide, 10) || 3500;
        setTimeout(() => fadeOut(el, 400), ms);
    });

    // Bottom nav active
    setActiveNav();
    // animasi klik + ripple
    $$('a[data-nav-link]').forEach(a => {
        attachRipple(a);
        a.addEventListener('click', () => {
            $$('a[data-nav-link]').forEach(i => i.classList.remove('active'));
            a.classList.add('active');
        });
    });

    // Form enhancement
    enhanceForms();

    // FAB
    enhanceFab();

    // Keyboard-safe handling
    setupKeyboardSafe();

    // Fade-in konten utama
    fadeIn($('.page-content'), 300);
});
