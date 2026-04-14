<?php require_once '../logic/historial_controller.php'; ?>
<!DOCTYPE html>
<html lang="es" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Asistencias</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <link rel="icon" type="image/svg+xml" href="../public/img/favicon.svg">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
        /* Esto evitará que los botones y filtros salgan en el PDF */
        .no-export {
            display: none !important;
        }
    </style>
    <style>
        body {
            display: flex !important;
            flex-direction: column !important;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-color: var(--bg-body);
        }

        /* Recuperamos tus estilos de modal y efectos */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(3px);
        }

        .modal-content {
            width: 90%;
            max-width: 450px;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .page-link.active {
            background: var(--accent);
            color: #000;
            font-weight: bold;
        }

        .page-link.disabled {
            opacity: 0.5;
            pointer-events: none;
        }

        /* Estilos para la barra QoL */
        .qol-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 20px;
            border-top: 1px solid var(--border);
            padding-top: 15px;
        }

        table tr {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        /* Opcional: Evita que el encabezado se separe de la primera fila */
        thead {
            display: table-header-group;
        }

         .msg-green {
            background: var(--success);
            color: #000;
        }

        .msg-red {
            background: var(--error);
            color: #fff;
        }

        .msg-success {
            background-color: rgba(0, 209, 178, 0.15);
            color: #00d1b2;
            /* Tu cyan favorito */
            border-color: #00d1b2;
        }
    </style>
</head>

<body>
    <?php include_once 'navbar.php'; ?>

    <div class="card" style="max-width: 900px;">
        <div id="pdf-header"
            style="display: none; margin-bottom: 20px; border-bottom: 2px solid var(--accent); padding-bottom: 10px;">
            <h1 style="margin: 0; color: var(--accent);">Reporte de Asistencias</h1>
            <p style="margin: 5px 0; color: var(--text-muted);">Generado el: <span id="pdf-fecha"></span></p>
            <p id="pdf-filtro-info" style="font-weight: bold;"></p>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <h2 style="margin: 0;">📋 Historial</h2>
        </div>

        <?php if (isset($_SESSION['msg'])): ?>
            <div id="alert-msg" class="msg-alert msg-<?= $_SESSION['msg_type'] ?>">
                <?= $_SESSION['msg']; ?>
                <?php unset($_SESSION['msg'], $_SESSION['msg_type']); ?>
            </div>
        <?php endif; ?>

        <form method="GET" style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 25px; align-items: center;">
            <select name="id_empleado" style="flex: 1; min-width: 150px;">
                <option value="">Todos los empleados</option>
                <?php if (!empty($empleados)): ?>
                    <?php foreach ($empleados as $emp): ?>
                        <option value="<?= $emp['id'] ?>" <?= ($id_empleado == $emp['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($emp['nombre_completo']) ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>

            <select name="estado" style="flex: 1; min-width: 150px;">
                <option value="">Cualquier Estado</option>
                <option value="Presente" <?= $estado == 'Presente' ? 'selected' : '' ?>>Presente</option>
                <option value="Ausente" <?= $estado == 'Ausente' ? 'selected' : '' ?>>Ausente</option>
            </select>
            <select name="mes" style="flex: 1; min-width: 150px;">
                <option value="">Todos los meses</option>
                <?php
                $meses = [
                    1 => "Enero",
                    2 => "Febrero",
                    3 => "Marzo",
                    4 => "Abril",
                    5 => "Mayo",
                    6 => "Junio",
                    7 => "Julio",
                    8 => "Agosto",
                    9 => "Septiembre",
                    10 => "Octubre",
                    11 => "Noviembre",
                    12 => "Diciembre"
                ];
                foreach ($meses as $num => $nombre): ?>
                    <option value="<?= $num ?>" <?= $mes == $num ? 'selected' : '' ?>><?= $nombre ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btn btn-primary">Filtrar</button>
            <a href="historial.php" class="btn"
                style="background: var(--input-bg); color: var(--text-main); text-decoration: none;">Limpiar</a>

            <div style="display: flex; gap: 10px;">
                <button type="button" onclick="exportarExcel()" class="btn"
                    style="background: #2e7d32; color: white;">Excel</button>
                <button type="button" onclick="generarPDF()" class="btn" style="background: #c62828; color: white;">PDF
                    / Imprimir</button>
            </div>
        </form>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border); text-align: left;">
                        <th style="padding: 12px; color: var(--text-muted);">Fecha</th>
                        <th style="padding: 12px; color: var(--text-muted);">Empleado</th>
                        <th style="padding: 12px; color: var(--text-muted);">Estado</th>
                        <th style="padding: 12px; color: var(--text-muted);">Detalle</th>
                        <th style="padding: 12px; text-align: center; color: var(--text-muted);">Teléfono</th>
                        <th style="padding: 12px; text-align: right; color: var(--text-muted);">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($registros):
                        foreach ($registros as $reg): ?>
                            <tr style="border-bottom: 1px solid var(--border);">
                                <td style="padding: 12px;"><?= date('d/m/Y', strtotime($reg['fecha'])) ?></td>
                                <td style="padding: 12px;"><strong><?= htmlspecialchars($reg['nombre_completo']) ?></strong>
                                </td>
                                <td style="padding: 12px;">
                                    <span class="msg-alert"
                                        style="padding: 2px 8px; font-size: 0.75rem; background: <?= $reg['estado'] == 'Presente' ? 'var(--success)' : 'var(--error)' ?>; color: #000;">
                                        <?= $reg['estado'] ?>
                                    </span>
                                </td>
                                <td style="padding: 12px; color: var(--text-muted); font-style: italic;">
                                    <?= $reg['sub_estado'] != 'Ninguno' ? $reg['sub_estado'] : '-' ?>
                                </td>
                                <td style="padding: 12px; text-align: center;"><?= $reg['telefono_entregado'] ? '✅' : '❌' ?>
                                </td>
                                <td
                                    style="padding: 12px; text-align: right; display: flex; gap: 8px; justify-content: flex-end;">
                                    <?php if (!empty($reg['observaciones'])): ?>
                                        <button onclick="openModal('<?= addslashes(htmlspecialchars($reg['observaciones'])) ?>')"
                                            class="btn-icon">👁️</button>
                                    <?php endif; ?>
                                    <a href="editar_asistencia.php?id=<?= $reg['id'] ?>" class="btn-icon">✏️</a>
                                    <a href="../logic/asistencia_controller.php?delete_id=<?= $reg['id'] ?>" class="btn-icon"
                                        onclick="return confirm('¿Borrar?')">🗑️</a>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                        <tr>
                            <td colspan="6" style="padding: 30px; text-align: center; color: var(--text-muted);">No se
                                encontraron registros.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php include_once '../includes/historial/paginacion.php' ?>
        </div>
    </div>

    <div id="obsModal" class="modal-overlay" onclick="closeModal(event)">
        <div class="modal-content card" onclick="event.stopPropagation()">
            <h3>📝 Observaciones</h3>
            <p id="obsText" style="margin: 20px 0; line-height: 1.6;"></p>
            <button onclick="closeModal()" class="btn btn-primary">Cerrar</button>
        </div>
    </div>

    <?php include '../public/js/historial/generarPDF.php' ?>
    <script src="../public/js/generarPDF"></script>
    <script src="../public/js/alert-msg.js">//Cambiar el tema</script>
</body>

</html>