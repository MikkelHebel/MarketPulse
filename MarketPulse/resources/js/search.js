import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

const container = document.getElementById('search-charts');
if (!container) return;

const ticker = container.dataset.ticker;

let priceChart = null;
let analysisChart = null;

function showError(message) {
    document.getElementById('search-loading').innerHTML =
        `<p class="text-red-400 text-lg">${message}</p>`;
}

async function fetchData() {
    try {
        const res  = await fetch(`/search/data?ticker=${encodeURIComponent(ticker)}`);
        const data = await res.json();

        if (!res.ok) {
            showError(data.error ?? `No data found for ${ticker}.`);
            return;
        }

        const snapshots       = (data.snapshots ?? []).slice().reverse();
        const sentimentScores = (data.sentimentScores ?? []).slice().reverse();

        renderPriceChart(snapshots);
        renderAnalysisChart(snapshots, sentimentScores);

        document.getElementById('search-loading').classList.add('hidden');
        container.classList.remove('hidden');

        const el = document.getElementById('last-updated');
        el.textContent = 'Updated ' + new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        el.classList.remove('hidden');
    } catch {
        showError(`Failed to load data for ${ticker}.`);
    }
}

function renderPriceChart(snapshots) {
    const labels = snapshots.map(s =>
        new Date(s.timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
    );
    const prices = snapshots.map(s => parseFloat(s.price));

    if (priceChart) {
        priceChart.data.labels = labels;
        priceChart.data.datasets[0].data = prices;
        priceChart.update();
        return;
    }

    priceChart = new Chart(document.getElementById('price-chart'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: ticker,
                data: prices,
                borderColor: '#f97316',
                backgroundColor: 'transparent',
                borderWidth: 2,
                pointRadius: 0,
                tension: 0.3,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { ticks: { callback: v => '$' + v.toFixed(2) } } }
        }
    });
}

function renderAnalysisChart(snapshots, sentimentScores) {
    const labels  = snapshots.map(s =>
        new Date(s.timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
    );
    const prices  = snapshots.map(s => parseFloat(s.price));
    const base    = prices[0] || 1;
    const pctData = prices.map(p => parseFloat(((p - base) / base * 100).toFixed(2)));

    const sentimentRaw  = sentimentScores.map(s => parseFloat(s.score));
    const sentimentData = Array.from({ length: prices.length }, (_, i) => sentimentRaw[i] ?? null);

    if (analysisChart) {
        analysisChart.data.labels = labels;
        analysisChart.data.datasets[0].data = pctData;
        analysisChart.data.datasets[1].data = sentimentData;
        analysisChart.update();
        return;
    }

    analysisChart = new Chart(document.getElementById('pct-chart'), {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: '% Change',
                    data: pctData,
                    borderColor: '#3b82f6',
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    pointRadius: 0,
                    tension: 0.3,
                    yAxisID: 'y',
                },
                {
                    label: 'Sentiment',
                    data: sentimentData,
                    borderColor: '#f97316',
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    pointRadius: 0,
                    tension: 0.3,
                    yAxisID: 'y1',
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: true, position: 'top' } },
            scales: {
                y:  { ticks: { callback: v => v + '%' } },
                y1: {
                    position: 'right',
                    min: 0,
                    max: 100,
                    grid: { drawOnChartArea: false },
                }
            }
        }
    });
}

fetchData();
setInterval(fetchData, 5000);
