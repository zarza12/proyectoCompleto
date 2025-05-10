<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once '../../controllers/daoEntregas.php';
include_once  '../../models/Entregas.php';

if (isset($_POST['modificarEntregaBtn']) && $_POST['modificarEntregaBtn'] === 'modificarEntregaBtn') {

    $idRegistro = $_POST['idRegistro'];
    $calidadProducto = $_POST['calidadProductoModificar'];
    $cantidadProductos = $_POST['cantidadProductosModificar'];
    $fechaEntrega = null;
    $nombreEmpresa = $_POST['nombreEmpresaModificar'];
    $nombreTransportista = $_POST['nombreTransportistaModificar'];
    $emailComprador = $_POST['emailCompradorModificar']??null;
    
    $entrega = new Entregas(
        $idRegistro, // idEntregas (null porque es autoincrementable)
        $fechaEntrega,
        $calidadProducto,
        $cantidadProductos,
        $nombreEmpresa,
        $emailComprador,
        $nombreTransportista
    );
    
    $daoEntregas = new daoEntregas();
    $registo = $daoEntregas->updateEntrega($entrega);
    

    if ($registo) {
        echo "
        <script>
            alert('Modificacion exitosa');
            window.location.href = 'modificarEntregas.php';
        </script>";
    } else {
        mostrarMensaje("Error al Modificar.");
       
    }
   

}

