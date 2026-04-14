<?php

include '../includes/reutilizable/session_start.php';
require_once '../logic/empleados_dashboard_controller.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Dashboard General de Personal</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <link rel="icon" type="image/svg+xml" href="../public/img/favicon.svg">
    <script src="../public/js/loadtheme.js">//Cambiar el tema</script>
    <style>
        /* Contenedor de tarjetas que se adapta a cuántos cargos haya */
        .stats-grid {
      
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 10fr));
            gap: 20px;
            margin-bottom: 40px;
            max-width: 1000px;
            width: 90%;
        }

        .card-stat {
            background: var(--bg-card);
                justify-content: center;
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 25px;
            text-align: center;
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }

        .card-stat:hover {
            border-color: var(--accent);
            transform: scale(1.05);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        /* Indicador visual de cargo activo */
        .card-stat.active {
            border-color: var(--accent);
            background: rgba(255, 255, 255, 0.05);
        }

        .card-stat h4 {
            font-size: 0.8rem;
            color: var(--text-muted);
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .card-stat .count {
            font-size: 2.5rem;
            font-weight: 900;
            color: var(--accent);
        }

        .sub-header {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 15px;
            border-left: 3px solid var(--accent);
            padding-left: 10px;
        }

        .badge-container {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }

        .badge-sub {
            background: var(--bg-card);
            padding: 8px 18px;
            border-radius: 12px;
            border: 1px solid var(--border);
            font-size: 0.85rem;
        }

        .table-glass {
            width: 100%;
            background: var(--bg-card);
            border-radius: 15px;
            border-collapse: collapse;
            overflow: hidden;
            border: 1px solid var(--border);
            text-align: center;
        }

        .table-glass th {
            text-align: left;
            padding: 18px;
            background: rgba(0, 0, 0, 0.2);
            color: var(--text-muted);
            font-size: 0.75rem;
            text-align: center;
        }

        .table-glass td {
            padding: 18px;
            border-bottom: 1px solid var(--border);
            color: var(--text-main);
            text-align: center;
        }
    </style>
</head>

<body>
  

    <div class="dashboard-container">
         <?php include_once 'navbar.php'; ?>
         

        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 25px;">
            <div>
                <h1 style="margin:0; color: var(--text-main);">Personal por Cargo</h1>
                <p style="color: var(--text-muted); margin: 5px 0 0 0;">Haz clic en una tarjeta para filtrar la lista
                </p>
            </div>
            <?php if ($cargo_filtrado): ?>
                <a href="dashboard_empleados.php"
                    style="color: var(--accent); text-decoration: none; font-weight: bold; border-bottom: 1px solid;">Ver
                    todos los trabajadores</a>
            <?php endif; ?>
        </div>


        <div class="stats-grid">
            <?php foreach ($counts as $c): ?>
                <a href="?id_cargo=<?= urlencode($c['id_cargo']) ?>"
                    class="card-stat <?= ($cargo_filtrado == $c['id_cargo']) ? 'active' : '' ?>">
                    <h4><?= htmlspecialchars($c['nombre_cargo']) //Aqui se gestiona el nombre del cargo ?></h4>
                    <div class="count"><?= $c['total'] ?></div>
                </a>
            <?php endforeach; ?>
        </div>


        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 25px;">
            <div>
                <h1 style="margin:0; color: var(--text-main);">Personal por Cargo Activo</h1>
                <p style="color: var(--text-muted); margin: 5px 0 0 0;"></p>
            </div>
        </div>

        <div class="stats-grid">
            <?php foreach ($counts_act as $c): ?>
                <a href="?id_cargo=<?= urlencode($c['id_cargo']) ?>"
                    class="card-stat <?= ($cargo_filtrado_act == $c['id_cargo']) ? 'active' : '' ?>">
                    <h4><?= htmlspecialchars($c['nombre_cargo']) //Aqui se gestiona el nombre del cargo ?></h4>
                    <div class="count"><?= $c['total'] ?></div>
                </a>
            <?php endforeach; ?>
        </div>

    </div>

    <div class="dashboard-container">


        <h3 class="sub-header">DESGLOSE DETALLADO / TURNO</h3>
        <!--
        <div class="badge-container">
            <?php foreach ($subcounts as $s): ?>
                <div class="badge-sub">
                    <span style="color: var(--text-muted);"><?= htmlspecialchars($s['sub_cargos']) ?>:</span>
                    <strong style="color: var(--accent);"><?= $s['total'] ?></strong>
                </div>
            <?php endforeach; ?>
        </div>
        -->

        <div style="overflow-x: auto;">
            <table class="table-glass">
                <thead>
                    <tr>
                        <th>TRABAJADOR</th>
                        <th>CARGO</th>
                        <th>SUB CARGOS</th>
                        <th>ESTADO</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($empleados as $e): ?>
                        <tr>
                            <td style="font-weight: 600;"><?= htmlspecialchars($e['nombre_completo']) ?></td>
                            <td><?= htmlspecialchars($e['nombre_cargo']) ?></td>
                            <td style="color: var(--text-muted);">
                                <?= htmlspecialchars($e['sub_cargos'] ?: 'Sin sub cargos') ?>
                            </td>
                            <td>

                                <?php
                                $f = $e['is_active'];
                                $texto;
                                if ($f == 1) {
                                    $texto = 'var(--btn-text)';
                                    $fondo = 'var(--success)';
                                } else if ($f == 0) {
                                    $texto = 'var(--accent)';
                                    $fondo = 'var(--error)';
                                }

                                ?>
                                <span
                                    style="color: 1px solid <?= $texto ?>; color: <?= $texto ?>; background:<?= $fondo ?>; color: <?= $fondo ?>; 
                        padding: 4px 10px; border-radius: 4px; font-size: 0.7rem; font-weight: bold; background-clip:text;"
                                    <?= $e['is_active'] ?>"><?= $e['is_active'] ? 'NÓMINA' : 'BAJA' ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>