<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once  '../../controllers/daoPersonas.php';
include_once  '../../models/Personas.php';

if (isset($_GET['idEliminar'])) {
    $id = $_GET['idEliminar'];
    $daoPersona = new daoPersonas();
    $Elimino = $daoPersona->eliminarPersona($id);
    if($Elimino){
        echo "
        <script>
            alert('Registro elimino');
            window.location.href = 'eliminarPersonal.php';
        </script>";
        
    }else{
        mostrarMensaje('No se puedo eliminar');
    }
    
} else {
   
}


$daoPersona = new daoPersonas();
$listarPersonas = $daoPersona->listarPersonal();

// Crear un array para los datos de JavaScript
$personasJS = [];
foreach ($listarPersonas as $persona) {
    $personasJS[] = [
        'id' => $persona->getId(),
        'nombre' => $persona->getNombreCompleto(),
        'dni' => $persona->getId(), // Asumiendo que el ID es el DNI
        'fechaNacimiento' => $persona->getFechaNacimiento(),
        'genero' => $persona->getGenero(),
        'puesto' => $persona->getPuesto(),
        'fechaIngreso' => $persona->getFechaIngreso(),
        'email' => $persona->getCorreoElectronico(),
        'telefono' => $persona->getTelefonoCelular(),
        'telefonoEmergencia' => $persona->getTelefonoEmergencia(),
        'direccion' => $persona->getDireccion()
    ];
   
}

// Convertir a JSON para JavaScript
$personasJSON = json_encode($personasJS);

// Función para generar el HTML de la tabla
function lisPer($listarPersonas) {
    $html = '';
    
    foreach ($listarPersonas as $persona) {
        // Usar los nombres correctos de los métodos getter
        $claseEstado = strtolower(str_replace(' ', '-', $persona->getPuesto()));
        
        if($claseEstado==='agrónomo'){
            $claseEstado='agronomo';
        }

        
        
        $html .= '<tr onclick="mostrarModalEliminar(\'' . $persona->getId() . '\')" id="fila-' . $persona->getId() . '">';
        $html .= '<td>' . $persona->getId() . '</td>';
        $html .= '<td>' . $persona->getNombreCompleto() . '</td>'; 
        $html .= '<td><span class="estado estado-' . $claseEstado . '">' . $persona->getPuesto() . '</span></td>';
        $html .= '<td>' . $persona->getFechaIngreso() . '</td>';
        $html .= '<td>' . $persona->getTelefonoCelular() . '</td>'; 
        $html .= '<td>
                    <button class="btn-accion btn-eliminar" onclick="mostrarModalEliminar(\'' . $persona->getId() . '\')"><i class="fas fa-trash"></i></button>
                  </td>';
        $html .= '</tr>';
    }
    return $html;
}

// Generar el HTML de la tabla
$htmlListado = lisPer($listarPersonas);

