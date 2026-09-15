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

// Result page: remove skill chips before regenerating the PDF.
function initSkillEditor() {
    const form = document.querySelector('[data-skills-form]');
    if (!form) return;

    const dirty = form.querySelector('[data-skills-dirty]');

    form.querySelectorAll('[data-skill-remove]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const chip = btn.closest('[data-skill-chip]');
            const group = chip.closest('[data-skill-group]');
            chip.remove();

            // Drop the group heading if it's now empty.
            if (group && group.querySelectorAll('[data-skill-chip]').length === 0) {
                group.remove();
            }
            if (dirty) dirty.classList.remove('hidden');
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initPreviewModal();
    initSkillEditor();
});
