import './bootstrap';
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables)

const COLORS = ['#f97316','#3b82f6','#10b981','#8b5cf6','#ef4444','#f59e0b','#06b6d4','#ec4899','#84cc16'];

let priceChart = null;

async function fetchData() {
    const res = await fetch('/chart/data');
    const tickers = await res.json();

    renderChart(tickers);
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
        priceChart.update();
    } else {
        priceChart = new Chart(ctx, {
            type: 'line',
            data: { labels, datasets },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } },
                scales: {
                    y: { ticks: { callback: v => v + '%' } }
                }
            }
        });
    }
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

fetchData();
setInterval(fetchData, 60000);
