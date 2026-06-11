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
    const downloadButton = event.target.closest('[data-download-card]');

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
    const fileName = downloadButton.dataset.qrFilename || `Member_Card_${qrValue}.svg`;
    const svg = `
        <svg xmlns="http://www.w3.org/2000/svg" width="400" height="250" viewBox="0 0 400 250">
            <rect width="400" height="250" rx="15" fill="#1b007c"/>
            <rect x="10" y="10" width="380" height="230" rx="10" fill="#ffffff" fill-opacity="0.1"/>
            <text x="200" y="40" text-anchor="middle" font-family="Arial, sans-serif" font-size="16" font-weight="700" fill="#ffffff">KARTU ANGGOTA SISCARD</text>
            <rect x="20" y="60" width="80" height="100" rx="5" fill="#ffffff" fill-opacity="0.2"/>
            <text x="110" y="80" font-family="Arial, sans-serif" font-size="14" font-weight="700" fill="#ffffff">${escapedQrValue}</text>
            <text x="110" y="100" font-family="Arial, sans-serif" font-size="12" fill="#cbd5e1">MEMBER ID</text>
            <image x="280" y="60" width="100" height="100" href="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${qrValue}"/>
            <text x="20" y="220" font-family="Arial, sans-serif" font-size="10" fill="#ffffff" fill-opacity="0.6">PT XYZ</text>
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
