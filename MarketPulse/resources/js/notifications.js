async function pullNotifications() {
    const res = await fetch('/notifications/poll');
    if (!res.ok) return;
    const notifications = await res.json();
    notifications.forEach(showToast);
}

function showToast(notification) {
    const container = document.getElementById('notification-container');
    const isHype = notification.type === 'hype';
    const toast = document.createElement('div');

    toast.className = `px-4 py-3 rounded-lg shadow-lg text-white text-sm max-w-xs transition-opacity duration-500 ${isHype ? 'bg-orange-500' : 'bg-red-600'}`;
    toast.textContent = notification.message;
    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 500);
    }, 15000);
}

pullNotifications();
setInterval(pullNotifications, 10000);
