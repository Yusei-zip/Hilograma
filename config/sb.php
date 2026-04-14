<?php
$host = 'localhost';
$db   = 'respaldo';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
    ]);
} catch (PDOException $e) {
    // Redirección directa a tu página de error personalizada
    // Usamos una ruta relativa o absoluta según tu estructura
    header("Location: /asistencia_mantenimiento/views/error_conexion.php");
    exit(); // IMPORTANTE: Detiene el script para que no intente cargar nada más
}