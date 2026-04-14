<?php
include '../includes/reutilizable/session_start.php';

if (!isset($_GET['id'])) {
    header("Location: empleados.php");
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM empleados WHERE id = ?");
$stmt->execute([$id]);
$emp = $stmt->fetch();

if (!$emp) {
    header("Location: empleados.php");
    exit;
}

$subCargosGuardados = !empty($emp['sub_cargos']) ? explode(', ', $emp['sub_cargos']) : [];

// Obtenemos los cargos de la base de datos
$stmtCargos = $pdo->query("SELECT * FROM cargos");
$cargos = $stmtCargos->fetchAll();
?>

<!DOCTYPE html>
<html lang="es" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <title>Editar Perfil | <?= htmlspecialchars($emp['nombre_completo']) ?></title>
    <link rel="stylesheet" href="../public/css/style.css">
    <link rel="icon" type="image/svg+xml" href="../public/img/favicon.svg">
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            background: var(--bg-body);
            margin: 0;
        }

        .card-edit {
            width: 95%;
            max-width: 500px;
            margin-top: 50px;
        }

        /* Modal Pop-up */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 10000;
        }

        .modal-content {
            background-color: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.5);
            animation: slideUp 0.3s ease-out;
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: var(--text-main);
        }

        input[type="checkbox"] {
            accent-color: var(--accent);
            cursor: pointer;
            width: auto;
        }

        .btn-container {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }
    </style>
</head>

<body>
    <script src="../public/js/loadtheme.js">//Cambiar el tema</script>
    <div class="card card-edit">
        <h2
            style="color: var(--accent); margin-bottom: 25px; border-bottom: 1px solid var(--border); padding-bottom: 10px;">
            👤 Editar Empleado ID: <?= htmlspecialchars($emp['id']) ?>
        </h2>

        <!--Formulario de editaje-->

        <form action="../logic/empleado_controller.php" method="POST">
            <input type="hidden" name="id_empleado" value="<?= $emp['id'] ?>">

            <div class="form-group">
                <label>Nombre Completo</label>
                <input type="text" name="nombre_completo" value="<?= htmlspecialchars($emp['nombre_completo']) ?>"
                    required>
            </div>

            <div class="form-group">
                <label>Teléfono</label>
                <input type="number" name="numero" value="<?= $emp['numero'] ?>" required>
            </div>

            <div class="form-group">
                <label>Cargo Principal</label>
                <select name="id_cargo" id="cargoSelect" onchange="checkSubCargos()" required>
                    <?php foreach ($cargos as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($emp['id_cargo'] == $c['id']) ? 'selected' : '' ?>>
                            <?= $c['nombre_cargo'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-weight: normal;">
                    <input type="checkbox" name="is_active" value="1" <?= $emp['is_active'] ? 'checked' : '' ?>>
                    ¿Está Contratado? (Activo en Nómina)
                </label>
            </div>

            <input type="hidden" name="sub_cargos_final" id="sub_cargos_final"
                value="<?= htmlspecialchars($emp['sub_cargos']) ?>">

            <div class="btn-container">
                <button type="submit" name="actualizar_empleado" class="btn btn-primary" style="flex: 2;">
                    Guardar Cambios
                </button>
                <a href="empleados.php" class="btn"
                    style="flex: 1; text-align: center; background: rgba(255,255,255,0.05); text-decoration: none; line-height: 40px;">
                    Cancelar
                </a>
            </div>
        </form>
    </div>

    <div id="modalSubCargos" class="modal-overlay">
        <div class="modal-content card" style="max-width: 400px; width: 90%;">
            <h3 id="modalTitle">🛠️ Especialidades</h3>
            <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 15px;">Selecciona las máquinas que
                opera:</p>
            <div id="listaSubCargos"
                style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px;"></div>
            <button type="button" onclick="closeSubModal()" class="btn btn-primary" style="width: 100%;">Confirmar
                Selección</button>
        </div>
    </div>

    <script>
        // Configuración de máquinas por cargo
        const subCargosData = {
            "Costurero": ["Overlock", "Collareta", "Recta", "Botonera", "Ojaladora", "Atracadora", "Engomadora", "Cinturera", "Cadeneta", "Pasacintera", "Doble", "Cortabies"],
            "Serigrafista": ["Revelado", "Estampado Manual", "Pulpo", "Secado"]
        };

        // Al cargar la página, memorizamos el cargo actual
        const select = document.getElementById('cargoSelect');
        let cargoActualNombre = select.options[select.selectedIndex].text;
        let seleccionados = <?= json_encode($subCargosGuardados) ?>;

        function checkSubCargos() {
            const nuevoCargoNombre = select.options[select.selectedIndex].text;
            const listaDiv = document.getElementById('listaSubCargos');
            const modal = document.getElementById('modalSubCargos');
            const inputFinal = document.getElementById('sub_cargos_final');

            // SI EL CARGO CAMBIÓ: Limpiamos los subcargos anteriores inmediatamente
            if (nuevoCargoNombre !== cargoActualNombre) {
                inputFinal.value = "";
                seleccionados = [];
                cargoActualNombre = nuevoCargoNombre; // Actualizamos el rastreador
            }

            // Si el cargo tiene máquinas, abrimos el modal
            if (subCargosData[nuevoCargoNombre]) {
                document.getElementById('modalTitle').innerText = `🛠️ Especialidades: ${nuevoCargoNombre}`;
                listaDiv.innerHTML = "";
                subCargosData[nuevoCargoNombre].forEach(sub => {
                    const isChecked = seleccionados.includes(sub) ? 'checked' : '';
                    listaDiv.innerHTML += `
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 0.85rem; cursor: pointer; padding: 5px; border-radius: 4px;">
                        <input type="checkbox" class="check-sub" value="${sub}" ${isChecked}> ${sub}
                    </label>
                `;
                });
                modal.style.display = 'flex';
            } else {
                // Si el cargo no tiene subcargos, el input se queda vacío
                inputFinal.value = "";
            }
        }

        function closeSubModal() {
            const checks = document.querySelectorAll('.check-sub:checked');
            const values = Array.from(checks).map(c => c.value);

            document.getElementById('sub_cargos_final').value = values.join(', ');
            seleccionados = values;

            document.getElementById('modalSubCargos').style.display = 'none';
        }

        // Cerrar modal al hacer clic fuera (opcional, por usabilidad)
        window.onclick = function (event) {
            const modal = document.getElementById('modalSubCargos');
            if (event.target == modal) { closeSubModal(); }
        }

        // Aplicar tema guardado
        document.documentElement.setAttribute('data-theme', localStorage.getItem('theme') || 'dark');
    </script>
</body>

</html>