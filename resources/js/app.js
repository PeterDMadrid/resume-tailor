// Preview modal: lazily loads the config-only resume PDF into an iframe.
const previewUrl = '/tailor/preview';

function initPreviewModal() {
    const modal = document.querySelector('[data-preview-modal]');
    const frame = document.querySelector('[data-preview-frame]');
    if (!modal || !frame) return;

    const open = () => {
        if (!frame.src) frame.src = previewUrl; // load once
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };

    const close = () => {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    };

    document.querySelectorAll('[data-preview-open]').forEach((el) => el.addEventListener('click', open));
    document.querySelectorAll('[data-preview-close]').forEach((el) => el.addEventListener('click', close));
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) close();
    });
}

document.addEventListener('DOMContentLoaded', initPreviewModal);
