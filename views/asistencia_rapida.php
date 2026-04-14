<?php
include_once '../config/db.php';
$empleados = $pdo->query("SELECT id, nombre_completo FROM empleados ORDER BY nombre_completo ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Carga Rápida | Sistema</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <link rel="icon" type="image/svg+xml" href="../public/img/favicon.svg">
    <script>
        document.documentElement.setAttribute('data-theme', localStorage.getItem('theme') || 'dark');
    </script>
    <style>
        /* FIX: Asegura que el contenido no se meta bajo la navbar */
        body {
            display: flex !important;
            flex-direction: column !important;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-color: var(--bg-body);
        }

        .asistencia-container {
            padding-top: 20px;
            /* Ajusta según el alto de tu navbar */
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            width: 95%;
            max-width: 1000px;
            margin: 0 auto 40px auto;
        }

        .config-card,
        #interactive-mode {
            background: var(--bg-card);
            border: 1px solid var(--border);
            padding: 30px;
            border-radius: 20px;
            width: 90%;
            max-width: 1000px;
        }

        .current-date {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--text-main);
            margin-bottom: 5px;
        }

        .current-day-name {
            font-size: 1.1rem;
            color: var(--accent);
            text-transform: uppercase;
            margin-bottom: 25px;
        }

        /* Controles de Sub-Estado y Teléfono */
        .details-box {
            background: rgba(0, 0, 0, 0.2);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 25px;
            display: flex;
            gap: 20px;
            justify-content: center;
            align-items: center;
        }

        .action-btns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        .btn-action {
            padding: 18px;
            border-radius: 12px;
            border: none;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-presente {
            background: #00ff96;
            color: #000;
        }

        .btn-ausente {
            background: #ff4757;
            color: #fff;
        }

        .btn-skip {
            background: #555;
            color: #fff;
            grid-column: span 2;
        }

        /* Checkbox grande para comodidad */
        .check-container {
            display: flex;
            align-items: center;
            cursor: pointer;
            font-size: 0.9rem;
            color: var(--text-main);
        }

        .check-container input {
            width: 20px;
            height: 20px;
            margin-right: 10px;
            cursor: pointer;
        }

        select {
            background: #1a1a1a;
            border: 1px solid var(--border);
            color: #fff;
            padding: 8px;
            border-radius: 5px;
        }

        /* Estilos para el Modal de Confirmación */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(5px);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .confirm-modal {
            background: var(--bg-card);
            border: 2px solid var(--accent);
            padding: 30px;
            border-radius: 20px;
            max-width: 450px;
            width: 90%;
            text-align: center;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.5);
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid var(--border);
        }

        .summary-item:last-child {
            border: none;
        }

        .btn-confirm {
            background: var(--accent);
            color: #fff;
            padding: 12px 25px;
            border-radius: 50px;
            border: none;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
            width: 100%;
        }

        .btn-cancel {
            background: transparent;
            color: var(--text-muted);
            padding: 10px;
            border: none;
            cursor: pointer;
            font-size: 0.8rem;
            margin-top: 10px;
        }
    </style>
</head>

