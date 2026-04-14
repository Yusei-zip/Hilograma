<?php
session_start();

// Si no existe la sesión, lo mandamos al login de una
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit();
}

include_once '../config/db.php';

// Verificamos que $pdo (el nombre que usas en tu db.php) exista
if (!isset($pdo)) {
    die("Error crítico: La conexión no está disponible.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $msg = "";

    try {
        switch ($action) {
            case 'clear_attendance':
                $pdo->exec("TRUNCATE TABLE asistencias");
                $msg = "Historial de asistencias reseteado.";
                break;

            case 'clear_workers':
                $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
                $pdo->exec("TRUNCATE TABLE empleados");
                $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
                $msg = "Base de datos de personal vaciada.";
                break;

            case 'optimize':
                // PDO no usa query() para esto, mejor exec()
                $pdo->exec("OPTIMIZE TABLE empleados, asistencias");
                $msg = "Tablas optimizadas con éxito.";
                break;

            case 'reset_ai':
                $pdo->exec("ALTER TABLE empleados AUTO_INCREMENT = 1");
                $pdo->exec("ALTER TABLE asistencias AUTO_INCREMENT = 1");
                $msg = "Contadores de ID reiniciados.";
                break;

            // ... dentro del switch ($action) ...
            case 'seed':
                $cantidad = isset($_POST['cantidad_seed']) ? (int) $_POST['cantidad_seed'] : 10;
                $modo = isset($_POST['modo_seed']) ? $_POST['modo_seed'] : 'normal';

                $nombres_base = ['Nanami', 'Tomoe', 'Mizuki', 'Kurama', 'Yusei', 'Julian', 'Ana', 'Juan', 'Maria', 'Lucas'];
                $cargos_base = [1,2,3,4,5];
                $activo = [0, 1];
                $estados = ['Presente', 'Ausente'];
                $sub_estados = ['Ninguno', 'Retiro Temprano', 'Llegada Tardía'];

                $stmtEmp = $pdo->prepare("INSERT INTO empleados (nombre_completo, id_cargo, is_active) VALUES (?,?, ?)");
                $stmtAsis = $pdo->prepare("INSERT INTO asistencias (id_empleado, fecha, estado, sub_estado, telefono_entregado, observaciones) VALUES (?, ?, ?, ?, ?, ?)");

                for ($j = 0; $j < $cantidad; $j++) {
                    $nombre = $nombres_base[array_rand($nombres_base)] . " " . rand(1000, 9999);
                    $cargo = $cargos_base[array_rand($cargos_base)] . " " . rand(100, 999);
                    $activo2 = $activo[array_rand($activo)] . " " . rand(100, 999);
                    $stmtEmp->execute([$nombre, $cargo, $activo2]);
       
                    $id_emp = $pdo->lastInsertId();

                    for ($i = 0; $i < 7; $i++) { // Generamos una semana completa
                        $fecha = date('Y-m-d', strtotime("-$i days"));

                        // Lógica de "Modo Caótico"
                        if ($modo === 'caotico') {
                            $estado = (rand(1, 10) > 2) ? 'Ausente' : 'Presente'; // 60% de faltas
                        } else {
                            $estado = (rand(1, 10) > 8) ? 'Ausente' : 'Presente'; // 20% de faltas
                        }

                        $sub = ($estado === 'Presente') ? $sub_estados[array_rand($sub_estados)] : 'Ninguno';
                        $tel = ($estado === 'Presente') ? (rand(1, 10) > 2 ? 1 : 0) : 0;
                        $obs = ($modo === 'caotico') ? "Generación caótica de prueba" : null;

                        $stmtAsis->execute([$id_emp, $fecha, $estado, $sub, $tel, $obs]);
                    }
                }
                $msg = "Laboratorio: Se inyectaron $cantidad empleados con historial de 7 días ($modo).";
                break;

            case 'backup':
                // Limpiamos cualquier salida previa para evitar archivos corruptos
                if (ob_get_level())
                    ob_end_clean();

                $tablas = ['empleados', 'asistencias'];
                $salida = "-- Respaldo: " . date('Y-m-d H:i:s') . "\nSET FOREIGN_KEY_CHECKS = 0;\n";

                foreach ($tablas as $tabla) {
                    $res = $pdo->query("SHOW CREATE TABLE $tabla")->fetch(PDO::FETCH_ASSOC);
                    $salida .= "\n\n" . $res['Create Table'] . ";\n";
                    $datos = $pdo->query("SELECT * FROM $tabla")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($datos as $fila) {
                        $valores = array_map(fn($v) => $v === null ? 'NULL' : $pdo->quote($v), $fila);
                        $salida .= "INSERT INTO $tabla (" . implode(', ', array_keys($fila)) . ") VALUES (" . implode(', ', $valores) . ");\n";
                    }
                }
                $salida .= "\nSET FOREIGN_KEY_CHECKS = 1;";

                // HEADERS CRÍTICOS
                header('Content-Type: application/sql');
                header('Content-Disposition: attachment; filename="backup_' . date('Y-m-d_H-i') . '.sql"');
                header('Content-Length: ' . strlen($salida));
                header('Pragma: no-cache');
                header('Expires: 0');

                echo $salida;
                exit; // <--- OBLIGATORIO para que no intente hacer el header(Location)
        }

        header("Location: ../views/maintenance.php?key=yusei123&success=" . urlencode($msg));
        exit;

    } catch (PDOException $e) {
        die("Error en el mantenimiento (PDO): " . $e->getMessage());
    }
}