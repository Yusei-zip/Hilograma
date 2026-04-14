<?php
session_start();

// Si no existe la sesión, lo mandamos al login de una
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit();
}

include_once '../config/db.php';

$stmtCargos = $pdo->query("SELECT * FROM cargos");
$cargos = $stmtCargos->fetchAll();

$stmtSub = $pdo->query("SELECT * FROM sub_cargos");
$subCargosBD = $stmtSub->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT e.*, c.nombre_cargo, 
        (SELECT COUNT(*) FROM asistencias 
         WHERE id_empleado = e.id 
         AND estado = 'Ausente' 
         AND MONTH(fecha) = MONTH(CURRENT_DATE()) 
         AND YEAR(fecha) = YEAR(CURRENT_DATE())) as faltas_mes
        FROM empleados e 
        LEFT JOIN cargos c ON e.id_cargo = c.id
        ORDER BY e.nombre_completo ASC";

$stmtEmp = $pdo->query($sql);
$empleados = $stmtEmp->fetchAll();

$meses = ["", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
?>

<!DOCTYPE html>
<html lang="es" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Personal | Asistencia</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <link rel="icon" type="image/svg+xml" href="../public/img/favicon.svg">
    <style>
        body {
            display: flex !important;
            flex-direction: column !important;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-color: var(--bg-body);
        }

        /* FIX: Permitir que los tooltips se vean fuera de la tabla */
        .card-full {
            width: 95%;
            max-width: 950px;
            margin: 0 auto 40px auto;
            box-sizing: border-box;
            overflow: visible !important;
            /* Vital para que no corte el hover */
        }

        table {
            border-collapse: collapse;
            width: 100%;
            overflow: visible !important;
        }

        td,
        th {
            overflow: visible !important;
            position: relative;
        }

        /* ESTILOS DE ESTADO */
        .status-badge {
            font-size: 0.65rem;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: bold;
            margin-left: 8px;
        }

        .status-1 {
            background: rgba(0, 209, 178, 0.15);
            color: var(--accent);
            border: 1px solid var(--accent);
        }

        .status-0 {
            background: rgba(255, 56, 96, 0.15);
            color: var(--error);
            border: 1px solid var(--error);
        }

        /* FIX: MODAL COMO POP-UP REAL */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.85);
            display: none;
            /* Se activa con JS */
            align-items: center;
            justify-content: center;
            z-index: 9999;
            /* Por encima de todo */
        }

        /* TOOLTIPS MEJORADOS */
        .wrapper-hover {
            position: relative;
            display: inline-block;
            cursor: help;
        }

        .tooltip-box {
            visibility: hidden;
            opacity: 0;
            position: absolute;
            bottom: 110%;
            left: 0;
            /* Alineado a la izquierda para evitar cortes */
            width: 200px;
            background: #1a1a1a;
            padding: 12px;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.8);
            z-index: 1000;
            transition: all 0.2s ease;
            border: 1px solid var(--border);
            pointer-events: none;
            /* Para que no estorbe al mover el mouse */
        }

        .wrapper-hover:hover .tooltip-box {
            visibility: visible;
            opacity: 1;
            transform: translateY(-10px);
        }

        /* Estilos de faltas (manteniendo tu lógica) */
        .faltas-wrapper {
            position: relative;
            display: inline-block;
            cursor: help;
        }

        .badge-faltas {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.05);
            font-size: 0.8rem;
            font-weight: bold;
        }

        .faltas-tooltip {
            visibility: hidden;
            opacity: 0;
            position: absolute;
            bottom: 110%;
            right: 0;
            width: 220px;
            background: #1a1a1a;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.8);
            z-index: 1000;
            transition: all 0.2s ease;
            border: 1px solid var(--border);
            color: #fff;
        }

        .faltas-wrapper:hover .faltas-tooltip {
            visibility: visible;
            opacity: 1;
            transform: translateY(-10px);
        }
    </style>
</head>

