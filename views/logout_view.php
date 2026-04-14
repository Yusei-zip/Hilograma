<!DOCTYPE html>
<html lang="es" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>Sesión Cerrada | Sistema</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <link rel="icon" type="image/svg+xml" href="../public/img/favicon.svg">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background: var(--bg-body);
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            overflow: hidden;
        }
        .logout-container {
            text-align: center;
            background: var(--bg-card);
            padding: 50px;
            border-radius: 30px;
            border: 1px solid var(--border);
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            max-width: 400px;
        }
        .icon-box {
            color: var(--error);
            margin-bottom: 20px;
        }
        .icon-box svg { width: 80px; height: 80px; filter: drop-shadow(0 0 10px rgba(255,71,87,0.3)); }

        h1 { color: var(--text-main); font-size: 1.8rem; margin-bottom: 10px; }
        p { color: var(--text-muted); margin-bottom: 30px; }

        /* El Contador Coqueto */
        .timer-wrapper {
            position: relative;
            width: 100px;
            height: 100px;
            margin: 0 auto 30px;
        }
        .timer-number {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 2rem;
            font-weight: bold;
            color: var(--accent);
        }
        .timer-svg {
            transform: rotate(-90deg);
        }
        .timer-circle {
            fill: none;
            stroke: var(--border);
            stroke-width: 6;
        }
        .timer-progress {
            fill: none;
            stroke: var(--accent);
            stroke-width: 6;
            stroke-linecap: round;
            stroke-dasharray: 283; /* 2 * PI * radio (45) */
            transition: stroke-dashoffset 1s linear;
        }

        .btn-direct {
            color: var(--accent);
            text-decoration: none;
            font-size: 0.9rem;
            border-bottom: 1px solid transparent;
            transition: 0.3s;
        }
        .btn-direct:hover { border-bottom-color: var(--accent); opacity: 0.8; }
    </style>
</head>
<body>

<div class="logout-container">
    <div class="icon-box">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path>
            <line x1="12" y1="2" x2="12" y2="12"></line>
        </svg>
    </div>
    
    <h1>¡Hasta pronto!</h1>
    <p>Has cerrado sesión correctamente.</p>

    <div class="timer-wrapper">
        <svg class="timer-svg" width="100" height="100">
            <circle class="timer-circle" cx="50" cy="50" r="45"></circle>
            <circle id="progress" class="timer-progress" cx="50" cy="50" r="45"></circle>
        </svg>
        <div id="countdown" class="timer-number">10</div>
    </div>

    <a href="../login.php" class="btn-direct">¿Quieres volver a entrar ahora?</a>
</div>

<script>
    let timeLeft = 10;
    const countdownEl = document.getElementById('countdown');
    const progressEl = document.getElementById('progress');
    const totalDash = 283;

    const timer = setInterval(() => {
        timeLeft--;
        countdownEl.innerText = timeLeft;
        
        // Actualizar el círculo de progreso
        const offset = totalDash - (timeLeft * (totalDash / 10));
        progressEl.style.strokeDashoffset = offset;

        if (timeLeft <= 0) {
            clearInterval(timer);
            window.location.href = '../login.php';
        }
    }, 1000);
</script>

<script>
    document.documentElement.setAttribute('data-theme', localStorage.getItem('theme') || 'dark');
</script>

</body>
</html>