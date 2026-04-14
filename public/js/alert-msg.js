document.addEventListener('DOMContentLoaded', () => {
    const savedTheme = localStorage.getItem('theme') || 'dark';
    document.documentElement.setAttribute('data-theme', savedTheme);
});

document.addEventListener('DOMContentLoaded', () => {
    const alertMsg = document.getElementById('alert-msg');
    if (alertMsg) {
        // Espera 3 segundos (3000ms) y luego cambia la opacidad
        setTimeout(() => {
            alertMsg.style.opacity = '0';
            // Después de la transición de desvanecimiento, lo elimina del espacio
            setTimeout(() => {
                alertMsg.style.display = 'none';
            }, 500);
        }, 3000);
    }
});