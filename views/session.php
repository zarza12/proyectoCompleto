<?php
session_start();
echo "si entreakkkkkkkkkkkkkkkkkk555555555555555555555555";
// Recibe JSON desde JavaScript
$data = json_decode(file_get_contents('php://input'), true);

// Guarda el nombre en la sesión si existe
if (isset($data['nombre'])) {
    $_SESSION['nombre'] = $data['nombre'];
    http_response_code(200); // OK
    echo 'Sesión iniciada correctamente';
} else {
    http_response_code(400); // Bad request
    echo 'Faltan datos';
}
exit;
