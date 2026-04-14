<?php
session_start();

// Si no existe la sesión, lo mandamos al login de una
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit();
}

include_once '../config/db.php';

$filename = "Asistencias_" . date('Y-m-d_H-i') . ".xls";

// Configuración de cabeceras para descarga inmediata
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=$filename");
header("Pragma: no-cache");
header("Expires: 0");

// Filtros (igual que en historial.php)
$where = [];
$params = [];
if (!empty($_GET['id_empleado'])) {
    $where[] = "a.id_empleado = ?";
    $params[] = $_GET['id_empleado'];
}
if (!empty($_GET['estado'])) {
    $where[] = "a.estado = ?";
    $params[] = $_GET['estado'];
}

$sql = "SELECT a.fecha, e.nombre_completo, a.estado, a.sub_estado, a.telefono_entregado 
        FROM asistencias a JOIN empleados e ON a.id_empleado = e.id";
if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY a.fecha DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$registros = $stmt->fetchAll();

// Crear la tabla de Excel
echo "<table border='1'>";
echo "<tr>
        <th style='background-color: #4CAF50; color: white;'>Fecha</th>
        <th style='background-color: #4CAF50; color: white;'>Empleado</th>
        <th style='background-color: #4CAF50; color: white;'>Estado</th>
        <th style='background-color: #4CAF50; color: white;'>Detalle</th>
        <th style='background-color: #4CAF50; color: white;'>Telefono</th>
      </tr>";

foreach ($registros as $r) {
    $tel = $r['telefono_entregado'] ? 'SI' : 'NO';
    echo "<tr>";
    echo "<td>" . date('d/m/Y', strtotime($r['fecha'])) . "</td>";
    echo "<td>" . utf8_decode($r['nombre_completo']) . "</td>";
    echo "<td>" . $r['estado'] . "</td>";
    echo "<td>" . $r['sub_estado'] . "</td>";
    echo "<td>" . $tel . "</td>";
    echo "</tr>";
}
echo "</table>";
?>