?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Personal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --color-primario: #4A235A; /* Color zarzamora oscuro */
            --color-secundario: #7D3C98; /* Color zarzamora medio */
            --color-acento: #A569BD; /* Color zarzamora claro */
            --color-resalte: #D2B4DE; /* Color zarzamora muy claro */
            --color-texto: #FFFFFF;
            --color-texto-oscuro: #333333;
            --color-texto-secundario: #666666;
            --color-borde: #E0E0E0;
            --color-fondo: #F9F9F9;
            --color-exito: #2ECC71;
            --color-advertencia: #F39C12;
            --color-peligro: #E74C3C;
            --fuente-principal: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            --sombra: 0 4px 6px rgba(0, 0, 0, 0.1);
            --sombra-hover: 0 6px 12px rgba(0, 0, 0, 0.15);
            --borde-radio: 8px;
            --transicion: all 0.3s ease;
            --menu-width-closed: 70px;
            --menu-width-open: 270px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: var(--fuente-principal);
            background-color: var(--color-fondo);
            color: var(--color-texto-oscuro);
            line-height: 1.6;
        }
        
        /* Barra superior */
        .barra-superior {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--color-primario), var(--color-acento), var(--color-secundario));
            z-index: 1000;
        }
        
        /* Información de usuario */
        .info-usuario {
            position: fixed;
            top: 15px;
            right: 20px;
            display: flex;
            align-items: center;
            background-color: var(--color-primario);
            padding: 6px 15px;
            border-radius: 20px;
            color: var(--color-texto);
            font-size: 14px;
            box-shadow: var(--sombra);
            z-index: 990;
            transition: var(--transicion);
        }
        
        .info-usuario:hover {
            background-color: var(--color-secundario);
            transform: translateY(-2px);
        }
        
        /* Clase para bloquear scroll cuando el modal está abierto */
        body.modal-abierto {
            overflow: hidden;
        }
        
        .avatar-usuario {
            width: 28px;
            height: 28px;
            background-color: var(--color-acento);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-weight: bold;
        }
        
        /* Contenedor principal */
        .contenedor {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
            padding-top: 50px; /* Espacio para la barra superior */
            margin-left: var(--menu-width-closed);
            width: calc(100% - var(--menu-width-closed));
            transition: margin-left 0.3s ease, width 0.3s ease;
        }
        
        /* Encabezado de la página */
        .encabezado-pagina {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 1px solid var(--color-borde);
            padding-bottom: 15px;
        }
        
        .titulo-pagina {
            display: flex;
            align-items: center;
        }
        
        .titulo-pagina h1 {
            color: var(--color-primario);
            font-size: 28px;
            margin-right: 15px;
        }
        
        .icono-seccion {
            font-size: 24px;
            color: var(--color-acento);
            margin-right: 15px;
        }
        
        .botones-accion {
            display: flex;
            gap: 10px;
        }
        
        /* Filtro y búsqueda */
        .filtro-busqueda {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .busqueda {
            display: flex;
            align-items: center;
            background-color: white;
            border: 1px solid var(--color-borde);
            border-radius: var(--borde-radio);
            padding: 10px 15px;
            width: 300px;
            box-shadow: var(--sombra);
        }
        
        .busqueda input {
            border: none;
            background: none;
            outline: none;
            font-size: 14px;
            padding: 0 10px;
            flex: 1;
        }
        
        .busqueda i {
            color: var(--color-secundario);
        }
        
        .filtros {
            display: flex;
            gap: 15px;
        }
        
        .filtro {
            position: relative;
        }
        
        .filtro select {
            appearance: none;
            background-color: white;
            border: 1px solid var(--color-borde);
            border-radius: var(--borde-radio);
            padding: 10px 35px 10px 15px;
            font-size: 14px;
            color: var(--color-texto-oscuro);
            cursor: pointer;
            outline: none;
            box-shadow: var(--sombra);
            min-width: 150px;
        }
        
        .filtro::after {
            content: '\f0d7';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            color: var(--color-secundario);
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
        }
        
        /* Tabla de registros */
        .tabla-contenedor {
            background-color: white;
            border-radius: var(--borde-radio);
            box-shadow: var(--sombra);
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .tabla-registros {
            width: 100%;
            border-collapse: collapse;
        }
        
        .tabla-registros th,
        .tabla-registros td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--color-borde);
        }
        
        .tabla-registros th {
            background-color: var(--color-primario);
            color: var(--color-texto);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
        }
        
        .tabla-registros tr {
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .tabla-registros tr:hover {
            background-color: #f5f5f5;
        }
        
        .tabla-registros .fila-activa {
            background-color: rgba(165, 105, 189, 0.1);
            border-left: 4px solid var(--color-acento);
        }
        
        .tabla-registros .fila-activa:hover {
            background-color: rgba(165, 105, 189, 0.15);
        }
        
        .tabla-registros td:last-child {
            text-align: center;
            white-space: nowrap;
        }
        
        /* Acciones en tabla */
        .btn-accion {
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
            font-size: 16px;
            transition: var(--transicion);
            color: var(--color-texto-secundario);
            border-radius: 4px;
        }
        
        .btn-accion:hover {
            transform: translateY(-2px);
        }
        
        .btn-ver {
            color: var(--color-acento);
        }
        
        .btn-ver:hover {
            color: var(--color-secundario);
        }
        
        .btn-eliminar {
            color: var(--color-peligro);
        }
        
        /* Paginación */
        .paginacion {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-top: 20px;
            gap: 5px;
        }
        
        .btn-pagina {
            background-color: white;
            border: 1px solid var(--color-borde);
            border-radius: 4px;
            padding: 8px 12px;
            cursor: pointer;
            transition: var(--transicion);
            color: var(--color-texto-oscuro);
            font-weight: 500;
        }
        
        .btn-pagina:hover {
            background-color: var(--color-resalte);
            color: var(--color-primario);
        }
        
        .btn-pagina.activa {
            background-color: var(--color-primario);
            color: white;
            border-color: var(--color-primario);
        }
        
        /* Tarjeta de Detalles del Personal */
        .tarjeta-detalles {
            background-color: white;
            border-radius: var(--borde-radio);
            box-shadow: var(--sombra);
            overflow: hidden;
            margin-top: 30px;
            display: none;
            transition: all 0.3s ease;
            border-top: 5px solid var(--color-acento);
        }
        
        .tarjeta-cabecera {
            background: linear-gradient(135deg, var(--color-secundario), var(--color-primario));
            color: white;
            padding: 25px;
            position: relative;
            text-align: center;
        }
        
        .tarjeta-cabecera .avatar {
            width: 100px;
            height: 100px;
            background-color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 40px;
            color: var(--color-secundario);
            border: 4px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .tarjeta-cabecera h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        
        .tarjeta-cabecera .puesto {
            display: inline-block;
            margin-top: 5px;
            background-color: rgba(255, 255, 255, 0.25);
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .tarjeta-cabecera .id-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: rgba(255, 255, 255, 0.2);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .secciones-tabs {
            background-color: var(--color-primario);
            display: flex;
            padding: 0 20px;
        }
        
        .tab {
            padding: 15px 20px;
            cursor: pointer;
            color: rgba(255, 255, 255, 0.7);
            font-weight: 500;
            position: relative;
            transition: all 0.3s ease;
        }
        
        .tab.activo {
            color: white;
        }
        
        .tab.activo::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: var(--color-acento);
        }
        
        .seccion-detalle {
            padding: 25px;
            display: none;
        }
        
        .seccion-detalle.activa {
            display: block;
            animation: fadeIn 0.5s;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .info-grupo {
            margin-bottom: 30px;
        }
        
        .titulo-seccion {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            color: var(--color-primario);
            font-size: 18px;
            font-weight: 600;
            border-bottom: 1px solid var(--color-borde);
            padding-bottom: 8px;
        }
        
        .titulo-seccion i {
            margin-right: 10px;
            font-size: 20px;
            color: var(--color-acento);
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .info-item {
            background-color: rgba(210, 180, 222, 0.1);
            border-left: 3px solid var(--color-acento);
            padding: 12px 15px;
            border-radius: 4px;
        }
        
        .info-item .etiqueta {
            color: var(--color-texto-secundario);
            font-size: 12px;
            margin-bottom: 5px;
            display: block;
        }
        
        .info-item .valor {
            color: var(--color-texto-oscuro);
            font-weight: 500;
            font-size: 16px;
        }
        
        .direccion-completa {
            grid-column: 1 / -1;
        }
        
        .grafico-container {
            height: 300px;
            margin-top: 20px;
        }
        
        .estadistica-item {
            background-color: white;
            border-radius: var(--borde-radio);
            padding: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 15px;
        }
        
        .estadistica-valor {
            font-size: 28px;
            font-weight: 700;
            color: var(--color-secundario);
            margin: 10px 0;
        }
        
        .estadistica-etiqueta {
            color: var(--color-texto-secundario);
            font-size: 14px;
        }
        
        .estadistica-icono {
            background-color: rgba(165, 105, 189, 0.1);
            color: var(--color-acento);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 10px;
        }
        
        .botones-footer {
            background-color: #f9f9f9;
            padding: 15px 25px;
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            border-top: 1px solid var(--color-borde);
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: var(--borde-radio);
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            transition: var(--transicion);
        }
        
        .btn i {
            margin-right: 8px;
        }
        
        .btn-primario {
            background-color: var(--color-primario);
            color: white;
        }
        
        .btn-primario:hover {
            background-color: var(--color-secundario);
            transform: translateY(-2px);
            box-shadow: var(--sombra-hover);
        }
        
        .btn-secundario {
            background-color: #f1f1f1;
            color: var(--color-texto-oscuro);
        }
        
        .btn-cerrar {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transicion);
        }
        
        .btn-cerrar:hover {
            background: rgba(255, 255, 255, 0.4);
            transform: rotate(90deg);
        }
        
        /* Estados de puesto */
        .estado {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-align: center;
        }
        
        .estado-agronomo {
            background-color: rgba(46, 204, 113, 0.2);
            color: #27ae60;
        }
        
        .estado-supervisor {
            background-color: rgba(52, 152, 219, 0.2);
            color: #2980b9;
        }
        
        /* Modal de eliminación */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            animation: fadeIn 0.4s ease;
            backdrop-filter: blur(5px);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .modal-contenido {
            background-color: white;
            border-radius: 15px;
            width: 550px;
            max-width: 95%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 2px solid rgba(231, 76, 60, 0.3);
        }
        
        @keyframes popIn {
            from { transform: scale(0.8); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        
        .modal-cabecera {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            padding: 20px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            border-bottom: 4px solid rgba(255, 255, 255, 0.1);
        }
        
        .modal-cabecera h3 {
            margin: 0;
            font-size: 20px;
            display: flex;
            align-items: center;
            font-weight: 600;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
            letter-spacing: 0.5px;
        }
        
        .modal-cabecera h3 i {
            margin-right: 12px;
            font-size: 24px;
            background-color: rgba(255, 255, 255, 0.15);
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }
        
        .modal-cabecera .btn-cerrar {
            background-color: rgba(255, 255, 255, 0.2);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            border: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        
        .modal-cabecera .btn-cerrar:hover {
            background-color: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }
        
        .modal-cuerpo {
            padding: 25px;
        }
        
        .advertencia-texto {
            border-left: 5px solid var(--color-peligro);
            padding: 15px 20px;
            background-color: rgba(231, 76, 60, 0.1);
            margin-bottom: 25px;
            font-size: 15px;
            color: var(--color-texto-oscuro);
            border-radius: 0 8px 8px 0;
            line-height: 1.5;
        }
        
        .info-eliminacion {
            background-color: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
            border: 1px solid #eee;
        }
        
        .info-eliminar-item {
            margin-bottom: 15px;
            display: flex;
            font-size: 15px;
            border-bottom: 1px dashed #eee;
            padding-bottom: 12px;
        }
        
        .info-eliminar-item:last-child {
            margin-bottom: 0;
            border-bottom: none;
            padding-bottom: 0;
        }
        
        .info-eliminar-item .etiqueta {
            width: 160px;
            font-weight: 600;
            color: var(--color-primario);
        }
        
        .info-eliminar-item .valor {
            flex: 1;
            color: var(--color-texto-oscuro);
            font-weight: 500;
        }
        
        .modal-pie {
            padding: 20px 25px;
            background-color: #f8f9fa;
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            border-top: 1px solid #eee;
        }
        
        .btn-peligro {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(231, 76, 60, 0.3);
            transition: all 0.3s;
        }
        
        .btn-peligro:hover {
            background: linear-gradient(135deg, #c0392b, #a93226);
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(231, 76, 60, 0.4);
        }
        
        .btn-secundario {
            background-color: #f1f2f6;
            color: #636e72;
            padding: 12px 24px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
            border: 1px solid #dfe6e9;
        }
        
        .btn-secundario:hover {
            background-color: #dfe6e9;
            color: #2d3436;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .contenedor {
                width: 100%;
                margin-left: 0;
                padding: 20px;
                padding-top: 60px;
            }
            
            .filtro-busqueda {
                flex-direction: column;
                align-items: stretch;
                gap: 10px;
            }
            
            .busqueda {
                width: 100%;
            }
            
            .filtros {
                flex-wrap: wrap;
            }
            
            .filtro select {
                width: 100%;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .tabla-registros {
                display: block;
                overflow-x: auto;
            }
            
            .tarjeta-cabecera {
                padding: 15px;
            }
            
            .tarjeta-cabecera .avatar {
                width: 80px;
                height: 80px;
                font-size: 30px;
            }
            
            .secciones-tabs {
                overflow-x: auto;
                white-space: nowrap;
                padding: 0 10px;
            }
            
            .tab {
                padding: 12px 15px;
                font-size: 14px;
            }
            
            .seccion-detalle {
                padding: 15px;
            }
            
            .botones-footer {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
            
            .info-usuario {
                top: 15px;
                right: 15px;
            }
        }
    </style>
</head>
<body>

<?php include '../../views/menuA.php'; ?>
    <!-- Barra superior con el degradado -->
    <div class="barra-superior"></div>
    
    
    <!-- Modal para confirmar eliminación -->
    <div id="modalEliminar" class="modal" style="display: none;">
        <div class="modal-contenido">
            <div class="modal-cabecera">
                <h3><i class="fas fa-exclamation-triangle"></i> Confirmación de Eliminación</h3>
                <button class="btn-cerrar" onclick="cerrarModalEliminar()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-cuerpo">
                <p class="advertencia-texto"><i class="fas fa-info-circle"></i> <strong>¡Atención!</strong> Después de eliminar este registro, no será posible recuperarlo. Por favor, confirme que desea eliminar este personal.</p>
                
                <div class="info-eliminacion">
                    <div class="info-eliminar-item">
                        <span class="etiqueta"><i class="fas fa-id-card"></i> ID/DNI:</span>
                        <span class="valor" id="eliminarId">P001</span>
                    </div>
                    <div class="info-eliminar-item">
                        <span class="etiqueta"><i class="fas fa-user"></i> Nombre:</span>
                        <span class="valor" id="eliminarNombre">María López García</span>
                    </div>
                    <div class="info-eliminar-item">
                        <span class="etiqueta"><i class="fas fa-calendar-alt"></i> Fecha Nacimiento:</span>
                        <span class="valor" id="eliminarFechaNacimiento">15/04/1990</span>
                    </div>
                    <div class="info-eliminar-item">
                        <span class="etiqueta"><i class="fas fa-venus-mars"></i> Género:</span>
                        <span class="valor" id="eliminarGenero">Femenino</span>
                    </div>
                    <div class="info-eliminar-item">
                        <span class="etiqueta"><i class="fas fa-briefcase"></i> Puesto:</span>
                        <span class="valor" id="eliminarPuesto">Agrónomo</span>
                    </div>
                </div>
            </div>
            <div class="modal-pie">
                <button class="btn btn-secundario" onclick="cerrarModalEliminar()"><i class="fas fa-times"></i> Cancelar</button>
                <button class="btn btn-peligro" onclick="confirmarEliminar()"><i class="fas fa-trash"></i> Eliminar</button>
            </div>
        </div>
    </div>

    <div class="contenedor">
        <div class="encabezado-pagina">
            <div class="titulo-pagina">
                <i class="fas fa-users icono-seccion"></i>
                <h1>Eliminar Personal</h1>
            </div>
            <div class="botones-accion">
                <button class="btn btn-exportar"><i class="fas fa-file-export"></i> Exportar Datos</button>
            </div>
        </div>
        
        <div class="filtro-busqueda">
            <div class="busqueda">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Buscar personal..." id="buscarPersonal">
            </div>
            <div class="filtros">
                <div class="filtro">
                    <select id="filtroPuesto">
                        <option value="">Todos los puestos</option>
                        <option value="agrónomo">Agrónomo</option>
                        <option value="supervisor">Supervisor</option>
                    </select>
                </div>
                <div class="filtro">
                    <select id="filtroAntigüedad">
                        <option value="">Toda la antigüedad</option>
                        <option value="nuevo">Menos de 1 año</option>
                        <option value="intermedio">1-3 años</option>
                        <option value="veterano">Más de 3 años</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Tabla de Registros -->
        <div class="tabla-contenedor">
            <table class="tabla-registros">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre Completo</th>
                        <th>Puesto</th>
                        <th>Fecha Ingreso</th>
                        <th>Teléfono</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php echo $htmlListado; ?>
                </tbody>
            </table>
        </div>
        
        <div class="paginacion">
            <button class="btn-pagina"><i class="fas fa-chevron-left"></i></button>
            <button class="btn-pagina activa">1</button>
            <button class="btn-pagina">2</button>
            <button class="btn-pagina">3</button>
            <button class="btn-pagina"><i class="fas fa-chevron-right"></i></button>
        </div>
        

    </div>
    
    <script>
        // Datos de ejemplo para simular personal
        const personalEjemplo = <?php echo $personasJSON; ?>;
        

        
        // Inicializar búsqueda
        document.getElementById('buscarPersonal').addEventListener('input', function(e) {
            const terminoBusqueda = e.target.value.toLowerCase();
            const filas = document.querySelectorAll('.tabla-registros tbody tr');
            
            filas.forEach(fila => {
                const texto = fila.textContent.toLowerCase();
                if (texto.includes(terminoBusqueda)) {
                    fila.style.display = '';
                } else {
                    fila.style.display = 'none';
                }
            });
        });
        
        // Inicializar filtros
        document.getElementById('filtroPuesto').addEventListener('change', function() {
            filtrarTabla();
        });
        
        document.getElementById('filtroAntigüedad').addEventListener('change', function() {
            filtrarTabla();
        });
        
        function filtrarTabla() {
            const filtroPuesto = document.getElementById('filtroPuesto').value.toLowerCase();
            const filtroAntigüedad = document.getElementById('filtroAntigüedad').value;
            const filas = document.querySelectorAll('.tabla-registros tbody tr');
            
            filas.forEach(fila => {
                let mostrar = true;
                
                // Filtrar por puesto
                if (filtroPuesto) {
                    const puestoCelda = fila.cells[2].textContent.toLowerCase();
                    if (!puestoCelda.includes(filtroPuesto)) {
                        mostrar = false;
                    }
                }
                
                // Filtrar por antigüedad (simulación simple)
                if (filtroAntigüedad) {
                    const fechaIngreso = new Date(fila.cells[3].textContent.split('/').reverse().join('-'));
                    const hoy = new Date();
                    const diferenciaMeses = (hoy.getFullYear() - fechaIngreso.getFullYear()) * 12 + 
                                          (hoy.getMonth() - fechaIngreso.getMonth());
                    
                    if (filtroAntigüedad === 'nuevo' && diferenciaMeses >= 12) {
                        mostrar = false;
                    } else if (filtroAntigüedad === 'intermedio' && (diferenciaMeses < 12 || diferenciaMeses > 36)) {
                        mostrar = false;
                    } else if (filtroAntigüedad === 'veterano' && diferenciaMeses <= 36) {
                        mostrar = false;
                    }
                }
                
                fila.style.display = mostrar ? '' : 'none';
            });
        }
        
        // Variables para el modal de eliminación
        let idPersonalEliminar = null;
        
        var IDelimiar=-1;
        // Función para mostrar el modal de eliminación
        function mostrarModalEliminar(id) {
            IDelimiar=id;

            event.stopPropagation(); // Evitar que se active la selección de fila
            
            // Buscar el personal en los datos
            const personal = personalEjemplo.find(p => p.id === id);
            
            if (personal) {
                // Guardar el ID para usarlo en la confirmación
                idPersonalEliminar = id;
                
                // Rellenar datos del modal
                document.getElementById('eliminarId').textContent = personal.id;
                document.getElementById('eliminarNombre').textContent = personal.nombre;
                document.getElementById('eliminarFechaNacimiento').textContent = personal.fechaNacimiento;
                document.getElementById('eliminarGenero').textContent = personal.genero;
                document.getElementById('eliminarPuesto').textContent = personal.puesto;
                
                // Mostrar el modal
                document.getElementById('modalEliminar').style.display = 'flex';
                
                // Añadir clase para evitar el scroll en el fondo
                document.body.classList.add('modal-abierto');
            }
        }
        
        // Función para cerrar el modal de eliminación
        function cerrarModalEliminar() {
            document.getElementById('modalEliminar').style.display = 'none';
            document.body.classList.remove('modal-abierto');
            idPersonalEliminar = null;
        }
        
        // Función para confirmar la eliminación
        function confirmarEliminar() {
            if (idPersonalEliminar) {
                // Mostrar diálogo de confirmación del sistema
                const confirmacion = confirm("¿Está seguro que desea eliminar este registro permanentemente?");
                
                if (confirmacion) {
                    // En un caso real, aquí enviarías una solicitud al servidor
                    
                    // Eliminar la fila de la tabla
                    document.getElementById(`fila-${idPersonalEliminar}`).remove();
                    window.location.href = 'eliminarPersonal.php?idEliminar=' + IDelimiar;
                    
                    // Si estamos mostrando los detalles de este personal, cerrarlos
                    if (document.getElementById('idPersonaDetalle').textContent === idPersonalEliminar) {
                        cerrarDetalles();
                    }
                    
                    // Cerrar el modal
                    cerrarModalEliminar();
                    
                    // Mostrar mensaje de éxito
                    alert(`Personal ${idPersonalEliminar} eliminado correctamente`);
                } else {
                    // El usuario ha cancelado la eliminación
                    console.log("Eliminación cancelada por el usuario");
                }
            }
        }
    </script>
</body>
</html>