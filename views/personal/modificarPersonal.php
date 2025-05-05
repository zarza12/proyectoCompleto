<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once  '../../controllers/daoPersonas.php';
include_once  '../../models/Personas.php';

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
        'direccion' => $persona->getDireccion(),
        'curp' => $persona->setCrup()
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
        $puesto=$persona->getPuesto();
        if($puesto==='Agrónomo'){
            $puesto='agronomo';
        }else{
            $puesto='supervisor';
        }

        
        
        $html .= '<tr onclick="seleccionarRegistro(\'' . $persona->getId() . '\')" id="fila-' . $persona->getId() . '">';
        $html .= '<td>' . $persona->getId() . '</td>';
        $html .= '<td>' . $persona->getNombreCompleto() . '</td>'; 
        $html .= '<td><span class="estado estado-' . $claseEstado . '">' . $persona->getPuesto() . '</span></td>';
        $html .= '<td>' . $persona->getFechaIngreso() . '</td>';
        $html .= '<td>' . $persona->getTelefonoCelular() . '</td>'; 
        $html .= '<td>
                    <button class="btn-accion btn-editar" onclick="seleccionarRegistro(\'' . $persona->getId() . '\')"><i class="fas fa-pen"></i></button>
                    <button class="btn-accion btn-eliminar"><i class="fas fa-trash"></i></button>
                  </td>';
        $html .= '</tr>';
    }
    return $html;
}

// Generar el HTML de la tabla
$htmlListado = lisPer($listarPersonas);


if (isset($_POST['modificarPersonal']) && $_POST['modificarPersonal'] === 'modificarPersonal') {
    $idPersonal = $_POST['idPersonal'];
    $modificarNombreCompleto = $_POST['modificarNombreCompleto'];
    
    $modificarFechaNacimiento = $_POST['modificarFechaNacimiento'];
    $modificarGenero = $_POST['modificarGenero'];
    $modificarPuesto = $_POST['modificarPuesto'];
    $modificarFechaIngreso = $_POST['modificarFechaIngreso'];
    $modificarEmail = $_POST['modificarEmail'];
    $modificarTelefono = $_POST['modificarTelefono'];
    $modificarTelefonoEmergencia = $_POST['modificarTelefonoEmergencia'];
    $modificarDireccion = $_POST['modificarDireccion'];
    $modificarCurp = $_POST['modificarCurp'];

    $persona = new Personas(
        $idPersonal, // id de la persona a modificar
        $modificarNombreCompleto, 
        $modificarFechaNacimiento, 
        $modificarGenero,
        $modificarCurp, 
        $modificarPuesto, 
        $modificarFechaIngreso, 
        $modificarEmail, 
        $modificarTelefono, 
        $modificarTelefonoEmergencia, 
        $modificarDireccion,
        true  // activo
    );
    $daoPersona = new daoPersonas();
    $registo = $daoPersona->modificarPersona($persona);

    if ($registo) {
        echo "<script>alert('Modificado exitoso.'); window.location.href = 'modificarPersonal.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error al modificar el registro.'); window.location.href = 'modificarPersonal.php';</script>";
        exit;
    }
   
}


