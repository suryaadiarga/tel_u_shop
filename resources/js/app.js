// Basic interactivity tanpa lib tambahan

// Set active state bottom nav berdasar path saat ini
(function () {
    const path = window.location.pathname
    document.querySelectorAll('[data-nav-link]').forEach(a => {
        const match = a.getAttribute('href')
        if (match === '/' && path === '/') a.classList.add('is-active')
        else if (match !== '/' && path.startsWith(match)) a.classList.add('is-active')
    })
})()

// Ripple kecil untuk tombol (efek sederhana)
document.addEventListener('click', (e) => {
    const target = e.target.closest('[data-ripple]')
    if (!target) return
    const circle = document.createElement('span')
    const d = Math.max(target.clientWidth, target.clientHeight)
    circle.style.width = circle.style.height = d + 'px'
    circle.style.left = (e.clientX - target.getBoundingClientRect().left - d / 2) + 'px'
    circle.style.top = (e.clientY - target.getBoundingClientRect().top - d / 2) + 'px'
    circle.className = 'pointer-events-none absolute rounded-full bg-black/10 animate-[ping_0.65s_ease-out_1]'
    target.style.position = 'relative'
    target.appendChild(circle)
    setTimeout(() => circle.remove(), 700)
})

// Placeholder “scan” handler untuk FAB kalau ada elemen [data-fab-scan]
const fab = document.querySelector('[data-fab-scan]')
if (fab) {
    fab.addEventListener('click', () => {
        // arahkan ke route qr (tanpa ganggu route lain)
        const to = fab.dataset.to || '/qr'
        window.location.href = to
    })
}
