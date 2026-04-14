<?php
include '../includes/reutilizable/session_start.php';


$empleados = $pdo->query("SELECT id, nombre_completo, is_active FROM empleados WHERE is_active = 1 ORDER BY nombre_completo ASC")->fetchAll();

// 1. Configuraciones de QoL y Captura de Datos
$opciones_rango = [10, 20, 50, 100, 'todos'];
$rango = $_GET['rango'] ?? 10;
$id_empleado = $_GET['id_empleado'] ?? '';
$estado = $_GET['estado'] ?? '';
$mes = $_GET['mes'] ?? ''; // Nuevo filtro de mes

// 2. Construcción de Filtros SQL para la TABLA
$where = [];
$params = [];
if (!empty($id_empleado)) { $where[] = "a.id_empleado = ?"; $params[] = $id_empleado; }
if (!empty($estado)) { $where[] = "a.estado = ?"; $params[] = $estado; }
if (!empty($mes)) { $where[] = "MONTH(a.fecha) = ?"; $params[] = $params_mes = $mes; }

$where_sql = $where ? " WHERE " . implode(" AND ", $where) : "";

// 3. Conteo Total (para paginación)
$sql_count = "SELECT COUNT(*) FROM asistencias a $where_sql";
$stmt_count = $pdo->prepare($sql_count);
$stmt_count->execute($params);
$total_registros = $stmt_count->fetchColumn();


if ($rango === 'todos') {
    $total_paginas = 1;
    $pagina_actual = 1;
    $limit_sql = "";
} else {
    $rango = (int)$rango;
    $total_paginas = ceil($total_registros / $rango);
    $pagina_actual = isset($_GET['p']) ? (int)$_GET['p'] : 1;
    if ($pagina_actual < 1) $pagina_actual = 1;
    if ($pagina_actual > $total_paginas && $total_paginas > 0) $pagina_actual = $total_paginas;
    
    $offset = ($pagina_actual - 1) * $rango;
    $limit_sql = " LIMIT $rango OFFSET $offset";
}

$sql = "SELECT a.*, e.nombre_completo 
        FROM asistencias a 
        JOIN empleados e ON a.id_empleado = e.id 
        $where_sql 
        ORDER BY a.fecha DESC $limit_sql";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$registros = $stmt->fetchAll();

// 6. URL Base para mantener filtros en los links
$query_params = $_GET;
unset($query_params['p']);
$base_url = "historial.php?" . http_build_query($query_params);