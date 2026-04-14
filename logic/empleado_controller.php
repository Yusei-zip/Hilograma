<?php
session_start();

// Si no existe la sesión, lo mandamos al login de una
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit();
}

include_once '../config/db.php';

if (isset($_POST['guardar_empleado'])) {
    $nombre = $_POST['nombre_completo'];
    $numero = empty($_POST['numero']) ? 'N/A' : $_POST['numero'];
    $id_cargo = $_POST['id_cargo']; // El ID de la tabla cargos
    //$cargo = $_POST['cargo'];
    $is_active = (int)$_POST['is_active']; // Capturamos el 1 o 0
    $sub_cargos = $_POST['sub_cargos_final'] ?? ''; // El string de las máquinas

    $sql = "INSERT INTO empleados (nombre_completo, numero, id_cargo, is_active, sub_cargos) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$nombre, $numero, $id_cargo, $is_active, $sub_cargos])) {
        $_SESSION['msg'] = "Empleado añadido correctamente";
        $_SESSION['msg_type'] = "success";
    } else {
        $_SESSION['msg'] = "Error al guardar el empleado";
        $_SESSION['msg_type'] = "error";
    }

    header("Location: ../views/empleados.php");
    exit();
}

if (isset($_POST['actualizar_empleado'])) {
    $id = $_POST['id_empleado'];
    $nombre = trim($_POST['nombre_completo']);
    $numero = trim($_POST['numero']);
    $id_cargo = $_POST['id_cargo'];
    $sub_cargos = $_POST['sub_cargos_final']; // Viene del modal
    
    // El checkbox solo se envía si está marcado. 
    // Si no está en $_POST, el empleado está inactivo (0).
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    try {
        $sql = "UPDATE empleados SET 
                nombre_completo = ?, 
                numero = ?, 
                id_cargo = ?, 
                sub_cargos = ?, 
                is_active = ? 
                WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        $resultado = $stmt->execute([$nombre, $numero, $id_cargo, $sub_cargos, $is_active, $id]);

        if ($resultado) {
            $_SESSION['msg'] = "Perfil actualizado correctamente";
            $_SESSION['msg_type'] = "success";
        } else {
            $_SESSION['msg'] = "No se pudieron guardar los cambios";
            $_SESSION['msg_type'] = "error";
        }
    } catch (PDOException $e) {
        $_SESSION['msg'] = "Error en la base de datos: " . $e->getMessage();
        $_SESSION['msg_type'] = "error";
    }

    header("Location: ../views/empleados.php");
    exit();
}

// AGREGAR
/*if (isset($_POST['guardar_empleado'])) {
    $nombre = $_POST['nombre_completo'];
    $cargo = $_POST['id_cargo'];
    $numero = $_POST['numero'];

    try {
        $sql = "INSERT INTO empleados (nombre_completo, numero, id_cargo) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $numero, $cargo]);
        
        $_SESSION['msg'] = "Trabajador Agregado";
        $_SESSION['msg_type'] = "green";
    } catch (Exception $e) {
        $_SESSION['msg'] = "Error: " . $e->getMessage();
        $_SESSION['msg_type'] = "red";
    }
    header("Location: ../views/empleados.php");
}*/

// ELIMINAR (Opcional)
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Iniciamos una transacción para asegurar que se borre todo o nada
    $pdo->beginTransaction();
    try {
        // 1. Borrar todas las asistencias vinculadas a este empleado
        $stmtAsis = $pdo->prepare("DELETE FROM asistencias WHERE id_empleado = ?");
        $stmtAsis->execute([$id]);

        // 2. Borrar al empleado
        $stmtEmp = $pdo->prepare("DELETE FROM empleados WHERE id = ?");
        $stmtEmp->execute([$id]);

        $pdo->commit();
        $_SESSION['msg'] = "Empleado y todo su historial eliminados.";
        $_SESSION['msg_type'] = "var(--error)";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['msg'] = "Error al eliminar: " . $e->getMessage();
    }
    header("Location: ../views/empleados.php");
    exit();
}