<body>
    <?php include_once 'navbar.php'; ?>

    <div class="card card-full">
        <div style="margin-bottom: 25px; border-bottom: 1px solid var(--border); padding-bottom: 10px;">
            <h2 style="margin: 0; color: var(--accent);">👥 Gestión de Personal</h2>
        </div>

        <?php if (isset($_SESSION['msg'])): ?>
            <div id="alert-msg" class="msg-alert msg-<?= $_SESSION['msg_type'] ?>">
                <?= $_SESSION['msg']; ?>
                <?php unset($_SESSION['msg'], $_SESSION['msg_type']); ?>
            </div>
        <?php endif; ?>

        <form action="../logic/empleado_controller.php" method="POST" onsubmit="return prepararEnvio()"
            style="display: grid; grid-template-columns: 1.5fr 1fr 1fr 1fr auto; gap: 15px; align-items: end;">
            <div class="form-group"><label>Nombre</label><input type="text" name="nombre_completo" required></div>
            <div class="form-group"><label>Teléfono</label><input type="number" name="numero"></div>
            <div class="form-group">
                <label>Cargo</label>
                <select name="id_cargo" id="cargoSelect" onchange="checkSubCargos()" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($cargos as $cargo): ?>
                        <option value="<?= $cargo['id'] ?>"><?= $cargo['nombre_cargo'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Estado</label>
                <select name="is_active">
                    <option value="1">Contratado</option>
                    <option value="0">No Contratado</option>
                </select>
            </div>
            <input type="hidden" name="sub_cargos_final" id="sub_cargos_final">
            <button type="submit" name="guardar_empleado" class="btn btn-primary" style="height: 45px;">Añadir</button>
        </form>

        <div style="margin-top: 35px;">
            <table style="width: 100%;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border); text-align: left;">
                        <th style="padding: 15px;">Nombre / Cargo</th>
                        <th style="padding: 15px;">Contacto</th>
                        <th style="padding: 15px; text-align: center;">Ausencias (Mes)</th>
                        <th style="padding: 15px; text-align: right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($empleados as $e):
                        $stmtHist = $pdo->prepare("SELECT MONTH(fecha) as mes_num, YEAR(fecha) as anio, COUNT(*) as total FROM asistencias WHERE id_empleado = ? AND estado = 'Ausente' GROUP BY anio, mes_num ORDER BY anio DESC, mes_num DESC");
                        $stmtHist->execute([$e['id']]);
                        $historial_faltas = $stmtHist->fetchAll();
                        ?>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <td style="padding: 15px;">
                                <div style="display: flex; align-items: center;">
                                    <strong><?= htmlspecialchars($e['nombre_completo']) ?></strong>
                                    <span
                                        class="status-badge status-<?= $e['is_active'] ?>"><?= $e['is_active'] ? 'NÓMINA' : 'BAJA' ?></span>
                                </div>
                                <div class="wrapper-hover">
                                    <small style="color: var(--accent);"><?= $e['nombre_cargo'] ?? 'Sin asignar' ?></small>
                                    <?php if (!empty($e['sub_cargos'])): ?>
                                        <div class="tooltip-box">
                                            <div style="font-size: 0.7rem; color: var(--accent); margin-bottom: 5px;">Máquinas:
                                            </div>
                                            <div style="font-size: 0.75rem; color: #eee;"><?= $e['sub_cargos'] ?></div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td style="padding: 15px; font-family: monospace;"><?= $e['numero'] ?></td>

                            <td style="padding: 15px; text-align: center;">
                                <?php $f = $e['faltas_mes'];
                                $color = ($f >= 3) ? 'var(--error)' : (($f > 0) ? '#ff9800' : 'var(--success)'); ?>
                                <div class="faltas-wrapper">
                                    <div class="badge-faltas"
                                        style="border: 1px solid <?= $color ?>; color: <?= $color ?>;">
                                        <?= $f ?>     <?= ($f == 1) ? 'falta' : 'faltas' ?>
                                    </div>
                                    <div class="faltas-tooltip">
                                        <h4
                                            style="margin: 0 0 10px 0; font-size: 0.8rem; border-bottom: 1px solid var(--border); padding-bottom: 5px; color: var(--accent);">
                                            Historial de Ausencias</h4>
                                        <?php foreach ($historial_faltas as $hf): ?>
                                            <div
                                                style="display: flex; justify-content: space-between; font-size: 0.75rem; margin-bottom: 5px;">
                                                <span><?= $meses[$hf['mes_num']] ?>         <?= $hf['anio'] ?>:</span>
                                                <strong><?= $hf['total'] ?></strong>
                                                <a href="historial.php?id_empleado=<?= $e['id'] ?>&mes=<?= $hf['mes_num'] ?>"
                                                    class="mini-link">ver</a>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </td>

                            <td style="padding: 15px; text-align: right; white-space: nowrap;">
                                <a href="historial.php?id_empleado=<?= $e['id'] ?>"
                                    style="color: var(--accent); margin-right: 12px;" title="Ver registros">
                                    <svg viewBox="0 0 24 24" width="20">
                                        <path fill="currentColor"
                                            d="M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9M12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z" />
                                    </svg>
                                </a>
                                <a href="editar_empleado.php?id=<?= $e['id'] ?>"
                                    style="color: var(--text-main); margin-right: 12px;" title="Editar">
                                    <svg viewBox="0 0 24 24" width="20" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                </a>
                                <a href="../logic/empleado_controller.php?delete=<?= $e['id'] ?>"
                                    style="color: var(--error);" onclick="return confirm('¿Eliminar?')">
                                    <svg viewBox="0 0 24 24" width="20">
                                        <path fill="currentColor"
                                            d="M19,4H15.5L14.5,3H9.5L8.5,4H5V6H19V4M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6V19Z" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="modalSub" class="modal-overlay">
        <div class="card" style="max-width: 400px; width: 90%; position: relative;">
            <h3 style="color: var(--accent); margin-top: 0;">Especialidades</h3>
            <p style="font-size: 0.8rem; color: var(--text-muted);">Seleccione las áreas que opera este trabajador:</p>
            <div id="listaSubCargos" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin: 20px 0;">
            </div>
            <button type="button" onclick="cerrarModal()" class="btn btn-primary" style="width: 100%;">Confirmar
                Selección</button>
        </div>
    </div>

    <script>
        const subCargosBD = <?php echo json_encode($subCargosBD); ?>;

        function checkSubCargos() {
            const cargoId = document.getElementById('cargoSelect').value;
            const filtrados = subCargosBD.filter(s => s.id_cargo_padre == cargoId);
            const container = document.getElementById('listaSubCargos');

            if (filtrados.length > 0) {
                container.innerHTML = "";
                filtrados.forEach(sub => {
                    container.innerHTML += `
                        <label style="font-size: 0.85rem; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            ${sub.nombre_subcargo}<input type="checkbox" class="sub-check" value="${sub.nombre_subcargo}"> 
                        </label>`;
                });
                document.getElementById('modalSub').style.display = 'flex'; // Aquí se muestra
            } else {
                document.getElementById('sub_cargos_final').value = "";
            }
        }

        function cerrarModal() {
            const selected = Array.from(document.querySelectorAll('.sub-check:checked')).map(c => c.value);
            document.getElementById('sub_cargos_final').value = selected.join(', ');
            document.getElementById('modalSub').style.display = 'none'; // Aquí se oculta
        }

        function prepararEnvio() { return true; }

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
    </script>
</body>

</html>