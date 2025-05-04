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

        
        
        $html .= '<tr onclick="seleccionarRegistro(\'' . $persona->getId() . '\')" id="fila-' . $persona->getId() . '">';
        $html .= '<td>' . $persona->getId() . '</td>';
        $html .= '<td>' . $persona->getNombreCompleto() . '</td>'; 
        $html .= '<td><span class="estado estado-' . $claseEstado . '">' . $persona->getPuesto() . '</span></td>';
        $html .= '<td>' . $persona->getFechaIngreso() . '</td>';
        $html .= '<td>' . $persona->getTelefonoCelular() . '</td>'; 
        $html .= '<td>
                    <button class="btn-accion btn-ver" onclick="seleccionarRegistro(\'' . $persona->getId() . '\')"><i class="fas fa-eye"></i></button>
                    <button class="btn-accion btn-eliminar"><i class="fas fa-trash"></i></button>
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
    <title>Listar Personal</title>
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
    
    
    <div class="contenedor">
        <div class="encabezado-pagina">
            <div class="titulo-pagina">
                <i class="fas fa-users icono-seccion"></i>
                <h1>Listar Personal</h1>
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
        
        <!-- Tarjeta de Detalles del Personal -->
        <div class="tarjeta-detalles" id="tarjetaDetalles">
            <div class="tarjeta-cabecera">
                <button class="btn-cerrar" onclick="cerrarDetalles()">
                    <i class="fas fa-times"></i>
                </button>
                <div class="id-badge" id="idPersonaDetalle">P001</div>
                <div class="avatar" id="avatarInicial">
                    <i class="fas fa-user"></i>
                </div>
                <h2 id="nombrePersonaDetalle">María López García</h2>
                <span class="puesto" id="puestoPersonaDetalle">Agrónomo</span>
            </div>
            
            <div class="secciones-tabs">
                <div class="tab activo" onclick="cambiarTab(this, 'informacionPersonal')">
                    <i class="fas fa-user-circle"></i> Información Personal
                </div>
                <div class="tab" onclick="cambiarTab(this, 'informacionLaboral')">
                    <i class="fas fa-briefcase"></i> Información Laboral
                </div>
                <div class="tab" onclick="cambiarTab(this, 'contactoEmergencia')">
                    <i class="fas fa-phone-alt"></i> Contacto
                </div>
            </div>
            
            <!-- Sección de Información Personal -->
            <div class="seccion-detalle activa" id="informacionPersonal">
                <div class="info-grupo">
                    <h3 class="titulo-seccion">
                        <i class="fas fa-address-card"></i>
                        Datos Personales
                    </h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="etiqueta">ID / DNI</span>
                            <span class="valor" id="dniDetalle">P001</span>
                        </div>
                        <div class="info-item">
                            <span class="etiqueta">Nombre Completo</span>
                            <span class="valor" id="nombreCompletoDetalle">María López García</span>
                        </div>
                        <div class="info-item">
                            <span class="etiqueta">Fecha de Nacimiento</span>
                            <span class="valor" id="fechaNacimientoDetalle">15/04/1990</span>
                        </div>
                        <div class="info-item">
                            <span class="etiqueta">Género</span>
                            <span class="valor" id="generoDetalle">Femenino</span>
                        </div>
                        <div class="info-item direccion-completa">
                            <span class="etiqueta">Dirección</span>
                            <span class="valor" id="direccionDetalle">Av. Principal 123, Colonia Centro, CP 12345, Ciudad</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sección de Información Laboral -->
            <div class="seccion-detalle" id="informacionLaboral">
                <div class="info-grupo">
                    <h3 class="titulo-seccion">
                        <i class="fas fa-briefcase"></i>
                        Datos Laborales
                    </h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="etiqueta">Puesto Actual</span>
                            <span class="valor" id="puestoActualDetalle">Agrónomo</span>
                        </div>
                        <div class="info-item">
                            <span class="etiqueta">Fecha de Ingreso</span>
                            <span class="valor" id="fechaIngresoDetalle">01/02/2023</span>
                        </div>
                        <div class="info-item">
                            <span class="etiqueta">Antigüedad</span>
                            <span class="valor" id="antiguedadDetalle">1 año, 2 meses</span>
                        </div>
                    </div>
                </div>
                
                <div class="info-grupo">
                    <h3 class="titulo-seccion">
                        <i class="fas fa-chart-line"></i>
                        Estadísticas
                    </h3>
                    <div class="info-grid">
                        <div class="estadistica-item">
                            <div class="estadistica-icono">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="estadistica-valor" id="diasAntiguedadDetalle">423</div>
                            <div class="estadistica-etiqueta">Días en la empresa</div>
                        </div>
                        <div class="estadistica-item">
                            <div class="estadistica-icono">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <div class="estadistica-valor">12</div>
                            <div class="estadistica-etiqueta">Proyectos asignados</div>
                        </div>
                        <div class="estadistica-item">
                            <div class="estadistica-icono">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="estadistica-valor">98%</div>
                            <div class="estadistica-etiqueta">Rendimiento</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sección de Contacto -->
            <div class="seccion-detalle" id="contactoEmergencia">
                <div class="info-grupo">
                    <h3 class="titulo-seccion">
                        <i class="fas fa-address-book"></i>
                        Información de Contacto
                    </h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="etiqueta">Correo Electrónico</span>
                            <span class="valor" id="emailDetalle">maria.lopez@ejemplo.com</span>
                        </div>
                        <div class="info-item">
                            <span class="etiqueta">Teléfono Celular</span>
                            <span class="valor" id="telefonoDetalle">987654321</span>
                        </div>
                        <div class="info-item">
                            <span class="etiqueta">Teléfono de Emergencia</span>
                            <span class="valor" id="telefonoEmergenciaDetalle">912345678</span>
                        </div>
                    </div>
                </div>
                
                <div class="info-grupo">
                    <h3 class="titulo-seccion">
                        <i class="fas fa-map-marked-alt"></i>
                        Ubicación
                    </h3>
                    <div class="info-grid">
                        <div class="info-item direccion-completa">
                            <span class="etiqueta">Dirección Completa</span>
                            <span class="valor" id="direccionCompletaDetalle">Av. Principal 123, Colonia Centro, CP 12345, Ciudad</span>
                        </div>
                    </div>
                    
                    <!-- Aquí se podría agregar un mapa en una implementación real -->
                    <div style="background-color: #f5f5f5; height: 200px; border-radius: 8px; margin-top: 20px; display: flex; align-items: center; justify-content: center; color: #888;">
                        <i class="fas fa-map-marker-alt" style="font-size: 20px; margin-right: 10px;"></i> 
                        Vista de mapa no disponible
                    </div>
                </div>
            </div>
            
            <div class="botones-footer">
                <button class="btn btn-secundario" onclick="cerrarDetalles()"><i class="fas fa-times"></i> Cerrar</button>
                <button class="btn btn-primario" onclick="window.location.href='modificarPersonal.php?id=' + document.getElementById('idPersonaDetalle').textContent"><i class="fas fa-edit"></i> Editar Información</button>
            </div>
        </div>
    </div>
    
    <script>
        // Datos de ejemplo para simular personal
        const personalEjemplo = <?php echo $personasJSON; ?>;
        
        // Función para seleccionar un registro de la tabla y mostrar detalles
        function seleccionarRegistro(id) {
            // Remover clase activa de todas las filas
            document.querySelectorAll('.tabla-registros tbody tr').forEach(fila => {
                fila.classList.remove('fila-activa');
            });
            
            // Agregar clase activa a la fila seleccionada
            document.getElementById('fila-' + id).classList.add('fila-activa');
            
            // Mostrar la tarjeta de detalles
            mostrarDetalles(id);
            
            // Hacer scroll suave hasta la tarjeta
            document.getElementById('tarjetaDetalles').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
        
        // Función para mostrar los detalles del personal
        function mostrarDetalles(id) {
            // Buscar el personal en los datos
            const personal = personalEjemplo.find(p => p.id === id);
            
            if (personal) {
                // Mostrar la tarjeta
                const tarjeta = document.getElementById('tarjetaDetalles');
                tarjeta.style.display = 'block';
                
                // Actualizar datos en la cabecera
                document.getElementById('idPersonaDetalle').textContent = personal.id;
                document.getElementById('nombrePersonaDetalle').textContent = personal.nombre;
                document.getElementById('puestoPersonaDetalle').textContent = personal.puesto;
                
                // Establecer la inicial del avatar
                const inicial = personal.nombre.charAt(0);
                document.getElementById('avatarInicial').innerHTML = inicial;
                
                // Actualizar datos en las diferentes secciones
                
                // Información Personal
                document.getElementById('dniDetalle').textContent = personal.dni;
                document.getElementById('nombreCompletoDetalle').textContent = personal.nombre;
                document.getElementById('fechaNacimientoDetalle').textContent = personal.fechaNacimiento;
                document.getElementById('generoDetalle').textContent = personal.genero;
                document.getElementById('direccionDetalle').textContent = personal.direccion || 'No disponible';
                
                // Información Laboral
                document.getElementById('puestoActualDetalle').textContent = personal.puesto;
                document.getElementById('fechaIngresoDetalle').textContent = personal.fechaIngreso;
                
                // Calcular antigüedad
                const fechaIngreso = new Date(personal.fechaIngreso);
                const hoy = new Date();
                const antiguedadMilisegundos = hoy - fechaIngreso;
                const diasAntiguedad = Math.floor(antiguedadMilisegundos / (1000 * 60 * 60 * 24));
                const años = Math.floor(diasAntiguedad / 365);
                const meses = Math.floor((diasAntiguedad % 365) / 30);
                
                document.getElementById('antiguedadDetalle').textContent = `${años} ${años === 1 ? 'año' : 'años'}, ${meses} ${meses === 1 ? 'mes' : 'meses'}`;
                document.getElementById('diasAntiguedadDetalle').textContent = diasAntiguedad;
                
                // Información de Contacto
                document.getElementById('emailDetalle').textContent = personal.email || 'No disponible';
                document.getElementById('telefonoDetalle').textContent = personal.telefono || 'No disponible';
                document.getElementById('telefonoEmergenciaDetalle').textContent = personal.telefonoEmergencia || 'No disponible';
                document.getElementById('direccionCompletaDetalle').textContent = personal.direccion || 'No disponible';
            }
        }
        
        // Función para cerrar los detalles
        function cerrarDetalles() {
            document.getElementById('tarjetaDetalles').style.display = 'none';
            
            // Remover clase activa de todas las filas
            document.querySelectorAll('.tabla-registros tbody tr').forEach(fila => {
                fila.classList.remove('fila-activa');
            });
        }
        
        // Función para cambiar entre tabs
        function cambiarTab(tabElement, seccionId) {
            // Quitar la clase 'activo' de todos los tabs
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('activo');
            });
            
            // Agregar la clase 'activo' al tab seleccionado
            tabElement.classList.add('activo');
            
            // Ocultar todas las secciones
            document.querySelectorAll('.seccion-detalle').forEach(seccion => {
                seccion.classList.remove('activa');
            });
            
            // Mostrar la sección seleccionada
            document.getElementById(seccionId).classList.add('activa');
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
                    
                    // Si estamos mostrando los detalles de este personal, cerrarlos
                    if (document.getElementById('idPersonaDetalle').textContent === id) {
                        cerrarDetalles();
                    }
                }
            });
        });
    </script>
</body>
</html>