<body>


    <?php include_once 'navbar.php'; ?>
    <div class="asistencia-container">


        <div id="setup-form" class="config-card">
            <h2 style="color: var(--text-main); margin-bottom: 20px;"> ⭐ Carga Masiva de asistencia</h2>
            <div style="display: flex; flex-direction: column; gap: 15px;">
                <select id="empleado_id">
                    <option value="">-- Seleccionar Empleado --</option>
                    <?php foreach ($empleados as $emp): ?>
                        <option value="<?= $emp['id'] ?>"><?= htmlspecialchars($emp['nombre_completo']) ?></option>
                    <?php endforeach; ?>
                </select>
                <div style="display: flex; gap: 10px; justify-content: center;">
                    <input type="date" id="fecha_inicio" value="2026-01-01">
                    <input type="date" id="fecha_fin" value="<?= date('Y-m-d') ?>">
                </div>
                <button class="btn-start" onclick="iniciarCarga()"
                    style="background: var(--btn-primary); color: #fff; padding: 12px; border-radius: 50px; border: none; font-weight: bold; cursor: pointer;">INICIAR
                    MODO CARGA</button>
            </div>
        </div>

        <div id="interactive-mode" style="display: none; text-align: center;">
            <div id="nombre-empleado-titulo" style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 10px;">
            </div>
            <div class="current-date" id="display-fecha">--/--/----</div>
            <div class="current-day-name" id="display-dia">Día</div>

            <div class="details-box">
                <div>
                    <label
                        style="display: block; font-size: 0.7rem; color: var(--text-muted); margin-bottom: 5px;">SUB-ESTADO</label>
                    <select id="sub_estado">
                        <option value="Normal">Normal</option>
                        <option value="Retiro temprano">Retiro temprano</option>
                        <option value="Llegada tardia">Llegada tardía</option>
                    </select>
                </div>
                <label class="check-container">
                    <input type="checkbox" id="check_tel"> ¿Entregó teléfono?
                </label>
            </div>

            <div class="action-btns">
                <button class="btn-action btn-presente" onclick="registrar('Presente')">PRESENTE (P)</button>
                <button class="btn-action btn-ausente" onclick="registrar('Ausente')">AUSENTE (A)</button>
                <button class="btn-action btn-skip" onclick="saltar()">SALTAR ESTE DÍA (S)</button>
            </div>

            <div style="margin-top: 20px;">
                <button onclick="enviarDatos(false)"
                    style="background: transparent; border: 1px solid var(--border); color: var(--text-muted); padding: 5px 15px; border-radius: 5px; cursor: pointer; font-size: 0.7rem;">Guardar
                    progreso parcial</button>
            </div>

            <div class="progress-bar"
                style="width: 100%; height: 4px; background: rgba(255,255,255,0.1); margin-top: 20px; border-radius: 10px; overflow: hidden;">
                <div id="barra-progreso" style="height: 100%; background: var(--accent); width: 0%;"></div>
            </div>
            <p id="contador-texto" style="font-size: 0.75rem; color: var(--text-muted); margin-top: 5px;"></p>
        </div>
    </div>

    <script>
        let fechasArray = [];
        let indiceActual = 0;
        let registros = [];

        function iniciarCarga() {
            const empId = document.getElementById('empleado_id').value;
            const inicioStr = document.getElementById('fecha_inicio').value;
            const finStr = document.getElementById('fecha_fin').value;

            if (!empId || !inicioStr || !finStr) return alert("Completa todos los campos");

            // Agregamos 'T00:00:00' para asegurar que JS tome la fecha local y no UTC
            const inicio = new Date(inicioStr + 'T00:00:00');
            const fin = new Date(finStr + 'T00:00:00');

            fechasArray = [];
            let temp = new Date(inicio);

            while (temp <= fin) {
                let diaSemana = temp.getDay();
                // 0 = Domingo, 6 = Sábado. Solo Lunes a Viernes.
                if (diaSemana !== 0 && diaSemana !== 6) {
                    fechasArray.push(new Date(temp));
                }
                temp.setDate(temp.getDate() + 1);
            }

            if (fechasArray.length === 0) {
                return alert("No hay días laborales (Lunes a Viernes) en el rango seleccionado.");
            }

            // Reiniciar índices por si se inicia una nueva carga
            indiceActual = 0;
            registros = [];

            document.getElementById('setup-form').style.display = 'none';
            document.getElementById('interactive-mode').style.display = 'block';
            document.getElementById('nombre-empleado-titulo').innerText = "REGISTRANDO A: " + document.getElementById('empleado_id').options[document.getElementById('empleado_id').selectedIndex].text;

            actualizarUI();
        }
        function actualizarUI() {
            if (indiceActual < fechasArray.length) {
                const f = fechasArray[indiceActual];

                // Actualizar Fecha y Día
                document.getElementById('display-fecha').innerText = f.toLocaleDateString('es-PY', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
                document.getElementById('display-dia').innerText = f.toLocaleDateString('es-PY', { weekday: 'long' });

                // Actualizar Barra de Progreso
                // Usamos (indiceActual / total) para que la barra refleje lo completado
                const porcentaje = (indiceActual / fechasArray.length) * 100;
                document.getElementById('barra-progreso').style.width = porcentaje + "%";

                // Texto del contador
                document.getElementById('contador-texto').innerText = `Día ${indiceActual + 1} de ${fechasArray.length}`;
            }
        }

        function registrar(estado) {
            const f = fechasArray[indiceActual];
            const fechaSQL = f.toISOString().split('T')[0];

            registros.push({
                empleado_id: document.getElementById('empleado_id').value,
                fecha: fechaSQL,
                estado: estado,
                sub_estado: document.getElementById('sub_estado').value,
                telefono_entregado: document.getElementById('check_tel').checked ? 1 : 0
            });

            avanzar();
        }

        function saltar() { avanzar(); }

        function avanzar() {
            // REINICIO DE CAMPOS PARA EL SIGUIENTE DÍA
            document.getElementById('check_tel').checked = false;
            document.getElementById('sub_estado').value = "Normal";

            indiceActual++;
            if (indiceActual < fechasArray.length) {
                actualizarUI();
            } else {
                mostrarResumenFinal(); // En lugar de enviar directo, mostramos el resumen
            }
        }

        function mostrarResumenFinal() {
            const totalPresentes = registros.filter(r => r.estado === 'Presente').length;
            const totalAusentes = registros.filter(r => r.estado === 'Ausente').length;
            const totalTelefonos = registros.filter(r => r.telefono_entregado === 1).length;
            const nombre = document.getElementById('empleado_id').options[document.getElementById('empleado_id').selectedIndex].text;

            document.getElementById('resumen-nombre').innerText = nombre;
            document.getElementById('resumen-detalles').innerHTML = `
        <div class="summary-item"><span>Días Totales:</span> <strong>${fechasArray.length}</strong></div>
        <div class="summary-item"><span>Presentes:</span> <strong style="color: #00ff96;">${totalPresentes}</strong></div>
        <div class="summary-item"><span>Ausentes:</span> <strong style="color: #ff4757;">${totalAusentes}</strong></div>
        <div class="summary-item"><span>Teléfonos Entregados:</span> <strong style="color: #00d2ff;">${totalTelefonos}</strong></div>
        <div class="summary-item"><span>Días Saltados:</span> <strong>${fechasArray.length - registros.length}</strong></div>
    `;

            document.getElementById('modal-confirmacion').style.display = 'flex';
        }

        function cerrarModal() {
            document.getElementById('modal-confirmacion').style.display = 'none';
        }

        function ejecutarGuardadoFinal() {
            enviarDatos(true);
        }

        function enviarDatos(finalizar) {
            if (registros.length === 0) {
                if (finalizar) location.reload();
                return;
            }

            fetch('../logic/guardar_asistencia_masiva.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(registros)
            })
                .then(r => r.json())
                .then(d => {
                    if (finalizar) {
                        alert("¡Éxito! " + d.mensaje);
                        location.reload();
                    } else {
                        alert("Progreso parcial guardado (" + registros.length + " días).");
                        registros = []; // Limpiamos para que el parcial no se duplique al final
                    }
                })
                .catch(err => alert("Error al guardar: " + err));
        }
    </script>
    <div id="modal-confirmacion" class="modal-overlay">
        <div class="confirm-modal">
            <h2 style="color: var(--text-main); margin-bottom: 15px;">¿Confirmar Asistencias?</h2>
            <p id="resumen-nombre" style="color: var(--accent); font-weight: bold; margin-bottom: 20px;"></p>

            <div id="resumen-detalles">
            </div>

            <button class="btn-confirm" onclick="ejecutarGuardadoFinal()">SÍ, GUARDAR TODO</button>
            <button class="btn-cancel" onclick="cerrarModal()">Revisar de nuevo</button>
        </div>
    </div>

</body>

</html>