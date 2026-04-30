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

document.querySelectorAll('[data-hotel-modal-open]').forEach((button) => {
    button.addEventListener('click', () => {
        const modal = document.getElementById(button.dataset.hotelModalOpen);

        if (!modal) {
            return;
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex', 'is-visible');
        document.body.classList.add('overflow-hidden');
    });
});

document.querySelectorAll('[data-hotel-modal]').forEach((modal) => {
    const closeModal = () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex', 'is-visible');
        document.body.classList.remove('overflow-hidden');
    };

    modal.querySelectorAll('[data-hotel-modal-close]').forEach((button) => {
        button.addEventListener('click', closeModal);
    });

    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeModal();
        }
    });
});

document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') {
        return;
    }

    document.querySelectorAll('[data-hotel-modal].is-visible').forEach((modal) => {
        modal.classList.add('hidden');
        modal.classList.remove('flex', 'is-visible');
    });

    document.body.classList.remove('overflow-hidden');
});
