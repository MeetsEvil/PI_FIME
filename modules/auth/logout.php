<?php
session_start();
session_unset();    // Limpia variables de sesión
session_destroy();  // Destruye la sesión
header("Location: index.php"); // Redirige al login/index
exit();
?>
