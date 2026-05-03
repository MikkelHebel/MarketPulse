import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

const container = document.getElementById('search-charts');
    if (!container) return;
    const ticker = container.dataset.ticker;

    (async function () {
        try {
            const res = await fetch(`/search/data?ticker=${encodeURIComponent(ticker)}`);
            const data = await res.json();

            const snapshots = data.snapshots ?? [];
            const prices = snapshots.map(s => parseFloat(s.price));
            const labels = snapshots.map(s =>
                new Date(s.timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
            );

            const base = prices[0] || 1;
            const pctData = prices.map(p => parseFloat(((p - base) / base * 100).toFixed(2)));

            new Chart(document.getElementById('price-chart'), {
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

            new Chart(document.getElementById('pct-chart'), {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: ticker,
                        data: pctData,
                        borderColor: '#3b82f6',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        pointRadius: 0,
                        tension: 0.3,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { ticks: { callback: v => v + '%' } } }
                }
            });

            document.getElementById('search-loading').classList.add('hidden');
            container.classList.remove('hidden');
        } catch {
            document.getElementById('search-loading').innerHTML =
                '<p class="text-red-400 text-lg">Failed to load data for ' + ticker + '.</p>';
        }
    })();
}
