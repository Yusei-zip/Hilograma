<head>
    <style>
    .main-nav {
        max-width: 1100px;
        margin: 20px auto;
        padding: 0.8rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: var(--bg-card);
        border: 1px solid var(--success);
        border-radius: 12px;
        box-shadow: 0 4px 1px var(--accent, 0, 0, 0, 0.3);
    }

    .nav-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
    }

    .nav-brand span {
        font-weight: 800;
        color: var(--accent);
        letter-spacing: 1px;
        font-size: 1.1rem;
    }

    .nav-links {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    /* Reutilizamos y mejoramos tu clase .page-link */
    .nav-item {
        text-decoration: none;
        color: var(--text-muted);
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 600;
        transition: all 0.3s ease;
        border: 1px solid transparent;
    }

    .nav-item:hover {
        color: var(--accent);
        background: rgba(187, 134, 252, 0.1);
    }

    .nav-item.active {
        background: var(--accent);
        color: var(--btn-text) !important;
        box-shadow: 0 4px 12px rgba(187, 134, 252, 0.3);
    }

    .nav-actions {
        display: flex;
        align-items: center;
        gap: 15px;
        padding-left: 15px;
        border-left: 1px solid var(--border);
    }

    .nav-icon {
        color: var(--text-muted);
        transition: color 0.2s;
        display: flex;
        align-items: center;
    }

    .nav-icon:hover {
        color: var(--error);
        /* Color de alerta para el terminal maestro */
    }

    .nav-icon svg {
        width: 20px;
        height: 20px;
        fill: currentColor;
    }
</style>
</head>

<script src="../public/js/loadtheme.js">//Cambiar el tema</script>
<nav class="main-nav">
    <a href="dashboard.php" class="nav-brand">
        <svg viewBox="0 0 24 24" style="width:28px; fill:var(--accent);">
            <path d="M12 2L2 12h3v8h6v-6h2v6h6v-8h3L12 2z" />
        </svg>
        <span>PART S.A</span>
    </a>
    <div class="nav-links">
        <?php
        $current_page = basename($_SERVER['PHP_SELF']);
        $menu = [
            'dashboard.php' => 'Resumen',
            'dashboard_empleados.php' => 'Taller',
            'empleados.php' => 'Empleados',
            'asistencias.php' => 'Asistencia',
            'asistencia_rapida.php' => 'Carga',
            'historial.php' => 'Registros',
        ];
        foreach ($menu as $url => $label):
            ?>
            <a href="<?= $url ?>" class="nav-item <?= $current_page == $url ? 'active' : '' ?>">
                <?= $label ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="nav-actions">
        <div class="nav-icon" title="Hora Actual"
            style="display: flex; align-items: center; gap: 5px; cursor: default; background: none; border: none; color: var(--text-main);">
            <svg viewBox="0 0 24 24" style="width: 20px;">
                <path
                    d="M12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22C6.47,22 2,17.53 2,12A10,10 0 0,1 12,2M12.5,7V12.25L17,14.92L16.25,16.15L11,13V7H12.5Z" />
            </svg>
            <span id="reloj-digital"
                style="font-size: 0.85rem; font-weight: bold; font-family: monospace;">00:00:00</span>
        </div>

        <?php include '../includes/themeToggler.php' ?>

        <a href="maintenance.php?key=yusei123" class="nav-icon" title="Mantenimiento">
            <svg viewBox="0 0 24 24">
                <path
                    d="M22.7 19l-9.1-9.1c.9-2.3.4-5-1.5-6.9-2-2-5-2.4-7.4-1.3L9 6 6 9 1.6 4.7C.5 7.1.9 10.1 2.9 12.1c1.9 1.9 4.6 2.4 6.9 1.5l9.1 9.1c.4.4 1 .4 1.4 0l2.3-2.3c.5-.4.5-1.1.1-1.4z" />
            </svg>
        </a>

        <a href="http://localhost/phpmyadmin/index.php?route=/database/structure&db=gestor_asistencia" class="nav-icon"
            title="Base de Datos" target="_blank" onclick="return confirm('¿Desea ver la base de datos?')">
            <svg viewBox="0 0 24 24">
                <path
                    d="M12,3C7.58,3 4,4.79 4,7C4,9.21 7.58,11 12,11C16.42,11 20,9.21 20,7C20,4.79 16.42,3 12,3M4,9V12C4,14.21 7.58,16 12,16C16.42,16 20,14.21 20,12V9C20,11.21 16.42,13 12,13C7.58,13 4,11.21 4,9M4,14V17C4,19.21 7.58,21 12,21C16.42,21 20,19.21 20,17V14C20,16.21 16.42,18 12,18C7.58,18 4,16.21 4,14Z" />
            </svg>
        </a>

        <a href="#" class="nav-icon" title="Actualizar" onclick="location.reload()">
            <svg viewBox="0 0 24 24">
                <path
                    d="M12,18A6,6 0 0,1 6,12C6,11 6.25,10.03 6.7,9.2L5.24,7.74C4.46,8.97 4,10.43 4,12A8,8 0 0,0 12,20V23L16,19L12,15V18M12,4V1L8,5L12,9V6A6,6 0 0,1 18,12C18,13 17.75,13.97 17.3,14.8L18.76,16.26C19.54,15.03 20,13.57 20,12A8,8 0 0,0 12,4Z" />
            </svg>
        </a>


        <a href="../logic/logout.php" class="nav-icon" title="Cerrar Sesión"
            onclick="return confirm('¿Cerrar sesión?')">
            <svg viewBox="0 0 24 24">
                <path
                    d="M13 3h-2v10h2V3zm4.83 2.17l-1.42 1.42A6.92 6.92 0 0 1 19 12c0 3.87-3.13 7-7 7s-7-3.13-7-7a6.92 6.92 0 0 1 2.59-5.41L6.17 5.17A8.99 8.99 0 0 0 3 12c0 4.97 4.03 9 9 9s9-4.03 9-9c0-2.74-1.23-5.18-3.17-6.83z" />
            </svg>
        </a>

    </div>
</nav>



<script>

    function actualizarReloj() {
        const ahora = new Date();
        const horas =
            String(ahora.getHours()).padStart(2, '0'
            );
        const minutos = String(ahora.getMinutes()).padStart(2, '0');
        const segundos = String(ahora.getSeconds()).padStart(2, '0');

        const reloj = document.getElementById('reloj-digital');
        if (reloj) {
            reloj.textContent = `${horas}:${minutos}:${segundos}`;
        }
    }

    setInterval(actualizarReloj, 1000);

    actualizarReloj();
</script>