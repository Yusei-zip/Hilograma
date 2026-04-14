<?php
session_start();

// Si no existe la sesión, lo mandamos al login de una
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit();
}

include_once '../config/db.php';
?>
<!DOCTYPE html>
<html lang="es" ddata-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sector de pruebas</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <link rel="stylesheet" href="../public/css/remixicon.css">
    
</head>
<body>
    <script src="../public/js/loadtheme.js">//Cambiar el tema</script>
    <form method="post">
        <input type="submit" name="boton" value="pagina1">
        <input type="submit" name="boton1" value="Asistencia masiva">
    </form>
    


    <?php

    if($_POST['boton'] == 'pagina1'){
        include 'asistencias.php';
    }

?>


<script>
    document.getElementById('boton').addEventListener('click', function(){
this.style.display = 'none';
this.disabled = true;
 })
</script>






   
   
    

    
    
    
</body>
</html>