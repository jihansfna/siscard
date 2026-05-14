import './bootstrap';

const chartTooltip = document.createElement('div');
chartTooltip.className = 'chart-tooltip';
chartTooltip.setAttribute('role', 'tooltip');
document.body.appendChild(chartTooltip);

const moveChartTooltip = (clientX, clientY) => {
    const margin = 16;
    const x = Math.min(Math.max(clientX, margin), window.innerWidth - margin);
    const y = Math.min(Math.max(clientY, margin), window.innerHeight - margin);

    chartTooltip.style.left = `${x}px`;
    chartTooltip.style.top = `${y}px`;
};

const showChartTooltip = (text, clientX, clientY) => {
    if (!text) {
        chartTooltip.classList.remove('is-visible');
        return;
    }

    chartTooltip.textContent = text;
    moveChartTooltip(clientX, clientY);
    chartTooltip.classList.add('is-visible');
};

const hideChartTooltip = () => {
    chartTooltip.classList.remove('is-visible');
};

const getPieTooltipText = (pieChart, event) => {
    const items = JSON.parse(pieChart.dataset.pieItems || '[]');
    const total = items.reduce((sum, item) => sum + Number(item.value || 0), 0);

    if (!total) {
        return '';
    }

    const rect = pieChart.getBoundingClientRect();
    const centerX = rect.left + rect.width / 2;
    const centerY = rect.top + rect.height / 2;
    const offsetX = event.clientX - centerX;
    const offsetY = event.clientY - centerY;
    const radius = rect.width / 2;
    const distance = Math.hypot(offsetX, offsetY);

    if (distance > radius) {
        return '';
    }

    const angle = (Math.atan2(offsetY, offsetX) * 180) / Math.PI;
    const percent = (((angle + 90 + 360) % 360) / 360) * 100;
    let cursor = 0;

    for (const item of items) {
        cursor += (Number(item.value || 0) / total) * 100;

        if (percent <= cursor) {
            return item.display;
        }
    }

    return items.at(-1)?.display || '';
};

document.addEventListener('mousemove', (event) => {
    const valueTarget = event.target.closest('[data-chart-tooltip]');

    if (valueTarget) {
        showChartTooltip(valueTarget.dataset.chartTooltip, event.clientX, event.clientY);
        return;
    }

    const pieChart = event.target.closest('.pie-chart[data-pie-items]');

    if (pieChart) {
        showChartTooltip(getPieTooltipText(pieChart, event), event.clientX, event.clientY);
        return;
    }

    hideChartTooltip();
});

document.addEventListener('focusin', (event) => {
    const valueTarget = event.target.closest('[data-chart-tooltip]');

    if (!valueTarget) {
        return;
    }

    const rect = valueTarget.getBoundingClientRect();
    showChartTooltip(valueTarget.dataset.chartTooltip, rect.left + rect.width / 2, rect.top);
});

document.addEventListener('focusout', (event) => {
    if (event.target.closest('[data-chart-tooltip]')) {
        hideChartTooltip();
    }
});

document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') {
        return;
    }

    const drawer = document.querySelector('.member-drawer.is-open');
    const closeUrl = drawer?.getAttribute('data-close-url');

    if (closeUrl) {
        window.location.href = closeUrl;
    }
});

document.addEventListener('click', (event) => {
    const downloadButton = event.target.closest('[data-download-qr]');

    if (!downloadButton) {
        return;
    }

    const qrValue = downloadButton.dataset.qrValue || 'SISCARD-MEMBER';
    const escapedQrValue = qrValue.replace(/[&<>"']/g, (character) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&apos;',
    })[character]);
    const fileName = downloadButton.dataset.qrFilename || 'siscard-qr.svg';
    const svg = `
        <svg xmlns="http://www.w3.org/2000/svg" width="320" height="320" viewBox="0 0 320 320">
            <rect width="320" height="320" fill="#ffffff"/>
            <rect x="24" y="24" width="72" height="72" fill="#0f172a"/>
            <rect x="44" y="44" width="32" height="32" fill="#ffffff"/>
            <rect x="224" y="24" width="72" height="72" fill="#0f172a"/>
            <rect x="244" y="44" width="32" height="32" fill="#ffffff"/>
            <rect x="24" y="224" width="72" height="72" fill="#0f172a"/>
            <rect x="44" y="244" width="32" height="32" fill="#ffffff"/>
            <path fill="#0f172a" d="M124 28h24v24h-24zM172 28h24v24h-24zM124 76h72v24h-72zM124 124h24v24h-24zM172 124h48v24h-48zM244 124h24v24h-24zM124 172h96v24h-96zM244 172h24v48h-24zM124 220h24v48h-24zM172 220h24v24h-24zM220 244h76v24h-76zM172 276h24v20h-24zM220 292h48v4h-48z"/>
            <rect x="92" y="132" width="136" height="56" rx="8" fill="#ffffff"/>
            <text x="160" y="166" text-anchor="middle" font-family="Arial, sans-serif" font-size="18" font-weight="700" fill="#1e293b">${escapedQrValue}</text>
        </svg>
    `.trim();
    const blob = new Blob([svg], { type: 'image/svg+xml' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');

    link.href = url;
    link.download = fileName;
    document.body.appendChild(link);
    link.click();
    link.remove();
    URL.revokeObjectURL(url);
});
