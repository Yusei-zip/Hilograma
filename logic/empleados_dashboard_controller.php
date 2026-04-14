<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include_once '../config/db.php';

// Filtro de cargo por URL
$cargo_filtrado = $_GET['id_cargo'] ?? '';
$cargo_filtrado_act = $_GET['id_cargo'] ?? '';

// 1. OBTENER TODOS LOS CARGOS DINÁMICAMENTE
// Esta consulta trae CUALQUIER cargo que exista en la tabla empleados 
$stmt_counts = $pdo->query("SELECT cargos.nombre_cargo, empleados.id_cargo, COUNT(*) as total 
FROM empleados JOIN cargos ON empleados.id_cargo = cargos.id GROUP BY empleados.id_cargo ORDER BY total DESC;");
$counts = $stmt_counts->fetchAll(PDO::FETCH_ASSOC); 
//solamente para personal activo en nomina.
$stmt_counts_act = $pdo->query("SELECT cargos.nombre_cargo, empleados.id_cargo, COUNT(*) as total 
FROM empleados JOIN cargos ON empleados.id_cargo = cargos.id WHERE empleados.is_active = '1' GROUP BY empleados.id_cargo ORDER BY total DESC;");
$counts_act = $stmt_counts_act->fetchAll(PDO::FETCH_ASSOC); 

$stmt_counts = $pdo->query("SELECT id, COUNT(*) as total FROM cargos GROUP BY id ORDER BY total DESC");
$counts2 = $stmt_counts->fetchAll(PDO::FETCH_ASSOC);

// 2. OBTENER TODOS LOS sub_cargosS DINÁMICAMENTE
$stmt_sub = $pdo->query("SELECT sub_cargos, COUNT(*) as total FROM empleados WHERE sub_cargos IS NOT NULL AND sub_cargos != '' GROUP BY sub_cargos");
$subcounts = $stmt_sub->fetchAll(PDO::FETCH_ASSOC);

// 3. LISTADO DE TRABAJADORES (Filtrado o General)
if ($cargo_filtrado) {
    $stmt_list = $pdo->prepare("SELECT e.* , c.nombre_cargo AS nombre_cargo FROM empleados e INNER JOIN cargos c ON e.id_cargo = c.id 
    WHERE e.id_Cargo = ?  ORDER BY e.nombre_completo ASC;");
    $stmt_list->execute([$cargo_filtrado]);
} else {
    $stmt_list = $pdo->query("SELECT e.*, c.nombre_cargo AS nombre_cargo FROM empleados e INNER JOIN cargos c ON e.id_cargo = c.id ORDER BY e.id_cargo ASC, e.nombre_completo ASC;");
}
$empleados = $stmt_list->fetchAll(PDO::FETCH_ASSOC);

if ($cargo_filtrado_act) {
    $stmt_list_act = $pdo->prepare("c");
    $stmt_list_act->execute([$cargo_filtrado_act]);
} else {
    $stmt_list_act = $pdo->query("SELECT e.*, c.nombre_cargo AS nombre_cargo FROM empleados e INNER JOIN cargos c ON e.id_cargo = c.id ORDER BY e.id_cargo ASC, e.nombre_completo ASC;");
}
$empleados_act = $stmt_list_act->fetchAll(PDO::FETCH_ASSOC);