<?php
session_start();

// Si no existe la sesión, lo mandamos al login de una
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit();
}

include_once '../config/db.php';
/*if (!isset($_GET['key']) || $_GET['key'] !== 'yusei123') { header("Location: ../index.php"); exit; }*/

// Conteos para el monitor
$countEmp = $pdo->query("SELECT COUNT(*) FROM empleados")->fetchColumn();
$countAsis = $pdo->query("SELECT COUNT(*) FROM asistencias")->fetchColumn();
$dbSize = $pdo->query("SELECT SUM(data_length + index_length) / 1024 / 1024 FROM information_schema.TABLES WHERE table_schema = (SELECT DATABASE())")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="es" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <title>Terminal Maestro | Admin</title>

    <link rel="stylesheet" href="../public/css/style.css">
    <style>
        .card {
            max-width: 950px;
            width: 95%;
            margin: 20px auto;
        }

        .grid-master {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 25px;
        }

        .stat-box {
            background: var(--input-bg);
            border: 1px solid var(--border);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }

        .stat-val {
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--accent);
            display: block;
        }

        .stat-label {
            font-size: 0.7rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .section-maint {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border);
            padding: 25px;
            border-radius: 12px;
            box-sizing: border-box;
        }

        .section-title {
            color: var(--accent);
            font-size: 0.9rem;
            font-weight: bold;
            margin-bottom: 20px;
            display: block;
            border-bottom: 1px solid var(--border);
            padding-bottom: 8px;
            text-transform: uppercase;
        }

        /* Contenedor de botones para que no se peguen */
        .button-stack {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        /* Área de descarga independiente */
        .backup-zone {
            margin-top: 10px;
            background: rgba(187, 134, 252, 0.07);
            border: 1px solid var(--accent);
            padding: 25px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            width: 100%;
            box-sizing: border-box;
        }

        .sql-zone {
            margin-top: 10px;

            background: rgba(252, 165, 134, 0.07);
            border: 1px solid var(--error);
            padding: 25px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            width: 100%;
            box-sizing: border-box;
        }

        .dash-zone {
            margin-top: 10px;

            background: rgba(134, 252, 244, 0.07);
            border: 1px solid var(--success);
            padding: 25px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            width: 100%;
            box-sizing: border-box;
        }

        .theme-zone {
            margin-top: 10px;

            background: rgba(134, 252, 244, 0.07);
            border: 1px solid var(--success);
            padding: 25px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            width: 100%;
            box-sizing: border-box;
        }

        @media (max-width: 768px) {
            .backup-zone {
                flex-direction: column;
                text-align: center;
            }

            .backup-zone button {
                width: 100%;
            }
        }
    </style>
</head>

<body>

<?php if (isset($_SESSION['msg'])): ?>
            <div id="alert-msg" class="msg-alert msg-<?= $_SESSION['msg_type'] ?>">
                <?= $_SESSION['msg']; ?>
                <?php unset($_SESSION['msg'], $_SESSION['msg_type']); ?>
            </div>
        <?php endif; ?>

    <script src="../public/js/loadtheme.js">//Cambiar el tema</script>

    <div class="card">
        <h1 style="text-align: center; color: var(--error); margin-bottom: 30px;">Terminal</h1>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 30px;">
            <div class="stat-box"><span class="stat-val"><?= $countEmp ?></span><span class="stat-label">Personal</span>
            </div>
            <div class="stat-box"><span class="stat-val"><?= $countAsis ?></span><span
                    class="stat-label">Registros</span></div>
            <div class="stat-box"><span class="stat-val"><?= number_format($dbSize, 2) ?> MB</span><span
                    class="stat-label">Peso BD</span></div>
        </div>

        <div
            style="display: flex; align-items: center; gap: 12px; background: var(--input-bg); padding: 15px; border-radius: 8px; border: 1px solid var(--border); margin-bottom: 30px;">
            <input type="checkbox" id="masterSwitch" style="width: 20px; height: 20px; cursor: progress;">
            <label for="masterSwitch" style="margin:0; font-size: 0.9rem; cursor:pointer; font-weight: 600;">Habilitar
                protocolos de borrado físico</label>
        </div>

        <form action="../logic/maintenance_controller.php" method="POST" id="formMaster">
            <div class="grid-master">

                <div class="section-maint">
                    <span class="section-title">📦 Sistema y Limpieza</span>
                    <div class="button-stack">
                        <button type="submit" name="action" value="optimize" class="btn"
                            style="background: #3b82f6; color: white;">Optimizar Tablas</button>
                        <button type="submit" name="action" value="reset_ai" class="btn"
                            style="background: #3b82f6; color: white;">Reiniciar IDs</button>

                        <div style="height: 1px; background: var(--border); margin: 5px 0;"></div>

                        <button style="cursor:not-allowed" type="submit" name="action" value="clear_attendance"
                            class="btn btn-red critical" disabled
                            onclick="return confirm('¿Purgar asistencias?')">Purgar Asistencias</button>
                        <button style="cursor:not-allowed" type="submit" name="action" value="clear_workers"
                            class="btn btn-red critical" disabled
                            onclick="return confirm('¿Eliminar personal?')">Eliminar Personal</button>
                    </div>
                </div>

                <div class="section-maint">
                    <span class="section-title">🧪 Inyección de Datos</span>
                    <label style="display: block; margin-bottom: 10px;">Cantidad: <span id="qty"
                            style="color: var(--success); font-weight: bold;">1</span></label>
                    <input type="range" id="range" min="1" max="1000" value="1"
                        style="width: 100%; margin-bottom: 20px; cursor:grab;">

                    <label style="display: block; margin-bottom: 8px;">Modo de simulación:</label>
                    <select id="modoSelect"
                        style="width: 100%; padding: 10px; margin-bottom: 20px; background: var(--input-bg); color: var(--text-main); border: 1px solid var(--border); border-radius: 8px;">
                        <option value="normal">Normal (Baja inasistencia)</option>
                        <option value="caotico">Caótico (Muchas faltas/alertas)</option>
                    </select>

                    <button type="button" onclick="runSeed()" class="btn btn-green" style="width: 100%;">Ejecutar
                        Simulación</button>
                </div>
            </div>
        </form>

        <div class="theme-zone">
            <div style="text-align: left;">
                <span
                    style="display: block; font-weight: bold; color: var(--accent); margin-bottom: 5px; text-transform: uppercase; font-size: 0.85rem; cursor: help;">
                    Cambiar tema seleccionado 🎨</span>
                <p style="margin: 0; font-size: 0.85rem; color: var(--text-muted);">Seleccione un tema de su elección.</p>
            </div>
            <form>
                <a onclick="toggleTheme()"
                    target="_blank" value="backup" class="btn"
                    style="background: var(--accent); color: var(--btn-text); padding: 12px 25px; width: auto; font-weight: bold;">
                    Cambiar tema
                </a>
            </form>
        </div>

        <div class="sql-zone">
            <div style="text-align: left;">
                <span
                    style="display: block; font-weight: bold; color: var(--accent); margin-bottom: 5px; text-transform: uppercase; font-size: 0.85rem; cursor: help;">
                    📃 Gestionar la base de datos 📃</span>
                <p style="margin: 0; font-size: 0.85rem; color: var(--text-muted);">Ir al gestor de base de datos para
                    realizar cambios avanzados.</p>
            </div>
            <form>
                <a href="http://localhost/phpmyadmin/index.php?route=/database/structure&db=gestor_asistencia"
                    target="_blank" value="backup" class="btn"
                    style="background: var(--accent); color: var(--btn-text); padding: 12px 25px; width: auto; font-weight: bold;">
                    GESTIONAR .SQL
                </a>
            </form>
        </div>

        <div class="backup-zone">
            <div style="text-align: left;">
                <span
                    style="display: block; font-weight: bold; color: var(--accent); margin-bottom: 5px; text-transform: uppercase; font-size: 0.85rem;">📥
                    Respaldo de Seguridad</span>
                <p style="margin: 0; font-size: 0.85rem; color: var(--text-muted);">Descarga el volcado SQL antes de
                    purgar la base de datos.</p>
            </div>
            <form action="../logic/maintenance_controller.php" method="POST" style="margin: 0;">
                <button type="submit" name="action" value="backup" class="btn"
                    style="background: var(--accent); color: var(--btn-text); padding: 12px 25px; width: auto; font-weight: bold;">
                    DESCARGAR BACKUP .SQL
                </button>
            </form>
        </div>

        <div class="dash-zone">
            <div style="text-align: left;">
                <span
                    style="display: block; font-weight: bold; color: var(--accent); margin-bottom: 5px; text-transform: uppercase; font-size: 0.85rem; cursor: help;">
                    ✅ REGRESAR AL Dashboard </span>
                <p style="margin: 0; font-size: 0.85rem; color: var(--text-muted);">Si ya realizó su inserción de datos,
                    aqui puede regresar a la pagina principal.</p>
            </div>
            <form>
                <a href=http://localhost/asistencia/views/dashboard.php value="backup" class="btn"
                    style="background: var(--accent); color: var(--btn-text); padding: 12px 25px; width: auto; font-weight: bold;">
                    REGRESAR
                </a>
            </form>
        </div>
    </div>

    <script>
        // Slider
        const slider = document.getElementById('range');
        const display = document.getElementById('qty');
        slider.oninput = () => display.innerText = slider.value;

        // Switch Maestro
        const sw = document.getElementById('masterSwitch');
        const crits = document.querySelectorAll('.critical');
        sw.onchange = () => crits.forEach(b => b.disabled = !sw.checked);

        // Función Seed
        function runSeed() {
            const f = document.getElementById('formMaster');
            const a = document.createElement('input'); a.type = 'hidden'; a.name = 'action'; a.value = 'seed';
            const q = document.createElement('input'); q.type = 'hidden'; q.name = 'cantidad_seed'; q.value = slider.value;
            const m = document.createElement('input'); m.type = 'hidden'; m.name = 'modo_seed'; m.value = document.getElementById('modoSelect').value;
            f.appendChild(a); f.appendChild(q); f.appendChild(m);
            f.submit();
        }
    </script>

</body>

</html>