document.querySelectorAll('[data-hotel-card]').forEach((card) => {
    const toggle = card.querySelector('[data-hotel-toggle]');
    const panel = card.querySelector('[data-hotel-rooms]');
    const label = card.querySelector('[data-toggle-label]');

    toggle.addEventListener('click', () => {
        const isOpen = card.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', String(isOpen));
        label.textContent = isOpen ? 'Hide rooms' : 'Show rooms';

        if (isOpen) {
            panel.style.maxHeight = `${panel.scrollHeight}px`;
            card.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
            panel.style.maxHeight = '0px';
        }
    });
});
