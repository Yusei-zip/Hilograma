//Footer y Toast del dashboard :]
function showToast(mensaje, tipo) {
    const toast = document.createElement('div');
    toast.className = `toast toast-${tipo}`;
    toast.innerHTML = `
        <div style="display: flex; align-items: center; gap: 10px;">
            <span>${tipo === 'success' ? '✅' : '❌'}</span>
            <span>${mensaje}</span>
        </div>
    `;
    document.body.appendChild(toast);

    // Animación de entrada y salida
    setTimeout(() => toast.classList.add('show'), 100);
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

function updateFooterClock() {
    const clockElement = document.getElementById('footer-clock');
    if (!clockElement) return;

    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');

    clockElement.textContent = `${hours}:${minutes}:${seconds}`;
}

// Iniciar reloj y actualizar cada segundo
setInterval(updateFooterClock, 1000);
updateFooterClock();
