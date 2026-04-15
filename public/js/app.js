// DólarApp — JS global

// ── Modal de contacto ─────────────────────────────────────────────────────
function showContact(phone, offerId) {
    const modal = document.getElementById('contact-modal-' + offerId);
    if (modal) modal.style.display = 'flex';
}

function closeModal(offerId) {
    const modal = document.getElementById('contact-modal-' + offerId);
    if (modal) modal.style.display = 'none';
}

// Cerrar modal al hacer clic fuera
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('modal-overlay')) {
        e.target.style.display = 'none';
    }
});

// ── Flash messages: auto-ocultar ──────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const flashes = document.querySelectorAll('.flash');
    flashes.forEach(el => {
        setTimeout(() => {
            el.style.transition = 'opacity .4s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 400);
        }, 4000);
    });
});
