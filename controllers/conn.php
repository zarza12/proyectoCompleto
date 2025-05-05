<?php
function getConnection() {
     $host     = getenv('DB_HOST');
    $port     = getenv('DB_PORT');
    $user     = getenv('DB_USER');
    $pass     = getenv('DB_PASS');
    $database = getenv('DB_NAME');

    $conn = mysqli_connect($host, $user, $pass, $database, (int)$port);

    if (!$conn) {
        die("Conexión fallida: " . mysqli_connect_error());
    }

    return $conn;
}

function mostrarMensaje($mensaje) {
    echo "<script>alert('$mensaje');</script>";
}

function mostrarMensajeColor($mensaje, $tipo) {
    // Determinar el color según el tipo de mensaje
    $color = '';
    switch ($tipo) {
        case 'error':
            $color = '#ff0000'; // Rojo
            break;
        case 'warning':
            $color = '#ffa500'; // Naranja
            break;
        case 'success':
            $color = '#008000'; // Verde
            break;
        default:
            $color = '#0000ff'; // Azul (info)
    }
    
    // Crear alerta personalizada con el color elegido
    echo "<script>
        var div = document.createElement('div');
        div.style.position = 'fixed';
        div.style.top = '20%';
        div.style.left = '50%';
        div.style.transform = 'translate(-50%, -50%)';
        div.style.backgroundColor = '$color';
        div.style.color = 'white';
        div.style.padding = '20px';
        div.style.borderRadius = '5px';
        div.style.boxShadow = '0 4px 8px rgba(0,0,0,0.2)';
        div.style.zIndex = '1000';
        div.style.textAlign = 'center';
        div.innerHTML = '$mensaje';
        
        document.body.appendChild(div);
        
        setTimeout(function() {
            div.style.display = 'none';
            document.body.removeChild(div);
        }, 3000);
    </script>";
}
?>