?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Personal</title>
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
        
        .btn-editar {
            color: var(--color-acento);
        }
        
        .btn-editar:hover {
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
        
        /* Formulario de edición */
        .formulario-edicion {
            background-color: white;
            border-radius: var(--borde-radio);
            box-shadow: var(--sombra);
            padding: 25px;
            margin-top: 30px;
            border-top: 5px solid var(--color-acento);
        }
        
        .titulo-formulario {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--color-borde);
        }
        
        .titulo-formulario h3 {
            color: var(--color-primario);
            font-size: 20px;
            margin-right: 15px;
        }
        
        .id-registro {
            background-color: var(--color-acento);
            color: white;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .detalles-item {
            background-color: rgba(165, 105, 189, 0.1);
            padding: 8px 15px;
            border-radius: var(--borde-radio);
            margin-bottom: 15px;
            font-size: 14px;
            color: var(--color-secundario);
        }
        
        .detalles-item strong {
            color: var(--color-primario);
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--color-texto-oscuro);
        }
        
        .section-title {
            color: var(--color-primario);
            font-size: 18px;
            margin-bottom: 15px;
            border-bottom: 1px solid var(--color-borde);
            padding-bottom: 8px;
        }
        
        .campo-contenedor {
            position: relative;
            display: flex;
            align-items: center;
        }

        .campo-icono {
            position: absolute;
            left: 10px;
            color: var(--color-primario);
            font-size: 16px;
            pointer-events: none;
        }

        .form-input,
        .form-select,
        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="date"],
        select,
        textarea {
            width: 100%;
            padding: 12px 15px 12px 35px;
            border: 1px solid var(--color-borde);
            border-radius: var(--borde-radio);
            font-size: 14px;
            outline: none;
            transition: var(--transicion);
        }
        
        .form-input:focus,
        .form-select:focus,
        input:focus,
        select:focus,
        textarea:focus {
            border-color: var(--color-acento);
            box-shadow: 0 0 0 3px rgba(165, 105, 189, 0.2);
        }
        
        .form-input:disabled,
        input:disabled {
            background-color: #f5f5f5;
            cursor: not-allowed;
        }
        
        .form-select,
        select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%234A235A' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 16px;
            padding-right: 35px;
        }
        
        /* Botones */
        .botones-form, .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--color-borde);
        }
        
        .btn, 
        button[type="button"] {
            padding: 12px 20px;
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
        
        .btn-primario, .btn-primary {
            background-color: var(--color-primario);
            color: white;
        }
        
        .btn-primario:hover, .btn-primary:hover {
            background-color: var(--color-secundario);
            transform: translateY(-2px);
            box-shadow: var(--sombra-hover);
        }
        
        .btn-secundario, .btn-secondary {
            background-color: #f1f1f1;
            color: var(--color-texto-oscuro);
        }
        
        .btn-secundario:hover, .btn-secondary:hover {
            background-color: #e1e1e1;
        }
        
        .btn-exportar {
            background-color: #009688;
            color: white;
        }
        
        .btn-exportar:hover {
            background-color: #00796b;
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
        
        /* Campos específicos del formulario */
        .form-section {
            background-color: white;
            padding: 20px;
            border-radius: var(--borde-radio);
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 10px;
        }
        
        .required {
            color: var(--color-peligro);
            margin-left: 5px;
        }
        
        textarea {
            resize: vertical;
            min-height: 60px;
        }
        
        /* Responsivo */
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
            
            .form-grid, .form-row {
                grid-template-columns: 1fr;
            }
            
            .tabla-registros {
                display: block;
                overflow-x: auto;
            }
            
            .botones-form, .form-actions {
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
    
    <div class="contenedor">
        <div class="encabezado-pagina">
            <div class="titulo-pagina">
                <i class="fas fa-users icono-seccion"></i>
                <h1>Modificar Personal</h1>
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
                        <option value="agronomo">Agrónomo</option>
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
                    <!-- Estas filas serían generadas dinámicamente desde la base de datos -->
                    <tr onclick="seleccionarRegistro('P001')" id="fila-P001">
                        <td>P001</td>
                        <td>María López García</td>
                        <td><span class="estado estado-agronomo">Agrónomo</span></td>
                        <td>01/02/2023</td>
                        <td>987654321</td>
                        <td>
                            <button class="btn-accion btn-editar" onclick="seleccionarRegistro('P001')"><i class="fas fa-pen"></i></button>
                            <button class="btn-accion btn-eliminar"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    <tr onclick="seleccionarRegistro('P002')" id="fila-P002">
                        <td>P002</td>
                        <td>Juan Martínez Ruiz</td>
                        <td><span class="estado estado-supervisor">Supervisor</span></td>
                        <td>15/05/2022</td>
                        <td>912345678</td>
                        <td>
                            <button class="btn-accion btn-editar" onclick="seleccionarRegistro('P002')"><i class="fas fa-pen"></i></button>
                            <button class="btn-accion btn-eliminar"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    <tr onclick="seleccionarRegistro('P003')" id="fila-P003">
                        <td>P003</td>
                        <td>Ana Sánchez Vidal</td>
                        <td><span class="estado estado-agronomo">Agrónomo</span></td>
                        <td>10/06/2021</td>
                        <td>923456789</td>
                        <td>
                            <button class="btn-accion btn-editar" onclick="seleccionarRegistro('P003')"><i class="fas fa-pen"></i></button>
                            <button class="btn-accion btn-eliminar"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    <tr onclick="seleccionarRegistro('P004')" id="fila-P004">
                        <td>P004</td>
                        <td>Pedro González Torres</td>
                        <td><span class="estado estado-supervisor">Supervisor</span></td>
                        <td>03/12/2022</td>
                        <td>934567890</td>
                        <td>
                            <button class="btn-accion btn-editar" onclick="seleccionarRegistro('P004')"><i class="fas fa-pen"></i></button>
                            <button class="btn-accion btn-eliminar"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
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
        
        <!-- Formulario de Edición -->
        <div class="formulario-edicion" id="formularioEdicion" style="display: none;"> 
            <div class="titulo-formulario">
                <h3>Modificar Datos del Personal</h3>
                <span class="id-registro" id="idRegistroMostrado">P001</span>
            </div>
            
            <div class="detalles-item">
                <i class="fas fa-info-circle"></i>
                <strong>Nota:</strong> Selecciona un registro de la tabla para modificar sus datos. El ID del personal no puede ser modificado.
            </div>
            
            <form id="modificarPersonalForm" method="POST" action="modificarPersonal.php">
                <input type="hidden" id="idPersonal" name="idPersonal" value="">
                <input type="hidden" name="modificarPersonal" value="modificarPersonal">
                
                <!-- Datos Personales -->
                <div class="form-section">
                    <h3 class="section-title">Datos Personales</h3>
                    <div class="form-group">
                        <label class="form-label">Nombre Completo<span class="required">*</span></label>
                        <div class="campo-contenedor">
                            <i class="fas fa-user campo-icono"></i>
                            <input type="text" id="modificarNombreCompleto" name="modificarNombreCompleto" class="form-input" required placeholder="Ingrese el nombre completo">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">ID<span class="required">*</span></label>
                        <div class="campo-contenedor">
                            <i class="fas fa-id-card campo-icono"></i>
                            <input type="text" id="modificarDni" name="modificarDni" class="form-input" required pattern="[0-9]{8}" placeholder="Ingrese el ID" maxlength="8" disabled>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">CURP</label>
                        <div class="campo-contenedor">
                            <i class="fas fa-id-card campo-icono"></i>
                            <input type="text" id="modificarCurp" name="modificarCurp" class="form-input" placeholder="Ingrese la CURP">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Fecha de Nacimiento<span class="required">*</span></label>
                            <div class="campo-contenedor">
                                <i class="fas fa-calendar-alt campo-icono"></i>
                                <input type="date" id="modificarFechaNacimiento" name="modificarFechaNacimiento" class="form-input" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Género</label>
                            <div class="campo-contenedor">
                                <i class="fas fa-venus-mars campo-icono"></i>
                                <select id="modificarGenero" name="modificarGenero" class="form-select">
                                    <option value="">Seleccione...</option>
                                    <option value="Masculino">Masculino</option>
                                    <option value="Femenino">Femenino</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Información Laboral -->
                <div class="form-section">
                    <h3 class="section-title">Información Laboral</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Puesto<span class="required">*</span></label>
                            <div class="campo-contenedor">
                                <i class="fas fa-briefcase campo-icono"></i>
                                <select id="modificarPuesto" name="modificarPuesto" class="form-select" required>
                                    <option value="">Seleccione un puesto...</option>
                                    <option value="Agronomo">Agrónomo</option>
                                    <option value="Supervisor">Supervisor</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Fecha de Ingreso<span class="required">*</span></label>
                        <div class="campo-contenedor">
                            <i class="fas fa-calendar-check campo-icono"></i>
                            <input type="date" id="modificarFechaIngreso" name="modificarFechaIngreso" class="form-input" required>
                        </div>
                    </div>
                </div>
                
                <!-- Datos de Contacto -->
                <div class="form-section">
                    <h3 class="section-title">Datos de Contacto</h3>
                    <div class="form-group">
                        <label class="form-label">Correo Electrónico</label>
                        <div class="campo-contenedor">
                            <i class="fas fa-envelope campo-icono"></i>
                            <input type="email" id="modificarEmail" name="modificarEmail" class="form-input" placeholder="ejemplo@correo.com">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Teléfono Celular<span class="required">*</span></label>
                            <div class="campo-contenedor">
                                <i class="fas fa-mobile-alt campo-icono"></i>
                                <input type="tel" id="modificarTelefono" name="modificarTelefono" class="form-input" required placeholder="Número de celular"  maxlength="10">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Teléfono de Emergencia</label>
                            <div class="campo-contenedor">
                                <i class="fas fa-phone campo-icono"></i>
                                <input type="tel" id="modificarTelefonoEmergencia" name="modificarTelefonoEmergencia" class="form-input" placeholder="Número de emergencia"  maxlength="10">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Dirección</label>
                        <div class="campo-contenedor">
                            <i class="fas fa-map-marker-alt campo-icono"></i>
                            <textarea id="modificarDireccion" name="modificarDireccion" class="form-input" rows="2" placeholder="Ingrese la dirección completa"></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Botones de acción -->
                <div class="botones-form">
                    <button type="button" class="btn btn-secundario" onclick="cancelarEdicion()"><i class="fas fa-times"></i> Cancelar</button>
                    <button type="submit" class="btn btn-primario"><i class="fas fa-save"></i> Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Datos de ejemplo para simular personal
        const personalEjemplo = <?php echo $personasJSON; ?>;
        
        // Función para seleccionar un registro de la tabla
        function seleccionarRegistro(id) {
            // Remover clase activa de todas las filas
            document.querySelectorAll('.tabla-registros tbody tr').forEach(fila => {
                fila.classList.remove('fila-activa');
            });
            
            // Agregar clase activa a la fila seleccionada
            document.getElementById('fila-' + id).classList.add('fila-activa');
            
            // Mostrar el formulario de edición
            const formulario = document.getElementById('formularioEdicion');
            formulario.style.display = 'block';
            
            // Hacer scroll suave hasta el formulario
            formulario.scrollIntoView({ behavior: 'smooth', block: 'start' });
            
            // Actualizar el ID mostrado en el formulario
            document.getElementById('idRegistroMostrado').textContent = id;
            document.getElementById('idPersonal').value = id;
            
            // Cargar los datos del registro seleccionado
            cargarDatosPersonal(id);
        }
        
        // Función para cargar datos del personal
        function cargarDatosPersonal(id) {
            // Buscar el personal en los datos de ejemplo
            const personal = personalEjemplo.find(p => p.id === id);
            
            if (personal) {
                // Llenar el formulario con los datos
                document.getElementById('modificarNombreCompleto').value = personal.nombre;
                document.getElementById('modificarDni').value = personal.dni;
                document.getElementById('modificarCurp').value = personal.curp;
                console.log(personal.curp);
                document.getElementById('modificarFechaNacimiento').value = personal.fechaNacimiento;
                document.getElementById('modificarGenero').value = personal.genero;
                console.log(personal.puesto);
                document.getElementById('modificarPuesto').value = personal.puesto;
                document.getElementById('modificarFechaIngreso').value = personal.fechaIngreso;
                document.getElementById('modificarEmail').value = personal.email;
                document.getElementById('modificarTelefono').value = personal.telefono;
                document.getElementById('modificarTelefonoEmergencia').value = personal.telefonoEmergencia;
                document.getElementById('modificarDireccion').value = personal.direccion;
            }
        }
        
        // Función para cancelar la edición
        function cancelarEdicion() {
            document.getElementById('formularioEdicion').style.display = 'none';
            
            // Remover clase activa de todas las filas
            document.querySelectorAll('.tabla-registros tbody tr').forEach(fila => {
                fila.classList.remove('fila-activa');
            });
        }
        
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
        
        // Función para eliminar personal (modal de confirmación)
        document.querySelectorAll('.btn-eliminar').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation(); // Evitar que se active la selección de fila
                const id = this.closest('tr').id.split('-')[1];
                if (confirm(`¿Está seguro que desea eliminar al personal ${id}?`)) {
                    // En un caso real, aquí enviarías una solicitud al servidor
                    alert(`Personal ${id} eliminado correctamente`);
                    // Eliminar la fila de la tabla
                    document.getElementById(`fila-${id}`).remove();
                }
            });
        });
    </script>
</body>
</html>