$daoEntregas = new daoEntregas();
$listar = $daoEntregas->listarEntregas();
$listarJSON = json_encode($listar);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Entregas</title>
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
        
        /* Ajuste cuando el menú está abierto */
        #menuPrincipal:hover ~ .contenedor {
            width: calc(100% - var(--menu-width-open));
            margin-left: var(--menu-width-open);
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
        
        /* Mensaje de no resultados */
        .mensaje-no-resultados {
            padding: 20px;
            text-align: center;
            display: none;
            color: var(--color-texto-secundario);
            font-style: italic;
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
            pointer-events: none; /* Asegura que no interfiera con clics */
        }

        .form-input,
        .form-select {
            width: 100%;
            padding: 12px 15px 12px 35px; /* Padding izquierdo para acomodar el ícono */
            border: 1px solid var(--color-borde);
            border-radius: var(--borde-radio);
            font-size: 14px;
            outline: none;
            transition: var(--transicion);
        }
        
        .form-input:focus,
        .form-select:focus {
            border-color: var(--color-acento);
            box-shadow: 0 0 0 3px rgba(165, 105, 189, 0.2);
        }
        
        .form-input:disabled {
            background-color: #f5f5f5;
            cursor: not-allowed;
        }
        
        .form-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%234A235A' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 16px;
            padding-right: 35px;
        }
        
        .email-section {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px dashed var(--color-borde);
        }
        
        /* Botones */
        .botones-form {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--color-borde);
        }
        
        .btn {
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
        
        .btn-secundario:hover {
            background-color: #e1e1e1;
        }
        
        .btn-exportar {
            background-color: #009688;
            color: white;
        }
        
        .btn-exportar:hover {
            background-color: #00796b;
        }
        
        /* Estados de calidad */
        .estado {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-align: center;
        }
        
        .estado-exportacion {
            background-color: rgba(46, 204, 113, 0.2);
            color: #27ae60;
        }
        
        .estado-nacional {
            background-color: rgba(52, 152, 219, 0.2);
            color: #2980b9;
        }
        
        .estado-desecho {
            background-color: rgba(231, 76, 60, 0.2);
            color: #c0392b;
        }
        
        /* Responsivo */
        @media (max-width: 768px) {
            .contenedor {
                width: 100%;
                margin-left: 0;
                padding: 20px;
                padding-top: 60px; /* Espacio para el botón de toggle */
            }
            
            #menuPrincipal:hover ~ .contenedor {
                width: 100%;
                margin-left: 0;
            }
            
            #menuPrincipal.active ~ .contenedor {
                width: 100%;
                margin-left: 0;
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
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .tabla-registros {
                display: block;
                overflow-x: auto;
            }
            
            .botones-form {
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
    
    <!-- Información del usuario activo -->
    <div class="info-usuario">
        <div class="avatar-usuario"><?php echo $avatar; ?></div>
        <span><?php echo $nombre; ?></span>
    </div>

    <div class="contenedor">
        <div class="encabezado-pagina">
            <div class="titulo-pagina">
                <i class="fas fa-truck icono-seccion"></i>
                <h1>Modificar Entregas</h1>
            </div>
            <div class="botones-accion">
                <button class="btn btn-exportar"><i class="fas fa-file-export"></i> Exportar Datos</button>
            </div>
        </div>
        
        <div class="filtro-busqueda">
            <div class="busqueda">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Buscar entregas..." id="buscarRegistro">
            </div>
            <div class="filtros">
                <div class="filtro">
                    <select id="filtroCalidad">
                        <option value="">Todas las calidades</option>
                        <option value="exportacion">Exportación</option>
                        <option value="nacional">Consumo Nacional</option>
                        <option value="desecho">Desecho</option>
                    </select>
                </div>
                <div class="filtro">
                    <select id="filtroFecha">
                        <option value="">Todas las fechas</option>
                        <option value="hoy">Hoy</option>
                        <option value="semana">Esta semana</option>
                        <option value="mes">Este mes</option>
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
                        <th>FECHA</th>
                        <th>CALIDAD</th>
                        <th>CANTIDAD</th>
                        <th>EMPRESA</th>
                        <th>TRANSPORTISTA</th>
                        <th>ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Aquí se cargarán dinámicamente las filas desde JavaScript -->
                </tbody>
            </table>
            <!-- Mensaje cuando no hay resultados -->
            <div id="mensajeNoResultados" class="mensaje-no-resultados">
                <i class="fas fa-search-minus"></i> No se encontraron entregas que coincidan con los filtros seleccionados.
            </div>
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
                <h3>Editar Registro de Entrega</h3>
                <span class="id-registro" id="idRegistroMostrado">ENT-001</span>
            </div>
            
            <div class="detalles-item">
                <i class="fas fa-info-circle"></i>
                <strong>Nota:</strong> Selecciona un registro de la tabla para modificar sus datos. La fecha de entrega se actualiza automáticamente.
            </div>
            
<!-- Formulario con atributos name añadidos -->
<form id="formularioModificar" method="POST" action="modificarEntregas.php">
    <input type="hidden" id="idRegistro" name="idRegistro" value="">
    
    <div class="form-grid">
        <!-- Calidad del Producto -->
        <div class="form-group">
            <label class="form-label">Calidad del Producto<span style="color: #E74C3C; margin-left: 5px;">*</span></label>
            <div class="campo-contenedor">
                <i class="fas fa-tag campo-icono"></i>
                <select id="calidadProductoModificar" name="calidadProductoModificar" class="form-select" required>
                    <option value="">Seleccione la calidad</option>
                    <option value="exportacion">Exportación</option>
                    <option value="nacional">Consumo Nacional</option>
                    <option value="desecho">Desecho</option>
                </select>
            </div>
        </div>
        
        <!-- Cantidad de Productos -->
        <div class="form-group">
            <label class="form-label">Cantidad de Productos<span style="color: #E74C3C; margin-left: 5px;">*</span></label>
            <div class="campo-contenedor">
                <i class="fas fa-boxes campo-icono"></i>
                <input type="number" id="cantidadProductosModificar" name="cantidadProductosModificar" class="form-input" min="1" required>
            </div>
        </div>
        
        <!-- Fecha de Entrega -->
        <div class="form-group">
            <label class="form-label">Fecha de Entrega</label>
            <div class="campo-contenedor">
                <i class="fas fa-calendar-alt campo-icono"></i>
                <input type="date" id="fechaModificarEntrega" name="fechaModificarEntrega" class="form-input" disabled>
            </div>
        </div>
    </div>
    
    <div class="form-grid">
        <!-- Empresa Receptora -->
        <div class="form-group">
            <label class="form-label">Empresa Receptora<span style="color: #E74C3C; margin-left: 5px;">*</span></label>
            <div class="campo-contenedor">
                <i class="fas fa-building campo-icono"></i>
                <input type="text" id="nombreEmpresaModificar" name="nombreEmpresaModificar" class="form-input" required>
            </div>
        </div>
        
        <!-- Nombre del Transportista -->
        <div class="form-group">
            <label class="form-label">Nombre del Transportista<span style="color: #E74C3C; margin-left: 5px;">*</span></label>
            <div class="campo-contenedor">
                <i class="fas fa-truck campo-icono"></i>
                <input type="text" id="nombreTransportistaModificar" name="nombreTransportistaModificar" class="form-input" required>
            </div>
        </div>
    </div>
    
    <!-- Opción para añadir email -->
    <div class="form-group">
        <label class="form-label">¿Desea registrar un correo electrónico para recibir reportes?</label>
        <div class="campo-contenedor">
            <i class="fas fa-envelope campo-icono"></i>
            <select id="opcionEmailModificar" name="opcionEmailModificar" class="form-select" onchange="mostrarEmailModificar()">
                <option value="no">No</option>
                <option value="si">Sí</option>
            </select>
        </div>
    </div>
    <!-- Email del Comprador (Opcional) -->
    <div class="form-group" id="campoEmailModificar" style="display: none;">
        <label class="form-label">Email del Comprador (Opcional)</label>
        <div class="campo-contenedor">
            <i class="fas fa-at campo-icono"></i>
            <input type="email" id="emailCompradorModificar" name="emailCompradorModificar" class="form-input" placeholder="ejemplo@correo.com">
        </div>
    </div>
    
    <div class="botones-form">
        <button type="button" class="btn btn-secundario" onclick="cancelarEdicion()"><i class="fas fa-times"></i> Cancelar</button>
        <button type="submit" id="modificarEntregaBtn" name="modificarEntregaBtn" value="modificarEntregaBtn" class="btn btn-primario" ><i class="fas fa-save"></i> Guardar Cambios</button>
    </div>
</form>
        </div>
    </div>
    
    <script>
        // Datos de ejemplo para simular entregas
        const entregasEjemplo = <?php echo $listarJSON; ?>

        // Función para cargar la tabla con los datos del array
        function cargarTablaEntregas() {
            const tbody = document.querySelector('.tabla-registros tbody');
            
            // Limpiar el contenido actual de la tabla
            tbody.innerHTML = '';
            
            // Mapeo de calidades a texto y clases
            const calidadTexto = {
                'exportacion': 'Exportación',
                'nacional': 'Nacional',
                'desecho': 'Desecho'
            };
            
            const calidadClase = {
                'exportacion': 'estado-exportacion',
                'nacional': 'estado-nacional',
                'desecho': 'estado-desecho'
            };
            
            // Generar filas para cada entrega en el array
            entregasEjemplo.forEach(entrega => {
                // Formato fecha de YYYY-MM-DD a DD/MM/YYYY
                const fechaPartes = entrega.fecha.split('-');
                const fechaFormateada = `${fechaPartes[2]}/${fechaPartes[1]}/${fechaPartes[0]}`;
                
                // Crear fila
                const fila = document.createElement('tr');
                fila.id = `fila-${entrega.id}`;
                fila.onclick = () => seleccionarRegistro(entrega.id);
                
                // Contenido de la fila
                fila.innerHTML = `
                    <td>${entrega.id}</td>
                    <td>${fechaFormateada}</td>
                    <td><span class="estado ${calidadClase[entrega.calidad]}">${calidadTexto[entrega.calidad]}</span></td>
                    <td>${entrega.cantidad}</td>
                    <td>${entrega.empresa}</td>
                    <td>${entrega.transportista}</td>
                    <td>
                        <button class="btn-accion btn-editar" onclick="seleccionarRegistro('${entrega.id}')"><i class="fas fa-pen"></i></button>
                        <button class="btn-accion btn-eliminar" onclick="confirmarEliminacion('${entrega.id}', event)"><i class="fas fa-trash"></i></button>
                    </td>
                `;
                
                // Agregar fila a la tabla
                tbody.appendChild(fila);
            });
            
            // Mostrar mensaje si no hay datos
            const mensajeNoResultados = document.getElementById('mensajeNoResultados');
            if (entregasEjemplo.length === 0) {
                mensajeNoResultados.style.display = 'block';
            } else {
                mensajeNoResultados.style.display = 'none';
            }
        }

        // Función para confirmar eliminación de un registro
        function confirmarEliminacion(id, event) {
            // Evitar que se active la selección de fila
            event.stopPropagation();
            
            if (confirm(`¿Está seguro que desea eliminar la entrega ${id}?`)) {
                // Buscar índice del elemento en el array
                const index = entregasEjemplo.findIndex(entrega => entrega.id === id);
                
                if (index !== -1) {
                    // Eliminar del array
                    entregasEjemplo.splice(index, 1);
                    
                    // Actualizar la tabla
                    cargarTablaEntregas();
                    
                    // Si se estaba editando este registro, ocultar el formulario
                    if (document.getElementById('idRegistro').value === id) {
                        cancelarEdicion();
                    }
                    
                    alert(`Entrega ${id} eliminada correctamente`);
                }
            }
        }

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
            document.getElementById('idRegistro').value = id;
            
            // Cargar los datos del registro seleccionado
            cargarDatosEntrega(id);
        }

        // Función para cargar datos de la entrega
        function cargarDatosEntrega(id) {
            // Buscar la entrega en los datos de ejemplo
            const entrega = entregasEjemplo.find(e => e.id === id);
            
            if (entrega) {
                // Llenar el formulario con los datos
                document.getElementById('calidadProductoModificar').value = entrega.calidad;
                document.getElementById('cantidadProductosModificar').value = entrega.cantidad;
                document.getElementById('fechaModificarEntrega').value = entrega.fecha;
                document.getElementById('nombreEmpresaModificar').value = entrega.empresa;
                document.getElementById('nombreTransportistaModificar').value = entrega.transportista;
                
                // Configurar el email si existe
                if (entrega.email) {
                    document.getElementById('opcionEmailModificar').value = 'si';
                    document.getElementById('campoEmailModificar').style.display = 'block';
                    document.getElementById('emailCompradorModificar').value = entrega.email;
                } else {
                    document.getElementById('opcionEmailModificar').value = 'no';
                    document.getElementById('campoEmailModificar').style.display = 'none';
                }
            }
        }

        // Función para mostrar u ocultar el campo de email
        function mostrarEmailModificar() {
            const opcionEmail = document.getElementById('opcionEmailModificar').value;
            const campoEmail = document.getElementById('campoEmailModificar');
            
            if (opcionEmail === 'si') {
                campoEmail.style.display = 'block';
            } else {
                campoEmail.style.display = 'none';
                document.getElementById('emailCompradorModificar').value = '';
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

        // Función para filtrar la tabla según los filtros seleccionados
        function filtrarTabla() {
            const filtroCalidad = document.getElementById('filtroCalidad').value.toLowerCase();
            const filtroFecha = document.getElementById('filtroFecha').value;
            const filas = document.querySelectorAll('.tabla-registros tbody tr');
            
            console.log('Filtro aplicado - Calidad:', filtroCalidad, 'Fecha:', filtroFecha);
            
            // Obtener la fecha actual para comparaciones
            const fechaActual = new Date();
            const diaActual = fechaActual.getDate().toString().padStart(2, '0');
            const mesActual = (fechaActual.getMonth() + 1).toString().padStart(2, '0');
            const anioActual = fechaActual.getFullYear();
            
            // Formato DD/MM/YYYY
            const hoy = `${diaActual}/${mesActual}/${anioActual}`;
            console.log('Fecha actual (hoy):', hoy);
            
            let filasVisibles = 0;
            
            filas.forEach(fila => {
                let mostrar = true;
                
                // Filtrar por calidad
                if (filtroCalidad) {
                    // Obtener el texto dentro del span de calidad
                    const calidadSpan = fila.cells[2].querySelector('span');
                    const calidadCelda = calidadSpan ? calidadSpan.textContent.toLowerCase() : '';
                    console.log('Fila ID:', fila.cells[0].textContent, 'Calidad en celda:', calidadCelda);
                    
                    // Mapeo específico de valores del filtro a textos en las celdas
                    if (filtroCalidad === 'exportacion' && !calidadCelda.includes('exportación')) {
                        mostrar = false;
                    } else if (filtroCalidad === 'nacional' && !calidadCelda.includes('nacional')) {
                        mostrar = false;
                    } else if (filtroCalidad === 'desecho' && !calidadCelda.includes('desecho')) {
                        mostrar = false;
                    }
                }
                
                // Filtrar por fecha solo si la fila aún es visible por el filtro de calidad
                if (filtroFecha && mostrar) {
                    const fechaFila = fila.cells[1].textContent; // Formato DD/MM/YYYY
                    console.log('Fecha en fila:', fechaFila);
                    
                    // Convertir fecha de la fila a objeto Date para comparaciones
                    const [diaFila, mesFila, anioFila] = fechaFila.split('/');
                    const fechaFilaObj = new Date(anioFila, parseInt(mesFila) - 1, parseInt(diaFila));
                    
                    if (filtroFecha === 'hoy') {
                        // Comprobar si la fecha es hoy
                        if (fechaFila !== hoy) {
                            mostrar = false;
                        }
                    } else if (filtroFecha === 'semana') {
                        // Calcular inicio de semana (lunes de la semana actual)
                        const inicioSemana = new Date(fechaActual);
                        const diaSemana = fechaActual.getDay(); // 0 es domingo, 1 es lunes, ...
                        const diferenciaDias = diaSemana === 0 ? 6 : diaSemana - 1; // Ajustar para que el inicio sea lunes
                        inicioSemana.setDate(fechaActual.getDate() - diferenciaDias);
                        inicioSemana.setHours(0, 0, 0, 0); // Establecer a inicio del día
                        
                        // Comprobar si la fecha está dentro de la semana actual
                        if (fechaFilaObj < inicioSemana || fechaFilaObj > fechaActual) {
                            mostrar = false;
                        }
                    } else if (filtroFecha === 'mes') {
                        // Comprobar si la fecha está dentro del mes actual
                        if (parseInt(mesFila) !== (fechaActual.getMonth() + 1) || parseInt(anioFila) !== fechaActual.getFullYear()) {
                            mostrar = false;
                        }
                    }
                }
                
                // Aplicar visibilidad
                fila.style.display = mostrar ? '' : 'none';
                
                if (mostrar) {
                    filasVisibles++;
                }
            });
            
            console.log('Total de filas visibles después de filtrar:', filasVisibles);
            
            // Mostrar mensaje si no hay resultados
            const mensajeNoResultados = document.getElementById('mensajeNoResultados');
            if (mensajeNoResultados) {
                if (filasVisibles === 0) {
                    mensajeNoResultados.style.display = 'block';
                } else {
                    mensajeNoResultados.style.display = 'none';
                }
            }
        }

        // Inicializar búsqueda
        function inicializarBusqueda() {
            document.getElementById('buscarRegistro').addEventListener('input', function(e) {
                const terminoBusqueda = e.target.value.toLowerCase();
                const filas = document.querySelectorAll('.tabla-registros tbody tr');
                
                let filasVisibles = 0;
                
                filas.forEach(fila => {
                    const texto = fila.textContent.toLowerCase();
                    if (texto.includes(terminoBusqueda)) {
                        fila.style.display = '';
                        filasVisibles++;
                    } else {
                        fila.style.display = 'none';
                    }
                });
                
                // Mostrar mensaje si no hay resultados
                const mensajeNoResultados = document.getElementById('mensajeNoResultados');
                if (mensajeNoResultados) {
                    mensajeNoResultados.style.display = filasVisibles === 0 ? 'block' : 'none';
                }
            });
        }

        // Inicializar filtros
        function inicializarFiltros() {
            document.getElementById('filtroCalidad').addEventListener('change', filtrarTabla);
            document.getElementById('filtroFecha').addEventListener('change', filtrarTabla);
        }

        // Inicializar todo cuando la página cargue
        document.addEventListener('DOMContentLoaded', function() {
            // Cargar la tabla con los datos
            cargarTablaEntregas();
            
            // Inicializar la búsqueda
            inicializarBusqueda();
            
            // Inicializar los filtros
            inicializarFiltros();
        });
    </script>
</body>
</html>