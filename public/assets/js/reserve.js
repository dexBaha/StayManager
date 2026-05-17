document.querySelectorAll('[data-room-photo]').forEach((button) => {
    button.addEventListener('click', () => {
        const mainPhoto = document.getElementById('mainRoomPhoto');

        if (!mainPhoto) {
            return;
        }

        mainPhoto.src = button.dataset.roomPhoto;
        document.querySelectorAll('.room-photo-thumb').forEach((thumb) => {
            thumb.classList.remove('ring-brand-600');
            thumb.classList.add('ring-transparent');
        });
        button.classList.add('ring-brand-600');
        button.classList.remove('ring-transparent');
    });
});

document.querySelectorAll('[data-stay-form]').forEach((form) => {
    const checkIn = form.querySelector('[data-check-in]');
    const checkOut = form.querySelector('[data-check-out]');
    const summary = form.querySelector('[data-night-summary]');
    const oneDayMs = 24 * 60 * 60 * 1000;

    if (!checkIn || !checkOut || !summary) {
        return;
    }

    const toDate = (date) => {
        const [year, month, day] = date.split('-').map(Number);
        return new Date(Date.UTC(year, month - 1, day));
    };

    const nextDate = (date) => {
        const next = toDate(date);
        next.setUTCDate(next.getUTCDate() + 1);
        return next.toISOString().slice(0, 10);
    };

    const refreshStayLength = () => {
        if (checkIn.value) {
            checkOut.min = nextDate(checkIn.value);
        }

        if (!checkIn.value || !checkOut.value) {
            summary.textContent = 'Choose dates to see the stay length.';
            return;
        }

        const nights = Math.round((toDate(checkOut.value) - toDate(checkIn.value)) / oneDayMs);
        summary.textContent = nights > 0 ? `${nights} night${nights === 1 ? '' : 's'} selected.` : 'Check-out must be after check-in.';
    };

    checkIn.addEventListener('change', () => {
        if (checkIn.value && checkOut.value && checkOut.value <= checkIn.value) {
            checkOut.value = nextDate(checkIn.value);
        }

        refreshStayLength();
    });
    checkOut.addEventListener('change', refreshStayLength);
    refreshStayLength();
});
