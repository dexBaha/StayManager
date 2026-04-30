function drawReservationChart(canvasId, labels, values) {
    const canvas = document.getElementById(canvasId);

    if (!canvas) {
        return;
    }

    const ctx = canvas.getContext('2d');
    const width = canvas.width;
    const height = canvas.height;
    const padding = 44;
    const max = Math.max(...values, 1);
    const barWidth = (width - padding * 2) / values.length - 20;

    ctx.clearRect(0, 0, width, height);
    ctx.font = '14px Arial';
    ctx.fillStyle = '#172033';
    ctx.fillText('Reservations by status', padding, 24);

    values.forEach((value, index) => {
        const x = padding + index * (barWidth + 30);
        const barHeight = ((height - 100) * value) / max;
        const y = height - padding - barHeight;

        ctx.fillStyle = ['#d97706', '#16a34a', '#dc2626'][index % 3];
        ctx.fillRect(x, y, barWidth, barHeight);

        ctx.fillStyle = '#172033';
        ctx.fillText(String(value), x + barWidth / 2 - 4, y - 8);
        ctx.fillText(labels[index], x, height - 18);
    });
}

