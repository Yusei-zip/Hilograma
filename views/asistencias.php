<?php
include '../includes/reutilizable/session_start.php';

try {
    $stmt = $pdo->query("SELECT id, nombre_completo FROM empleados ORDER BY nombre_completo ASC");
    $empleados = $stmt->fetchAll();
} catch (Exception $e) {
    die("Error al cargar empleados: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Asistencia</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <link rel="icon" type="image/svg+xml" href="../public/img/favicon.svg">
    <style>
        /* RESET PARA CENTRADO UNIFICADO */
        body {
            display: flex !important;
            flex-direction: column !important;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-color: var(--bg-body);
        }

        /* EL TAMAÑO GEMELO (600px) */
        .card-asistencia {
            width: 95%;
            max-width: 600px;
            height: 95%;
            max-height: 700px;
            margin: 0 auto 40px auto;
        }

       
    </style>
</head>

<body>

    <?php include_once 'navbar.php'; ?>
    <br>
    <div class="card card-asistencia">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <h2 style="margin: 0;">⏱️ Registro de Asistencia individual</h2>
        </div>

        

        <!--Formulario -->

        <form action="../logic/asistencia_controller.php" method="POST">
            <div class="form-group">
                <label>Trabajador</label>
                <select name="id_empleado" required>
                    <option value="">-- Seleccionar --</option>
                    <?php foreach ($empleados as $emp): ?>
                        <option value="<?= $emp['id'] ?>"><?= htmlspecialchars($emp['nombre_completo']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>


            <div class="form-group">
                <label>Fecha</label>
                <input type="date" name="fecha" value="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="form-group">
                <label>Estado</label>
                <select name="estado" id="estadoMain" onchange="toggleSubEstados()" required>
                    <option value="Presente">Presente</option>
                    <option value="Ausente">Ausente</option>
                </select>
            </div>

            <div id="seccionPresente">
                <div class="form-group">
                    <label>Sub-estado</label>
                    <select name="sub_estado">
                        <option value="Ninguno">Normal</option>
                        <option value="Retiro Temprano">Retiro Temprano</option>
                        <option value="Llegada Tardía">Llegada Tardía</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Observación</label>
                    <input type="text" name="observaciones" placeholder="Ej: Consulta Médica">
                </div>

                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" name="telefono" value="1" style="width: auto;">
                        ¿Entregó el teléfono?
                    </label>
                </div>
            </div>

            <button type="submit" name="guardar_asistencia" class="btn btn-primary" style="width: 100%;">
                <svg viewBox="0 0 24 24" style="width: 20px; fill: currentColor;">
                    <path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z" />
                </svg>
                Guardar Registro
            </button>
        </form>
    </div>

    <script>
        function toggleSubEstados() {
            const estado = document.getElementById('estadoMain').value;
            document.getElementById('seccionPresente').style.display = (estado === 'Presente') ? 'block' : 'none';
        }

        document.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
            toggleSubEstados();
        });


    </script>
    <script src="../public/js/alert-msg.js">//Cambiar el tema</script>

</body>

</html>