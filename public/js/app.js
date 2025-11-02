document.addEventListener('DOMContentLoaded', () => {
    const focusEmail = document.querySelector('input[type="email"]');
    if (focusEmail && document.body.classList.contains('login')) { focusEmail.focus(); }
});
