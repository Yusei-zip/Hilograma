<?php
include '../includes/reutilizable/session_start.php';


$total_emp = 0;
$val_presentes = 0;
$val_ausentes = 0;
$porcentaje = 0;
$logs_borrados = [];
$actividad_reciente = [];

if ($pdo) {
    try {
        $fecha_hoy = date('Y-m-d');

        // Estadísticas
        $total_emp = $pdo->query("SELECT COUNT(*) FROM empleados")->fetchColumn();
        $total_emp_act = $pdo->query("SELECT COUNT(*) FROM empleados WHERE is_active = 1")->fetchColumn();

        $presentes_hoy = $pdo->prepare("SELECT COUNT(*) FROM asistencias a JOIN empleados e ON a.id_empleado = e.id 
        WHERE a.fecha = ? AND a.estado = 'Presente' AND e.is_active = '1'; ");
        $ausentes_hoy = $pdo->prepare("SELECT COUNT(*) FROM asistencias a JOIN empleados e ON a.id_empleado = e.id 
        WHERE a.fecha = ? AND a.estado = 'Ausente' AND e.is_active = '1'; ");
        $presentes_hoy->execute([$fecha_hoy]);
        $ausentes_hoy->execute([$fecha_hoy]);

        $val_presentes = $presentes_hoy->fetchColumn();
        

        $val_ausentes = $ausentes_hoy->fetchColumn();   
        $porcentaje = ($total_emp > 0) ? ($val_presentes / $total_emp_act) * 100 : 0;

        // Logs
        $logs_borrados = $pdo->query("SELECT * FROM logs_borrados ORDER BY fecha_borrado DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

        $actividad_reciente = $pdo->query("SELECT nombre_completo, creado_at, editado_at 
            FROM empleados 
            ORDER BY GREATEST(creado_at, editado_at) DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        // Si una consulta falla (ej. tabla inexistente), reseteamos $pdo
        $pdo = null;
    }
}
?>

<!DOCTYPE html>
<html lang="es" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <title>Dashboard | Sistema Asistencia</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <link rel="stylesheet" href="../public/css/dashboard.css">
    <link rel="icon" type="image/svg+xml" href="../public/img/favicon.svg">
    <style>
        /* Ajustes para pantalla completa */
        body {
            margin: 0;
            padding: 20px;
            display: block;
            /* Quitamos el flex centrado */
            background-color: var(--bg-body);
        }

        .main-container {
            width: 100%;
            max-width: 1400px;
            /* Un límite sano para monitores ultra-wide */
            margin: 0 auto;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            /* 2 columnas para stats/nav y 1 para logs */
            gap: 20px;
            margin-top: 20px;
        }

        /* Estilo para las secciones de logs */
        .activity-panel {
            background: var(--bg-card);
            border-radius: 12px;
            padding: 20px;
            border: 1px solid var(--border);
            height: fit-content;
        }

        .log-badge {
            font-size: 0.7rem;
            padding: 2px 8px;
            border-radius: 10px;
            text-transform: uppercase;
            font-weight: bold;
            margin-right: 10px;
        }

        .badge-new {
            background: rgba(0, 209, 178, 0.2);
            color: var(--accent);
        }

        .badge-edit {
            background: rgba(181, 137, 0, 0.2);
            color: #b58900;
        }

        @media (max-width: 900px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <div class="main-container">
        <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <div>
                <h1 style="margin: 0; color: var(--text-main);">🚀 Panel de Control</h1>
                <p style="color: var(--text-muted); margin: 5px 0 0 0;">Bienvenido, hoy es <?= date('d/m/Y') ?></p>
            </div>
            <button onclick="toggleTheme()" class="btn-icon"
                style="background: var(--bg-card); border: 1px solid var(--border); padding: 10px; border-radius: 50%; cursor: pointer;">
                <svg id="themeIcon" viewBox="0 0 24 24" style="width: 24px; fill: var(--accent);">
                    <path
                        d="M12,18C11.11,18 10.26,17.8 9.5,17.45C11.56,16.5 13,14.42 13,12C13,9.58 11.56,7.5 9.5,6.55C10.26,6.2 11.11,6 12,6A6,6 0 0,1 18,12A6,6 0 0,1 12,18M20,8.69V4H15.31L12,0.69L8.69,4H4V8.69L0.69,12L4,15.31V20H8.69L12,23.31L15.31,20H20V15.31L23.31,12L20,8.69Z" />
                </svg>
            </button>
        </header>

        <div class="dashboard-grid">

            <!--Información instantanea-->

            <div class="left-column">
                <div class="stats-grid"
                    style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 20px;">
                    <div class="stat-card card">
                        <p class="stat-label">Total Personal</p>
                        <h1 class="stat-value" style="color: var(--accent);"><?= $total_emp ?></h1>
                    </div>
                    <div class="stat-card card">
                        <p class="stat-label">Total Personal</p>
                        <h1 class="stat-value" style="color: var(--text-main);"><?= $total_emp_act?></h1>
                    </div>
                   
                    <div class="stat-card card">
                        <p class="stat-label">Presentes Hoy</p>
                        <h1 class="stat-value" style="color: var(--success);"><?= $val_presentes ?></h1>
                    </div>
                    <div class="stat-card card">
                        <p class="stat-label">Ausentes Hoy</p>
                        <h1 class="stat-value" style="color: var(--error);"><?= $val_ausentes ?></h1>
                    </div>
                </div>

                <div class="card progress-container" style="margin-bottom: 20px;">
                    <div class="progress-header">
                        <span style="color: var(--text-muted);">Tasa de asistencia:</span>
                        <span style="font-weight: bold; color: var(--accent);"><?= round($porcentaje, 1) ?>%</span>
                    </div>
                    <div class="progress-bar-bg"
                        style="background: var(--border); height: 12px; border-radius: 6px; overflow: hidden; margin-top: 10px;">
                        <div class="progress-fill"
                            style="width: <?= $porcentaje ?>%; background: var(--accent); height: 100%; transition: width 0.5s ease;">
                        </div>
                    </div>
                </div>

                <!--Botones de acceso rapido [Ver include]-->
                <?php include '../includes/dashboard/dashboard_buttons.php' ?>
            </div>


            <!--Actividad reciente-->

            <div class="right-column" style="display: flex; flex-direction: column; gap: 20px;">
                <section class="activity-panel">
                    <h3 style="margin-top:0; font-size: 1rem; display: flex; align-items: center; gap: 8px;">
                        ✨ Actividad Reciente
                    </h3>
                    <div class="log-list">
                        <?php foreach ($actividad_reciente as $act):
                            $es_nuevo = ($act['creado_at'] == $act['editado_at']);
                            $fecha_act = $es_nuevo ? $act['creado_at'] : $act['editado_at'];
                            ?>
                            <div class="log-item"
                                style="padding: 10px 0; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <span class="log-badge <?= $es_nuevo ? 'badge-new' : 'badge-edit' ?>">
                                        <?= $es_nuevo ? 'Nuevo' : 'Editado' ?>
                                    </span>
                                    <span style="font-size: 0.9rem;"><?= htmlspecialchars($act['nombre_completo']) ?></span>
                                </div>
                                <span
                                    style="font-size: 0.75rem; color: var(--text-muted);"><?= date('H:i', strtotime($fecha_act)) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>

                

                

                <section class="activity-panel" style="border-color: rgba(255, 56, 96, 0.3);">
                    <h3 style="margin-top:0; font-size: 1rem; color: #ff3860;">🗑️ Borrados Recientes</h3>
                    <div class="log-list">
                        <?php if ($logs_borrados): ?>
                            <?php foreach ($logs_borrados as $log): ?>
                                <div class="log-item"
                                    style="padding: 10px 0; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between;">
                                    <span
                                        style="font-size: 0.85rem; color: var(--text-muted);"><?= htmlspecialchars($log['detalle_registro']) ?></span>
                                    <span
                                        style="font-size: 0.75rem;"><?= date('H:i', strtotime($log['fecha_borrado'])) ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p style="color: var(--text-muted); font-size: 0.8rem; text-align: center;">Sin bajas recientes.
                            </p>
                        <?php endif; ?>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <script src="../public/js/loadtheme.js">//Cambiar el tema</script>
    <script src="../public/js/dashboard/footer.js">//Gestionar el footer</script>
    <?php
    // Verificación rápida de conexión
    $db_status = false;
    try {
        if ($pdo) {
            $db_status = true;
            $db_version = $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
        }
    } catch (Exception $e) {
        $db_status = false;
    }
    ?>
    <?php include '../includes/dashboard/dashboard_footer.php' ?>
</body>

</html>