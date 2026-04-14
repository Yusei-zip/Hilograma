<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Error de Sistema</title>
    <style>
        body { 
            background: #0f0f0f; 
            color: #fff; 
            font-family: 'Segoe UI', sans-serif; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            height: 100vh; 
            margin: 0; 
        }
        .container { 
            text-align: center; 
            border: 1px solid #333; 
            padding: 50px; 
            border-radius: 20px; 
            background: #1a1a1a;
        }
        h1 { color: #ff4757; font-size: 4rem; margin: 0; }
        p { color: #ccc; font-size: 1.2rem; }
        .btn-retry {
            background: #00d1b2; /* Tu color cyan */
            color: #000;
            padding: 10px 25px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>500</h1>
        <h2>Conexión Denegada</h2>
        <p>Parece que el servidor MySQL (XAMPP) está apagado o las credenciales son incorrectas.</p>
        <a href="../views/dashboard.php" class="btn-retry">REINTENTAR ACCESO</a>
    </div>
</body>
</html>