<!DOCTYPE html>
<html lang="es" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso al Sistema | Asistencia</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="icon" type="image/svg+xml" href="public/img/favicon.svg">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background: var(--bg-body);
            /* Usando tu fondo oscuro */
            margin: 0;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 40px;
            background: var(--bg-card);
            border-radius: 20px;
            box-shadow: 0 15px 35px var(--success, 0.10, 0, 0, 0.5);
            border: 1px solid var(--border);
            text-align: center;
        }

        .login-card h1 {
            margin-bottom: 10px;
            color: var(--text-main);
        }

        .login-card p {
            color: var(--text-muted);
            margin-bottom: 30px;
        }

        .form-group {
            text-align: left;
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .input-login {
            width: 100%;
            padding: 12px 15px;
            background: var(--bg-body);
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--text-main);
            box-sizing: border-box;
        }

        .input-login:focus {
            border-color: var(--accent);
            outline: none;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: var(--accent);
            border: none;
            border-radius: 10px;
            color: #000;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            filter: brightness(1.1);
        }

        .error-msg {
            background: rgba(255, 71, 87, 0.1);
            color: #ff4757;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.85rem;
        }
    </style>
</head>

<body>
    <script src="../public/js/randomTheme.js">//Cambiar el tema</script>

    <div class="login-card">
        <h1>Bienvenido</h1>
        <p>Introduce tus credenciales para continuar</p>

        <?php session_start();
        if (isset($_SESSION['error_login'])): ?>
            <div class="error-msg"> <?= $_SESSION['error_login'];
            unset($_SESSION['error_login']); ?> </div>
        <?php endif; ?>

        <form action="logic/auth_controller.php" method="POST">
            <div class="form-group">
                <label>Usuario</label>
                <input type="text" name="usuario" class="input-login" required autofocus placeholder="Ej: admin">
            </div>
            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" class="input-login" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn-login">ENTRAR AL PANEL</button>
        </form>
    </div>
    <script>
        document.documentElement.setAttribute('data-theme', localStorage.getItem('theme') || 'dark');
    </script>

</body>

</html>