async function pullNotifications() {
    const res = await fetch('/notifications/poll');
    if (!res.ok) return;
    const notifications = await res.json();
    if (notifications.length === 0) return;

    const hype  = notifications.filter(n => n.type === 'hype').map(n => n.ticker);
    const crash = notifications.filter(n => n.type === 'crash').map(n => n.ticker);

    if (hype.length > 0) {
        const label = hype.join(', ');
        const verb  = hype.length === 1 ? 'is' : 'are';
        showToast(`${label} ${verb} hyping! 🚀 HCI alert triggered`, 'hype');
    }

    if (crash.length > 0) {
        const label = crash.join(', ');
        const verb  = crash.length === 1 ? 'is' : 'are';
        showToast(`${label} ${verb} crashing! 💀 HCI alert triggered`, 'crash');
    }
}

function showToast(message, type) {
    const container = document.getElementById('notification-container');
    const toast = document.createElement('div');

    toast.className = `px-4 py-3 rounded-lg shadow-lg text-white text-sm max-w-xs transition-opacity duration-500 ${type === 'hype' ? 'bg-green-600' : 'bg-red-600'}`;
    toast.textContent = message;
    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 500);
    }, 8000);
}

pullNotifications();
setInterval(pullNotifications, 15000);
