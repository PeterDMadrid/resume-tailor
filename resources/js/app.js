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

// Navbar: refresh the Gemini daily-usage counter without a full page reload.
function initGeminiUsage() {
    const widget = document.querySelector('[data-gemini-usage]');
    if (!widget) return;

    const url = widget.dataset.usageUrl;
    const todayEl = widget.querySelector('[data-usage-today]');
    const lifetimeEl = widget.querySelector('[data-usage-lifetime]');
    const refreshBtn = widget.querySelector('[data-usage-refresh]');
    const icon = widget.querySelector('[data-usage-refresh-icon]');

    const fmt = (n) => Number(n || 0).toLocaleString();

    const refresh = async () => {
        if (!url) return;
        icon?.classList.add('animate-spin');
        try {
            const res = await fetch(url, { headers: { Accept: 'application/json' } });
            if (!res.ok) return;
            const data = await res.json();
            if (todayEl) todayEl.textContent = fmt(data.today?.total);
            if (lifetimeEl) lifetimeEl.textContent = fmt(data.lifetime?.total);
        } catch {
            // Non-fatal: leave the last-known values in place.
        } finally {
            icon?.classList.remove('animate-spin');
        }
    };

    refreshBtn?.addEventListener('click', refresh);
}

document.addEventListener('DOMContentLoaded', () => {
    initPreviewModal();
    initSkillEditor();
    initGeminiUsage();
});
