// =======================================================
//  Tel-U Shop Frontend Script (no Vite version, manual)
// =======================================================

// ===== Helper util ringkas =====
const $ = (sel, ctx = document) => ctx.querySelector(sel);
const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));

// Animasi kecil (fade)
const fadeIn = (el, dur = 300) => {
    el.style.opacity = 0;
    el.style.display = '';
    el.style.transition = `opacity ${dur}ms ease`;
    requestAnimationFrame(() => (el.style.opacity = 1));
};
const fadeOut = (el, dur = 300) => {
    el.style.transition = `opacity ${dur}ms ease`;
    el.style.opacity = 0;
    setTimeout(() => (el.style.display = 'none'), dur);
};

document.addEventListener('DOMContentLoaded', () => {
    console.log('✅ Tel-U Shop UI loaded (No Vite).');

    // ===== Demo tombol (optional) =====
    $$('[data-hello]').forEach(btn => {
        btn.addEventListener('click', () => {
            alert('JS aktif tanpa Vite! 🚀');
        });
    });

    // ===== Form enhancer =====
    $$('form').forEach(form => {
        form.addEventListener('submit', () => {
            const submitters = $$('button[type=submit], input[type=submit]', form);
            submitters.forEach(b => {
                b.disabled = true;
                b.dataset.originalText = b.textContent;
                b.textContent = 'Processing…';
                b.classList.add('btn-loading');
            });
            // auto re-enable (fallback)
            setTimeout(() => {
                submitters.forEach(b => {
                    b.disabled = false;
                    if (b.dataset.originalText) b.textContent = b.dataset.originalText;
                    b.classList.remove('btn-loading');
                });
            }, 8000);
        });
    });

    // ===== Toggle element visibility =====
    $$('[data-toggle]').forEach(tg => {
        tg.addEventListener('click', () => {
            const target = $(tg.dataset.toggle);
            if (target) target.classList.toggle('hidden');
        });
    });

    // ===== Flash alert auto-hide =====
    $$('.alert[data-autohide]').forEach(el => {
        const ms = parseInt(el.dataset.autohide, 10) || 3500;
        setTimeout(() => fadeOut(el, 400), ms);
    });

    // ===== Bottom nav active state animation =====
    const navItems = $$('.bottom-nav .item');
    const path = window.location.pathname;
    navItems.forEach(link => {
        if (path === '/' && link.href.endsWith('/')) link.classList.add('active');
        else if (link.href.endsWith(path)) link.classList.add('active');
        link.addEventListener('click', () => {
            navItems.forEach(i => i.classList.remove('active'));
            link.classList.add('active');
        });
    });

    // ===== Floating Action Button (FAB) feedback =====
    const fab = $('.fab');
    if (fab) {
        fab.addEventListener('click', () => {
            fab.animate([{ transform: 'scale(1)' }, { transform: 'scale(0.9)' }, { transform: 'scale(1)' }], {
                duration: 250,
                easing: 'ease-out'
            });
        });
    }

    // ===== Auto blur inputs on submit =====
    $$('form').forEach(form => {
        form.addEventListener('submit', () => {
            const active = document.activeElement;
            if (active && typeof active.blur === 'function') active.blur();
        });
    });

    // ===== Fade-in animation untuk konten utama =====
    const content = $('.page-content');
    if (content) fadeIn(content, 350);

    // ===== Keyboard safe area (mobile only) =====
    window.addEventListener('resize', () => {
        if (window.innerHeight < 500) document.body.classList.add('keyboard-open');
        else document.body.classList.remove('keyboard-open');
    });
});
