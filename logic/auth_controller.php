<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['usuario'] ?? '');
    $pass = $_POST['password'] ?? '';

    try {
        // Buscamos al usuario
        $stmt = $pdo->prepare("SELECT id, usuario, password, rol FROM usuarios WHERE usuario = ? LIMIT 1");
        $stmt->execute([$user]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificamos: ¿Existe el usuario? Y ¿La contraseña coincide?
        if ($usuario && password_verify($pass, $usuario['password'])) {
            
            // Regeneramos el ID de sesión por seguridad (evita fijación de sesión)
            session_regenerate_id(true);

            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['usuario'];
            $_SESSION['rol'] = $usuario['rol'];

            header("Location: ../views/dashboard.php");
            exit();
        } else {
            // Mensaje genérico: No decimos si falló el usuario o la clave
            $_SESSION['error_login'] = "Credenciales incorrectas. Intente de nuevo.";
            header("Location: ../login.php");
            exit();
        }

    } catch (PDOException $e) {
        // En producción, nunca mostramos $e->getMessage()
        error_log("Error de Login: " . $e->getMessage());
        $_SESSION['error_login'] = "Servicio no disponible temporalmente.";
        header("Location: ../login.php");
        exit();
    }
} else {
    header("Location: ../login.php");
    exit();
}