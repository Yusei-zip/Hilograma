<?php
include_once '../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['mensaje' => 'No hay datos']);
    exit;
}

try {
    $pdo->beginTransaction();
    $sql = "INSERT INTO asistencias (id_empleado, fecha, estado, sub_estado, telefono_entregado) 
            VALUES (:emp, :fecha, :estado, :sub, :tel) 
            ON DUPLICATE KEY UPDATE 
                estado = VALUES(estado), 
                sub_estado = VALUES(sub_estado), 
                telefono_entregado = VALUES(telefono_entregado)";
    
    $stmt = $pdo->prepare($sql);

    foreach ($data as $reg) {
        $stmt->execute([
            ':emp'    => $reg['empleado_id'],
            ':fecha'  => $reg['fecha'],
            ':estado' => $reg['estado'],
            ':sub'    => $reg['sub_estado'],
            ':tel'    => $reg['telefono_entregado']
        ]);
    }

    $pdo->commit();
    echo json_encode(['mensaje' => count($data) . ' días procesados con éxito.']);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['mensaje' => 'Error: ' . $e->getMessage()]);
}