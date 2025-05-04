<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once '../../controllers/daoEntregas.php';
include_once  '../../models/Entregas.php';

if (isset($_POST['eliminarEntregaBtn']) && $_POST['eliminarEntregaBtn'] === 'eliminarEntregaBtn') {

    $idRegistro = $_POST['idRegistro'];
   
    

    $daoEntregas = new daoEntregas();
    $registo = $daoEntregas->deleteEntrega($idRegistro);
    

    if ($registo) {
        echo "
        <script>
            alert('Eliminacion exitosa');
            window.location.href = 'eliminarEntregas.php';
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
    <title>Eliminar Entregas</title>
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
        
        .tabla-registros tr {
            cursor: pointer;
            transition: var(--transicion);
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
        
        /* Modal de eliminación */
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1050;
            display: none;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes zoomIn {
            from { transform: scale(0.8); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        
        .modal {
            background-color: white;
            border-radius: var(--borde-radio);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            width: 90%;
            overflow: hidden;
            animation: zoomIn 0.3s;
            position: relative;
        }
        
        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 20px;
            border-bottom: 1px solid var(--color-borde);
        }
        
        .modal-peligro .modal-header {
            background-color: var(--color-peligro);
            color: white;
        }
        
        .modal-titulo {
            font-size: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .modal-peligro .modal-titulo {
            color: white;
        }
        
        .modal-cerrar {
            background: none;
            border: none;
            color: inherit;
            font-size: 20px;
            cursor: pointer;
            padding: 5px;
            opacity: 0.7;
            transition: var(--transicion);
        }
        
        .modal-cerrar:hover {
            opacity: 1;
        }
        
        .modal-cuerpo {
            padding: 20px;
        }
        
        .modal-advertencia {
            background-color: rgba(231, 76, 60, 0.1);
            border-left: 4px solid var(--color-peligro);
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .modal-advertencia i {
            color: var(--color-peligro);
            font-size: 24px;
        }
        
        .modal-advertencia p {
            color: var(--color-texto-oscuro);
            font-size: 14px;
            margin: 0;
        }
        
        .modal-advertencia strong {
            color: var(--color-peligro);
        }
        
        .modal-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .modal-info-item {
            margin-bottom: 10px;
        }
        
        .modal-info-item label {
            display: block;
            font-weight: 600;
            font-size: 13px;
            color: var(--color-texto-secundario);
            margin-bottom: 5px;
        }
        
        .modal-info-item span {
            font-size: 14px;
            color: var(--color-texto-oscuro);
        }
        
        .modal-footer {
            padding: 15px 20px;
            border-top: 1px solid var(--color-borde);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        
        .modal-btn {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: var(--transicion);
        }
        
        .modal-btn-cancelar {
            background-color: #f1f1f1;
            color: var(--color-texto-oscuro);
        }
        
        .modal-btn-cancelar:hover {
            background-color: #e1e1e1;
        }
        
        .modal-btn-eliminar {
            background-color: var(--color-peligro);
            color: white;
        }
        
        .modal-btn-eliminar:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
            box-shadow: var(--sombra-hover);
        }
        
        .modal-confirmacion {
            max-width: 400px;
            text-align: center;
        }
        
        .modal-confirmacion .modal-cuerpo {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }
        
        .icono-confirmacion {
            font-size: 48px;
            color: var(--color-peligro);
            margin-bottom: 10px;
        }
        
        .modal-confirmacion p {
            font-size: 16px;
            margin-bottom: 10px;
        }
        
        .required {
            color: var(--color-peligro);
            margin-left: 4px;
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
            
            .tabla-registros {
                display: block;
                overflow-x: auto;
            }
            
            .modal-info-grid {
                grid-template-columns: 1fr;
            }
            
            .modal-footer {
                flex-direction: column;
            }
            
            .modal-btn {
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
    
    <!-- Información del usuario en la parte superior derecha -->
    <div class="info-usuario">
        <div class="avatar-usuario"><?php echo $avatar; ?></div>
        <span><?php echo $nombre; ?></span>
    </div>
    
    <div class="contenedor">
        <div class="encabezado-pagina">
            <div class="titulo-pagina">
                <i class="fas fa-trash-alt icono-seccion"></i>
                <h1>Eliminar Entregas</h1>
            </div>
            <div class="botones-accion">
                <button class="btn btn-secundario" onclick="volverListado()"><i class="fas fa-arrow-left"></i> Volver al Listado</button>
            </div>
        </div>
        
        <div class="filtro-busqueda">
            <div class="busqueda">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Buscar entregas..." id="buscarEntrega">
            </div>
            <div class="filtros">
                <div class="filtro">
                    <select id="filtroCalidad">
                        <option value="">Todas las calidades</option>
                        <option value="exportacion">Exportación</option>
                        <option value="nacional">Nacional</option>
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
                        <th>Fecha</th>
                        <th>Calidad</th>
                        <th>Cantidad</th>
                        <th>Empresa</th>
                        <th>Transportista</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- La tabla se cargará dinámicamente mediante JavaScript -->
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
    
    <!-- Modal de Eliminación (Primer paso) -->
    <div class="modal-backdrop" id="modalEliminacion">
        <div class="modal modal-peligro">
            <div class="modal-header">
                <div class="modal-titulo">
                    <i class="fas fa-trash-alt"></i>
                    <span>Eliminar Registro de Entrega</span>
                    <span class="id-registro" id="idRegistroModal"></span>
                </div>
                <button class="modal-cerrar" onclick="cerrarModal('modalEliminacion')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-cuerpo">
                <div class="modal-advertencia">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p><strong>Advertencia:</strong> Esta acción no se puede deshacer. Una vez eliminado el registro, no será posible recuperar la información.</p>
                </div>
                
                <!-- Información de la entrega -->
                <h3 style="margin-bottom: 15px; color: var(--color-primario);">Detalles de la Entrega</h3>
                <div class="modal-info-grid">
                    <div class="modal-info-item">
                        <label>Fecha de Entrega:</label>
                        <span id="fechaEntregaModal"></span>
                    </div>
                    <div class="modal-info-item">
                        <label>Calidad del Producto:</label>
                        <span id="calidadProductoModal"></span>
                    </div>
                    <div class="modal-info-item">
                        <label>Cantidad de Productos:</label>
                        <span id="cantidadProductosModal"></span>
                    </div>
                    <div class="modal-info-item">
                        <label>Empresa Receptora:</label>
                        <span id="nombreEmpresaModal"></span>
                    </div>
                    <div class="modal-info-item">
                        <label>Correo del Comprador:</label>
                        <span id="emailCompradorModal"></span>
                    </div>
                    <div class="modal-info-item">
                        <label>Transportista:</label>
                        <span id="nombreTransportistaModal"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="modal-btn modal-btn-cancelar" onclick="cerrarModal('modalEliminacion')">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="modal-btn modal-btn-eliminar" onclick="solicitarConfirmacion()">
                    <i class="fas fa-trash"></i> Eliminar Entrega
                </button>
            </div>
        </div>
    </div>
    
    <!-- Modal de Confirmación Final -->
    <div class="modal-backdrop" id="modalConfirmacion">
        <div class="modal modal-confirmacion">
            <div class="modal-header">
                <div class="modal-titulo">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Confirmar Eliminación</span>
                </div>
                <button class="modal-cerrar" onclick="cerrarModal('modalConfirmacion')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-cuerpo">
                <i class="fas fa-trash-alt icono-confirmacion"></i>
                <p>¿Está seguro de que desea eliminar la entrega <strong id="idConfirmacion"></strong>?</p>
                <p style="color: var(--color-peligro); font-weight: 600;">Esta acción no se puede deshacer.</p>
            </div>
            <form method="POST" action="eliminarEntregas.php">
            <input type="hidden" id="idRegistro" name="idRegistro" value="">
                <div class="modal-footer">
                <button type="button" class="modal-btn modal-btn-cancelar" onclick="cerrarModal('modalConfirmacion')">
                    <i class="fas fa-arrow-left"></i> Volver
                </button>
                <button type="submit" id="eliminarEntregaBtn" name="eliminarEntregaBtn" value="eliminarEntregaBtn" class="modal-btn modal-btn-eliminar">
                    <i class="fas fa-check"></i> Sí, Eliminar
                </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
      // Datos de ejemplo para simular entregas
const entregasEjemplo =  <?php echo $listarJSON; ?>

// Función para obtener el nombre legible de la calidad
function getNombreCalidad(calidad) {
    switch(calidad) {
        case 'exportacion':
            return 'Exportación';
        case 'nacional':
            return 'Nacional';
        case 'desecho':
            return 'Desecho';
        default:
            return calidad;
    }
}

// Función para cargar la tabla con los datos del array
function cargarTabla(datos) {
    const tbody = document.querySelector('.tabla-registros tbody');
    tbody.innerHTML = ''; // Limpiar tabla
    
    if (datos.length === 0) {
        // Si no hay datos, mostrar mensaje
        const filaVacia = document.createElement('tr');
        filaVacia.innerHTML = '<td colspan="7" style="text-align: center; padding: 20px;">No se encontraron registros que coincidan con los criterios de búsqueda</td>';
        tbody.appendChild(filaVacia);
        return;
    }
    
    // Crear filas con los datos
    datos.forEach(entrega => {
        const fila = document.createElement('tr');
        fila.id = `fila-${entrega.id}`;
        fila.onclick = () => mostrarModalEliminar(entrega.id);
        
        // Definir la clase según la calidad
        let estadoClass = `estado-${entrega.calidad}`;
        
        // Obtener nombre legible de la calidad para mostrar
        const nombreCalidad = getNombreCalidad(entrega.calidad);
        
        // Crear el contenido de la fila
        fila.innerHTML = `
            <td>${entrega.id}</td>
            <td>${entrega.fecha}</td>
            <td><span class="estado ${estadoClass}">${nombreCalidad}</span></td>
            <td>${entrega.cantidad}</td>
            <td>${entrega.empresa}</td>
            <td>${entrega.transportista}</td>
            <td>
                <button class="btn-accion btn-eliminar" onclick="mostrarModalEliminar('${entrega.id}', event)">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        
        tbody.appendChild(fila);
    });
}

// Función para filtrar entregas
function filtrarEntregas() {
    const terminoBusqueda = document.getElementById('buscarEntrega').value.toLowerCase();
    const filtroCalidad = document.getElementById('filtroCalidad').value;
    const filtroFecha = document.getElementById('filtroFecha').value;
    
    // Fecha actual para comparaciones
    const fechaActual = new Date();
    const hoy = fechaActual.toISOString().split('T')[0];
    
    // Calcular inicio de semana (domingo o lunes, según preferencia)
    const inicioSemana = new Date(fechaActual);
    const diaSemana = fechaActual.getDay(); // 0 es domingo, 1 es lunes, etc.
    const diasRestar = diaSemana === 0 ? 6 : diaSemana - 1; // Si es lunes como inicio
    inicioSemana.setDate(fechaActual.getDate() - diasRestar);
    const inicioSemanaStr = inicioSemana.toISOString().split('T')[0];
    
    // Inicio del mes actual
    const inicioMes = new Date(fechaActual.getFullYear(), fechaActual.getMonth(), 1);
    const inicioMesStr = inicioMes.toISOString().split('T')[0];
    
    // Filtrar los datos
    const datosFiltrados = entregasEjemplo.filter(entrega => {
        let cumpleBusqueda = true;
        let cumpleCalidad = true;
        let cumpleFecha = true;
        
        // Obtener nombre legible de la calidad para búsqueda
        const nombreCalidad = getNombreCalidad(entrega.calidad);
        
        // Filtrar por término de búsqueda
        if (terminoBusqueda) {
            cumpleBusqueda = entrega.id.toLowerCase().includes(terminoBusqueda) ||
                            entrega.fecha.toLowerCase().includes(terminoBusqueda) ||
                            nombreCalidad.toLowerCase().includes(terminoBusqueda) ||
                            entrega.empresa.toLowerCase().includes(terminoBusqueda) ||
                            entrega.transportista.toLowerCase().includes(terminoBusqueda) ||
                            entrega.cantidad.toString().includes(terminoBusqueda);
        }
        
        // Filtrar por calidad
        if (filtroCalidad) {
            cumpleCalidad = entrega.calidad === filtroCalidad;
        }
        
        // Filtrar por fecha
        if (filtroFecha) {
            switch(filtroFecha) {
                case 'hoy':
                    cumpleFecha = entrega.fecha === hoy;
                    break;
                case 'semana':
                    cumpleFecha = entrega.fecha >= inicioSemanaStr && entrega.fecha <= hoy;
                    break;
                case 'mes':
                    cumpleFecha = entrega.fecha >= inicioMesStr && entrega.fecha <= hoy;
                    break;
            }
        }
        
        return cumpleBusqueda && cumpleCalidad && cumpleFecha;
    });
    
    // Actualizar tabla con resultados filtrados
    cargarTabla(datosFiltrados);
}

// Función para mostrar el modal de eliminación
function mostrarModalEliminar(id, event) {
    if (event) {
        event.stopPropagation(); // Evitar que se propague el clic a la fila
    }
    
    // Resaltar la fila correspondiente
    document.querySelectorAll('.tabla-registros tbody tr').forEach(fila => {
        fila.classList.remove('fila-activa');
    });
    
    document.getElementById('fila-' + id).classList.add('fila-activa');
    
    // Cargar los datos de la entrega en el modal
    cargarDatosEntregaModal(id);
    
    // Mostrar el modal
    document.getElementById('modalEliminacion').style.display = 'flex';
}

// Función para cargar datos de la entrega en el modal
function cargarDatosEntregaModal(id) {
    // Buscar la entrega en el array de datos
    const entrega = entregasEjemplo.find(e => e.id === id);
    
    if (entrega) {
        // Establecer el ID en el modal
        document.getElementById('idRegistroModal').textContent = entrega.id;
        
        // Obtener nombre legible de la calidad
        const nombreCalidad = getNombreCalidad(entrega.calidad);
        
        // Mostrar los datos en el panel de detalles
        document.getElementById('fechaEntregaModal').textContent = entrega.fecha;
        document.getElementById('calidadProductoModal').textContent = nombreCalidad;
        document.getElementById('cantidadProductosModal').textContent = entrega.cantidad;
        document.getElementById('nombreEmpresaModal').textContent = entrega.empresa;
        document.getElementById('emailCompradorModal').textContent = entrega.email || 'No registrado';
        document.getElementById('nombreTransportistaModal').textContent = entrega.transportista;
    }
}

// Función para cerrar cualquier modal
function cerrarModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
    
    // Si se cierra el modal principal, también quitar resaltado de filas
    if (modalId === 'modalEliminacion') {
        document.querySelectorAll('.tabla-registros tbody tr').forEach(fila => {
            fila.classList.remove('fila-activa');
        });
    }
}

// Función para solicitar confirmación de eliminación
function solicitarConfirmacion() {
    // Obtener el ID de la entrega
    const id = document.getElementById('idRegistroModal').textContent;
    
    // Establecer el ID en el modal de confirmación
    document.getElementById('idConfirmacion').textContent = id;
    document.getElementById('idRegistro').value = id;
    // Ocultar el primer modal y mostrar el de confirmación
    document.getElementById('modalEliminacion').style.display = 'none';
    document.getElementById('modalConfirmacion').style.display = 'flex';
}

// Función para confirmar la eliminación

// Función para volver al listado (simulado)
function volverListado() {
    alert('Esta función redirigiría al listado completo de entregas');
}

// Inicializar la tabla al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    cargarTabla(entregasEjemplo);
    
    // Inicializar búsqueda
    document.getElementById('buscarEntrega').addEventListener('input', filtrarEntregas);
    
    // Inicializar filtros
    document.getElementById('filtroCalidad').addEventListener('change', filtrarEntregas);
    document.getElementById('filtroFecha').addEventListener('change', filtrarEntregas);
    
    // Cerrar modales si se hace clic fuera de ellos
    document.querySelectorAll('.modal-backdrop').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = 'none';
                
                // Quitar resaltado de filas si se cierra el modal principal
                if (this.id === 'modalEliminacion') {
                    document.querySelectorAll('.tabla-registros tbody tr').forEach(fila => {
                        fila.classList.remove('fila-activa');
                    });
                }
            }
        });
    });
});
    </script>
</body>
</html>