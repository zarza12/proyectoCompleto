<?php
session_start(); // Necesario para acceder a la sesión

// Elimina todas las variables de sesión
session_unset();

// Destruye la sesión
session_destroy();

// Opcionalmente, puedes enviar una respuesta
http_response_code(200);
echo "<script> window.location.href = 'index.php';</script>";
exit;
?>
