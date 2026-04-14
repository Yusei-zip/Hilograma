<?php
session_start();
session_unset();
session_destroy();

// Redirigimos a la página visual de despedida
header("Location: ../views/logout_view.php");
exit();