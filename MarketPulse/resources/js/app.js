import './bootstrap';
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables)

const COLORS = ['#f97316','#3b82f6','#10b981','#8b5cf6','#ef4444','#f59e0b','#06b6d4','#ec4899','#84cc16'];

let priceChart = null;
let filtersRendered = false;

async function fetchData() {
    const res = await fetch('/chart/data');
    const tickers = await res.json();

    renderChart(tickers);

    if (!filtersRendered) {
        renderFilters(tickers);
        filtersRendered = true;
    }

    renderTable(tickers);
    document.getElementById('last-updated').textContent = new Date().toLocaleString();
}

function renderChart(tickers) {
    const ctx = document.getElementById('price-chart');
    if (!ctx) return;

    const datasets = tickers.map((ticker, i) => {
        const prices = ticker.snapshots.map(s => parseFloat(s.price)).reverse();
        const base = prices[0] || 1;
        return {
            label: ticker.ticker,
            data: prices.map(p => parseFloat(((p - base) / base * 100).toFixed(2))),
            borderColor: COLORS[i % COLORS.length],
            backgroundColor: 'transparent',
            borderWidth: 2,
            pointRadius: 0,
            tension: 0.3,
        };
    });

    const labels = tickers[0]?.snapshots.map(s =>
        new Date(s.timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
    ).reverse() ?? [];

    if (priceChart) {
        priceChart.data.labels = labels;
        priceChart.data.datasets = datasets;
        document.querySelectorAll('.ticker-filter').forEach(cb => {
            priceChart.getDatasetMeta(parseInt(cb.dataset.index)).hidden = !cb.checked;
        });
        priceChart.update();
    } else {
        priceChart = new Chart(ctx, {
            type: 'line',
            data: { labels, datasets },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { ticks: { callback: v => v + '%' } } }
            }
        });
    }
}

function renderFilters(tickers) {
    const container = document.getElementById('chart-filters');
    if (!container) return;

    const checkboxes = tickers.map((ticker, i) => `
        <label class="flex items-center gap-1.5 cursor-pointer text-sm select-none">
            <input type="checkbox" checked data-index="${i}" class="ticker-filter cursor-pointer">
            <span class="font-medium" style="color:${COLORS[i % COLORS.length]}">${ticker.ticker}</span>
        </label>
    `).join('');

    container.innerHTML = `
        <div class="flex items-center gap-3 flex-wrap mt-4 pt-4 border-t border-gray-100">
            <button id="filter-select-all" class="text-xs px-2 py-1 rounded bg-gray-100 hover:bg-gray-200 text-gray-600 cursor-pointer">Select All</button>
            <button id="filter-deselect-all" class="text-xs px-2 py-1 rounded bg-gray-100 hover:bg-gray-200 text-gray-600 cursor-pointer">Deselect All</button>
            <span class="text-gray-200 select-none">|</span>
            ${checkboxes}
        </div>
    `;

    container.querySelectorAll('.ticker-filter').forEach(cb => {
        cb.addEventListener('change', () => toggleDataset(parseInt(cb.dataset.index), cb.checked));
    });

    document.getElementById('filter-select-all').addEventListener('click', () => {
        container.querySelectorAll('.ticker-filter').forEach(cb => {
            cb.checked = true;
            toggleDataset(parseInt(cb.dataset.index), true);
        });
    });

    document.getElementById('filter-deselect-all').addEventListener('click', () => {
        container.querySelectorAll('.ticker-filter').forEach(cb => {
            cb.checked = false;
            toggleDataset(parseInt(cb.dataset.index), false);
        });
    });
}

function toggleDataset(index, visible) {
    if (!priceChart) return;
    priceChart.getDatasetMeta(index).hidden = !visible;
    priceChart.update();
}

function renderTable(tickers) {
    const tbody = document.getElementById('ticker-table');
    if (!tbody) return;

    tbody.innerHTML = tickers.map((ticker, i) => {
        const sentimentColor = ticker.sentiment > 50 ? 'text-green-600' : ticker.sentiment < 50 ? 'text-red-500' : 'text-gray-500';
        const hciColor = ticker.hci >= 75 ? 'text-orange-500 font-bold' : ticker.hci <= 25 ? 'text-red-600 font-bold' : 'text-gray-500';

        return `
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 font-semibold">${ticker.ticker}</td>
                <td class="px-6 py-4 text-right">$${ticker.price ?? '--'}</td>
                <td class="px-6 py-4 text-right ${sentimentColor}">${ticker.sentiment ?? '--'}</td>
                <td class="px-6 py-4 text-right ${hciColor}">${ticker.hci ?? '--'}</td>
            </tr>
        `;
    }).join('');
}

if (document.getElementById('ticker-table')) {
    fetchData();
    setInterval(fetchData, 60000);
}
