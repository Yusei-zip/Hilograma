<?php
include '../includes/reutilizable/session_start.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT a.*, e.nombre_completo FROM asistencias a JOIN empleados e ON a.id_empleado = e.id WHERE a.id = ?");
$stmt->execute([$id]);
$asistencia = $stmt->fetch();


if (!$asistencia)
    exit("Registro no encontrado");
?>

<!DOCTYPE html>
<html lang="es" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <title>Editar Asistencia</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <link rel="icon" type="image/svg+xml" href="../public/img/favicon.svg">
</head>

<body>
    <script src="../public/js/loadtheme.js">//Cambiar el tema</script>

    <div class="card" style="max-width: 500px;">
        <h2>✏️ Editar Registro</h2>
        <p style="color: var(--text-muted);">Empleado: <strong><?= $asistencia['nombre_completo'] ?></strong></p>
        <p style="color: var(--text-muted);">ID: <strong><?= $asistencia['id'] ?></strong></p>
        <p style="color: var(--text-muted); font-size: 0.9rem;">Fecha:
            <?= date('d/m/Y', strtotime($asistencia['fecha'])) ?>
        </p>

        <!--Formulario de editado-->

        <form action="../logic/asistencia_controller.php" method="POST">
            <input type="hidden" name="id" value="<?= $asistencia['id'] ?>">
            <div class="form-group">
                <label>Estado Principal</label>
                <select name="estado" required id="estadoMain" onchange="toggleSubEstados()">
                    <option value="Presente" <?= $asistencia['estado'] == 'Presente' ? 'selected' : '' ?>>Presente</option>
                    <option value="Ausente" <?= $asistencia['estado'] == 'Ausente' ? 'selected' : '' ?>>Ausente</option>
                </select>
            </div>

            <div id="seccionPresente">

                <div class="form-group">
                    <label>Sub-Estado / Detalle</label>
                    <select name="sub_estado" id="sub_estado">
                        <option value="Ninguno" <?= $asistencia['sub_estado'] == 'Ninguno' ? 'selected' : '' ?>>Ninguno
                        </option>
                        <option value="Llegada Tardía" <?= $asistencia['sub_estado'] == 'Llegada Tardía' ? 'selected' : '' ?>>
                            Llegada Tardía</option>
                        <option value="Retiro Temprano" <?= $asistencia['sub_estado'] == 'Retiro Temprano' ? 'selected' : '' ?>>
                            Retiro Temprano</option>
                    </select>
                </div>


                <div class="form-group">
                    <label>Observaciones</label>
                    <textarea name="observaciones" rows="3"
                        style="width: 100%; background: var(--input-bg); color: var(--text-main); border: 1px solid var(--border); border-radius: 8px; padding: 10px;"><?= htmlspecialchars($asistencia['observaciones']) ?></textarea>
                </div>

                <div style="margin: 15px 0; display: flex; align-items: left; gap: 10px;">
                    <input type="checkbox" name="telefono_entregado" <?= $asistencia['telefono_entregado'] ? 'checked' : '' ?>>
                    <label>¿Teléfono entregado?</label>
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit" name="update_asistencia" class="btn btn-primary" style="flex: 2;">Guardar
                    Cambios</button>
                <a href="historial.php" class="btn"
                    style="flex: 1; text-align: center; text-decoration: none; background: var(--border);">Cancelar</a>
            </div>
        </form>
    </div>
</body>
<script>

    function toggleSubEstados() {
        const estado = document.getElementById('estadoMain').value;
        document.getElementById('seccionPresente').style.display = (estado === 'Presente') ? 'block' : 'none';
    }

    toggleSubEstados()

</script>

</html>