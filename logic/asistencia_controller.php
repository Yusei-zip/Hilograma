<?php
session_start();

// Si no existe la sesión, lo mandamos al login de una
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit();
}

include_once '../config/db.php';
if (isset($_POST['guardar_asistencia'])) {
    $empleado = $_POST['id_empleado'];
    $fecha = $_POST['fecha'];
    $estado = $_POST['estado'];
    $sub_estado = ($estado == 'Presente') ? $_POST['sub_estado'] : 'Ninguno';
    $tel = isset($_POST['telefono']) ? 1 : 0;
    $observaciones = $_POST['observaciones'];

    $sql = "INSERT INTO asistencias (id_empleado, fecha, estado, sub_estado, telefono_entregado, observaciones) 
            VALUES (?, ?, ?, ?, ?,?) 
            ON DUPLICATE KEY UPDATE estado=?, sub_estado=?, telefono_entregado=?";
    
    $stmt = $pdo->prepare($sql);
    $res = $stmt->execute([$empleado, $fecha, $estado, $sub_estado, $tel, $estado, $sub_estado, $tel, $observaciones]);

    if ($res) {
        $_SESSION['msg'] = "Agregado correctamente";
        $_SESSION['msg_type'] = "green";
    } else {
        $_SESSION['msg'] = "Error al procesar el registro";
        $_SESSION['msg_type'] = "red";
    }
    header("Location: ../views/asistencias.php");
}

if (isset($_POST['update_asistencia'])) {
    $id = $_POST['id'];
    $estado = $_POST['estado'];
    $sub_estado = $_POST['sub_estado'];
    $observaciones = $_POST['observaciones'];
    $telefono = isset($_POST['telefono_entregado']) ? 1 : 0;

    $sql = "UPDATE asistencias SET estado = ?, sub_estado = ?, observaciones = ?, telefono_entregado = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$estado, $sub_estado, $observaciones, $telefono, $id])) {
        $_SESSION['msg'] = "Asistencia actualizada con éxito";
        $_SESSION['msg_type'] = "var(--success)";
    }

    
    header("Location: ../views/historial.php");
    exit();
}


//Logs
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];

    // Obtener datos antes de borrar para el log
    $stmtData = $pdo->prepare("SELECT a.fecha, e.nombre_completo FROM asistencias a JOIN empleados e ON a.id_empleado = e.id WHERE a.id = ?");
    $stmtData->execute([$id]);
    $asis = $stmtData->fetch();

    if ($asis) {
        $detalle = "Asistencia de " . $asis['nombre_completo'] . " del día " . date('d/m/Y', strtotime($asis['fecha']));
        
        // Insertar en log
        $stmtLog = $pdo->prepare("INSERT INTO logs_borrados (detalle_registro) VALUES (?)");
        $stmtLog->execute([$detalle]);

        // Borrar registro original
        $stmtDelete = $pdo->prepare("DELETE FROM asistencias WHERE id = ?");
        $stmtDelete->execute([$id]);
    }

    
    header("Location: ../views/historial.php");
    exit();
}
?>