<?php
include_once '../../controllers/daoProduccion.php';
include_once  '../../models/Produccion.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['eliminarProduccion']) && $_POST['eliminarProduccion'] === 'eliminarProduccion') {
    // Recibir datos del formulario en PHP
    $idProduccion  = $_POST['idProduccion'];
    $daoProduccion = new daoProduccion();
    $registo = $daoProduccion->eliminarProduccion($idProduccion);
    

    if ($registo) {
        echo "
        <script>
            alert('Eliminado exitoso');
            window.location.href = 'eliminarProduccion.php';
        </script>";
    } else {
        mostrarMensaje("Error al Eliminado el registro.");
       
    }
   

}

$daoProduccion = new daoProduccion();
$listarProducciones = $daoProduccion->listarProduccion();

// Preparar datos para JavaScript
$produccionesJS = [];
foreach ($listarProducciones as $produccion) {
    $produccionesJS[] = [
        'id'          => $produccion->getId(),
        'id2'          => 'PROD-' . str_pad($produccion->getId(), 3, '0', STR_PAD_LEFT),
        'sector'      => $produccion->getSectorProduccion(),
        'fecha'       => $produccion->getFechaProduccion(),
        'exportacion' => (int)$produccion->getCalidadExportacion(),
        'nacional'    => (int)$produccion->getCalidadNacional(),
        'desecho'     => (int)$produccion->getCalidadDesecho()
    ];
}
$produccionesJSON = json_encode($produccionesJS);


?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Producción</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --color-primary: #4A235A; /* Color zarzamora oscuro */
            --color-secondary: #7D3C98; /* Color zarzamora medio */
            --color-accent: #A569BD; /* Color zarzamora claro */
            --color-hover: #D2B4DE; /* Color zarzamora muy claro */
            --color-text: #FFFFFF;
            --color-text-dark: #333333;
            --color-text-secondary: #666666;
            --color-border: #E0E0E0;
            --color-background: #F9F9F9;
            --color-success: #2ECC71;
            --color-warning: #F39C12;
            --color-danger: #E74C3C;
            --font-primary: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-hover: 0 6px 12px rgba(0, 0, 0, 0.15);
            --border-radius: 8px;
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: var(--font-primary);
            background-color: var(--color-background);
            color: var(--color-text-dark);
            line-height: 1.6;
        }
        
        /* Barra superior */
        .barra-superior {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--color-primary), var(--color-accent), var(--color-secondary));
            z-index: 1000;
        }
        
        /* Información de usuario */
        .info-usuario {
            position: fixed;
            top: 15px;
            right: 20px;
            display: flex;
            align-items: center;
            background-color: var(--color-primary);
            padding: 6px 15px;
            border-radius: 20px;
            color: var(--color-text);
            font-size: 14px;
            box-shadow: var(--shadow);
            z-index: 990;
            transition: var(--transition);
        }
        
        .info-usuario:hover {
            background-color: var(--color-secondary);
            transform: translateY(-2px);
        }
        
        .avatar-usuario {
            width: 28px;
            height: 28px;
            background-color: var(--color-accent);
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
        }
        
        /* Encabezado de la página */
        .encabezado-pagina {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 1px solid var(--color-border);
            padding-bottom: 15px;
        }
        
        .titulo-pagina {
            display: flex;
            align-items: center;
        }
        
        .titulo-pagina h1 {
            color: var(--color-primary);
            font-size: 28px;
            margin-right: 15px;
        }
        
        .icono-seccion {
            font-size: 24px;
            color: var(--color-danger);
            margin-right: 15px;
        }
        
        .botones-accion {
            display: flex;
            gap: 10px;
        }
        
        /* Advertencia */
        .alerta-advertencia {
            display: flex;
            align-items: center;
            background-color: rgba(243, 156, 18, 0.1);
            border-left: 4px solid var(--color-warning);
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 0 var(--border-radius) var(--border-radius) 0;
        }
        
        .alerta-advertencia i {
            color: var(--color-warning);
            font-size: 24px;
            margin-right: 15px;
        }
        
        .alerta-advertencia p {
            color: #7F5006;
            font-weight: 500;
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
            border: 1px solid var(--color-border);
            border-radius: var(--border-radius);
            padding: 10px 15px;
            width: 300px;
            box-shadow: var(--shadow);
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
            color: var(--color-secondary);
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
            border: 1px solid var(--color-border);
            border-radius: var(--border-radius);
            padding: 10px 35px 10px 15px;
            font-size: 14px;
            color: var(--color-text-dark);
            cursor: pointer;
            outline: none;
            box-shadow: var(--shadow);
            min-width: 150px;
        }
        
        .filtro::after {
            content: '\f0d7';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            color: var(--color-secondary);
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
        }
        
        /* Tabla de registros */
        .tabla-contenedor {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
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
            border-bottom: 1px solid var(--color-border);
        }
        
        .tabla-registros th {
            background-color: var(--color-primary);
            color: var(--color-text);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
        }
        
        .tabla-registros tr {
            cursor: pointer;
            transition: var(--transition);
        }
        
        .tabla-registros tr:hover {
            background-color: #f5f5f5;
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }
        
        .tabla-registros .fila-activa {
            background-color: rgba(231, 76, 60, 0.1);
            border-left: 4px solid var(--color-danger);
        }
        
        .tabla-registros .fila-activa:hover {
            background-color: rgba(231, 76, 60, 0.15);
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
            transition: var(--transition);
            color: var(--color-text-secondary);
            border-radius: 4px;
        }
        
        .btn-accion:hover {
            transform: translateY(-2px);
        }
        
        .btn-eliminar {
            color: var(--color-danger);
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
            border: 1px solid var(--color-border);
            border-radius: 4px;
            padding: 8px 12px;
            cursor: pointer;
            transition: var(--transition);
            color: var(--color-text-dark);
            font-weight: 500;
        }
        
        .btn-pagina:hover {
            background-color: var(--color-hover);
            color: var(--color-primary);
        }
        
        .btn-pagina.activa {
            background-color: var(--color-primary);
            color: white;
            border-color: var(--color-primary);
        }
        
        /* Estados de calidad */
        .estado {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
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
        
        /* Botones de acción */
        .botones-form {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--color-border);
        }
        
        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            transition: var(--transition);
        }
        
        .btn i {
            margin-right: 8px;
        }
        
        .btn-peligro {
            background-color: var(--color-danger);
            color: white;
        }
        
        .btn-peligro:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }
        
        .btn-secundario {
            background-color: #f1f1f1;
            color: var(--color-text-dark);
        }
        
        .btn-secundario:hover {
            background-color: #e1e1e1;
        }
        
        /* Modal de detalles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
            padding: 15px;
        }
        
        .modal-overlay.activo {
            opacity: 1;
            visibility: visible;
        }
        
        .modal-contenido {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-hover);
            padding: 0;
            max-width: 650px;
            width: 100%;
            transform: translateY(20px);
            transition: var(--transition);
            overflow: hidden;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
        }
        
        .modal-overlay.activo .modal-contenido {
            transform: translateY(0);
        }
        
        .modal-encabezado {
            background-color: var(--color-primary);
            color: var(--color-text);
            padding: 20px 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .modal-titulo {
            font-size: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .modal-titulo i {
            margin-right: 15px;
            font-size: 24px;
        }
        
        .btn-cerrar-modal {
            background: none;
            border: none;
            color: var(--color-text);
            font-size: 24px;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .btn-cerrar-modal:hover {
            transform: rotate(90deg);
        }
        
        .modal-cuerpo {
            padding: 25px;
            overflow-y: auto;
        }
        
        .id-badge {
            background-color: var(--color-accent);
            color: white;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 14px;
            font-weight: 600;
            margin-left: 10px;
        }
        
        .detalles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .detalle-item {
            background-color: #f9f9f9;
            border-radius: var(--border-radius);
            padding: 15px;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }
        
        .detalle-item:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }
        
        .detalle-item-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--color-secondary);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }
        
        .detalle-item-label i {
            margin-right: 8px;
        }
        
        .detalle-item-valor {
            font-size: 18px;
            font-weight: 500;
            color: var(--color-text-dark);
        }
        
        .modal-alerta {
            background-color: rgba(231, 76, 60, 0.1);
            border-left: 4px solid var(--color-danger);
            padding: 15px;
            margin: 15px 25px 0;
            color: #c0392b;
            font-weight: 500;
            font-size: 15px;
            line-height: 1.5;
        }
        
        .modal-pie {
            background-color: #f5f5f5;
            padding: 20px 25px;
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            border-top: 1px solid var(--color-border);
        }
        
        /* Confirmación modal */
        .confirmacion-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
            padding: 15px;
        }
        
        .confirmacion-overlay.activo {
            opacity: 1;
            visibility: visible;
        }
        
        .confirmacion-modal {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-hover);
            padding: 25px;
            max-width: 450px;
            width: 100%;
            border-top: 5px solid var(--color-danger);
            transform: scale(0.9);
            transition: var(--transition);
        }
        
        .confirmacion-overlay.activo .confirmacion-modal {
            transform: scale(1);
        }
        
        .confirmacion-titulo {
            color: var(--color-text-dark);
            margin-bottom: 20px;
            font-size: 18px;
            display: flex;
            align-items: center;
        }
        
        .confirmacion-titulo i {
            color: var(--color-danger);
            margin-right: 15px;
            font-size: 24px;
        }
        
        .confirmacion-mensaje {
            margin-bottom: 25px;
            color: var(--color-text-secondary);
            line-height: 1.6;
        }
        
        .confirmacion-botones {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
        }
        
        /* Responsivo */
        @media (max-width: 768px) {
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
            
            .detalles-grid {
                grid-template-columns: 1fr;
            }
            
            .tabla-registros {
                display: block;
                overflow-x: auto;
            }
            
            .modal-contenido {
                max-height: 85vh;
            }
            
            .modal-titulo {
                font-size: 16px;
            }
            
            .modal-titulo i {
                font-size: 20px;
                margin-right: 10px;
            }
            
            .id-badge {
                margin-left: 0;
                margin-top: 5px;
            }
            
            .modal-pie {
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
            
            .confirmacion-botones {
                flex-direction: column;
            }
            
            .detalle-item-valor {
                font-size: 16px;
            }
        }
        
        /* Notificaciones */
        .notificacion {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 15px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            min-width: 300px;
            max-width: 450px;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease;
        }
        
        .notificacion-icono {
            margin-right: 15px;
            font-size: 24px;
        }
        
        .notificacion-success .notificacion-icono {
            color: var(--color-success);
        }
        
        .notificacion-error .notificacion-icono {
            color: var(--color-danger);
        }
        
        .notificacion-mensaje {
            flex: 1;
            font-size: 14px;
            color: var(--color-text-dark);
        }
        
        #contenedor-notificaciones {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
        }
        
        @media (max-width: 480px) {
            #contenedor-notificaciones {
                right: 10px;
                left: 10px;
            }
            
            .notificacion {
                min-width: auto;
                width: 100%;
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
                <i class="fas fa-trash-alt icono-seccion"></i>
                <h1>Eliminar Producción</h1>
            </div>
        </div>
        
        <!-- Advertencia general -->
        <div class="alerta-advertencia">
            <i class="fas fa-exclamation-triangle"></i>
            <p>La eliminación de registros es permanente. Esta acción no se puede deshacer. Por favor, verifique cuidadosamente los detalles antes de eliminar.</p>
        </div>
        
        <div class="filtro-busqueda">
            <div class="busqueda">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Buscar registros..." id="buscarRegistro">
            </div>
            <div class="filtros">
                <div class="filtro">
                    <select id="filtroSector">
                        <option value="">Todos los sectores</option>
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
                        <th>Sector</th>
                        <th>Fecha</th>
                        <th>Cajas Totales</th>
                        <th>Exportación</th>
                        <th>Nacional</th>
                        <th>Desecho</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-cuerpo">
                    <!-- Los datos se cargarán desde JS -->
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
    
    <!-- Modal de detalles y eliminación -->
    <div class="modal-overlay" id="modalDetalles">
        <div class="modal-contenido">
            <div class="modal-encabezado">
                <div class="modal-titulo">
                    <i class="fas fa-info-circle"></i>
                    <span>Detalles de Producción</span>
                    <span class="id-badge" id="modalIdRegistro">PROD-001</span>
                </div>
                <button class="btn-cerrar-modal" onclick="cerrarModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="modal-alerta">
                Al eliminar este registro se perderán todos los datos asociados de forma permanente.
            </div>

            <div class="modal-cuerpo">
                <div class="detalles-grid">
                    <div class="detalle-item">
                        <div class="detalle-item-label">
                            <i class="fas fa-calendar"></i>
                            Fecha
                        </div>
                        <div class="detalle-item-valor" id="modalFecha">16/03/2025</div>
                    </div>
                    <div class="detalle-item">
                        <div class="detalle-item-label">
                            <i class="fas fa-map-marker-alt"></i>
                            Sector
                        </div>
                        <div class="detalle-item-valor" id="modalSector">Sector A</div>
                    </div>
                    <div class="detalle-item">
                        <div class="detalle-item-label">
                            <i class="fas fa-box"></i>
                            Total Cajas
                        </div>
                        <div class="detalle-item-valor" id="modalCajas">150</div>
                    </div>
                    <div class="detalle-item">
                        <div class="detalle-item-label">
                            <i class="fas fa-ship"></i>
                            Exportación
                        </div>
                        <div class="detalle-item-valor" id="modalExportacion">120</div>
                    </div>
                    <div class="detalle-item">
                        <div class="detalle-item-label">
                            <i class="fas fa-flag"></i>
                            Nacional
                        </div>
                        <div class="detalle-item-valor" id="modalNacional">25</div>
                    </div>
                    <div class="detalle-item">
                        <div class="detalle-item-label">
                            <i class="fas fa-trash"></i>
                            Desecho
                        </div>
                        <div class="detalle-item-valor" id="modalDesecho">5</div>
                    </div>
                </div>
            </div>
            <div class="modal-pie">
                <button type="button" class="btn btn-secundario" onclick="cerrarModal()">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-peligro" onclick="confirmarEliminacion()">
                    <i class="fas fa-trash-alt"></i> Eliminar Registro
                </button>
            </div>
        </div>
    </div>
    
    <!-- Modal de confirmación -->
    <div class="confirmacion-overlay" id="confirmacionOverlay">
        <form  method="POST" action="eliminarProduccion.php">
        <div class="confirmacion-modal">
            <h3 class="confirmacion-titulo"><i class="fas fa-exclamation-triangle"></i> Confirmar eliminación</h3>
            <p class="confirmacion-mensaje">¿Está seguro que desea eliminar permanentemente el registro <strong id="idConfirmacion">PROD-001</strong>? Esta acción no se puede deshacer.</p>
            <div class="confirmacion-botones">
                <button class="btn btn-secundario" onclick="cancelarConfirmacion()">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <input type="hidden" id="idProduccion" name="idProduccion" value="">
                <button type="submit" class="btn btn-peligro" id="eliminarProduccion" name="eliminarProduccion" value="eliminarProduccion">
                    <i class="fas fa-check"></i> Sí, eliminar
                </button>
            </div>
        </div>
        </form>
    </div>
    
    <script>
        // Datos de producción desde PHP
         const datosProduccion = <?php echo $produccionesJSON; ?>;

        // ID del registro seleccionado actualmente
        let registroSeleccionadoId = null;

        // Procesamos los datos para añadir fechaISO y calcular cajas totales
         function procesarDatos(datos) {
        return datos.map(registro => {
        // Calcular cajas totales sumando exportación + nacional + desecho
        const cajasTotales = registro.exportacion + registro.nacional + registro.desecho;
        
        // Agregar al objeto el total de cajas y la fecha ISO
        return {
            ...registro,
            cajas: cajasTotales, // Añadir el total de cajas calculado
            fechaISO: convertirFechaAISO(registro.fecha) // Convertir la fecha al formato ISO
              };
            });
                }

        // Convertir fecha de formato DD/MM/YYYY a YYYY-MM-DD (ISO)
        function convertirFechaAISO(fecha) {
    if (!fecha) return '';
    // Si la fecha ya está en formato ISO, la devolvemos tal cual
    if (fecha.includes('-')) return fecha;
    
    // Dividir la fecha por partes
    const partes = fecha.split('/');
    if (partes.length !== 3) return fecha; // Si no tiene el formato esperado, devolver la original
    
    // Reconstruir en formato ISO: YYYY-MM-DD
    return `${partes[2]}-${partes[1]}-${partes[0]}`;
}

// Obtener fecha de hoy en formato ISO local (sin ajuste de zona horaria)
function getLocalISODate() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

// Obtener fecha de inicio de semana en formato ISO (desde el lunes)
function getStartOfWeekDate() {
    const now = new Date();
    const day = now.getDay(); // 0 = domingo, 1 = lunes, etc.
    const diff = now.getDate() - day + (day === 0 ? -6 : 1); // Ajuste para que la semana comience en lunes
    const startOfWeek = new Date(now);
    startOfWeek.setDate(diff);
    
    const year = startOfWeek.getFullYear();
    const month = String(startOfWeek.getMonth() + 1).padStart(2, '0');
    const date = String(startOfWeek.getDate()).padStart(2, '0');
    return `${year}-${month}-${date}`;
}

// Obtener fecha de inicio de mes en formato ISO
function getStartOfMonthDate() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    return `${year}-${month}-01`;
}

// Cargar los datos en la tabla al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    // Procesar datos para añadir cajas totales y formato ISO de fecha
    const datosProcesados = procesarDatos(datosProduccion);
    cargarTabla(datosProcesados);
    populateFiltroSector();
});

// Función para cargar los datos en la tabla
function cargarTabla(datos) {
    const tablaCuerpo = document.getElementById('tabla-cuerpo');
    tablaCuerpo.innerHTML = '';
    
    datos.forEach(registro => {
        const fila = document.createElement('tr');
        fila.id = `fila-${registro.id}`;
        fila.dataset.fechaIso = registro.fechaISO; // Guardar la fecha ISO como atributo data
        fila.onclick = function() { abrirModalDetalles(registro.id); };
        
        fila.innerHTML = `
            <td>${registro.id2 || registro.id}</td>
            <td>${registro.sector}</td>
            <td>${registro.fecha}</td>
            <td><strong>${registro.cajas}</strong></td>
            <td><span class="estado estado-exportacion">${registro.exportacion}</span></td>
            <td><span class="estado estado-nacional">${registro.nacional}</span></td>
            <td><span class="estado estado-desecho">${registro.desecho}</span></td>
            <td>
                <button class="btn-accion btn-eliminar" onclick="abrirModalDetalles('${registro.id}', event)">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        
        tablaCuerpo.appendChild(fila);
    });
}

// Función para abrir el modal de detalles
function abrirModalDetalles(id, event) {
    if (event) {
        event.stopPropagation(); // Evitar que se propague el evento si se hizo clic en el botón
    }
    
    // Remover clase activa de todas las filas
    document.querySelectorAll('.tabla-registros tbody tr').forEach(fila => {
        fila.classList.remove('fila-activa');
    });
    
    // Agregar clase activa a la fila seleccionada
    document.getElementById(`fila-${id}`).classList.add('fila-activa');
    
    // Obtener los datos del registro
    const registro = procesarDatos(datosProduccion).find(r => r.id === id);
    registroSeleccionadoId = id;
    
    if (registro) {
        // Actualizar los datos en el modal
        document.getElementById('modalIdRegistro').textContent = registro.id2 || registro.id;
        document.getElementById('modalFecha').textContent = registro.fecha;
        document.getElementById('modalSector').textContent = registro.sector;
        document.getElementById('modalCajas').textContent = registro.cajas;
        document.getElementById('modalExportacion').textContent = registro.exportacion;
        document.getElementById('modalNacional').textContent = registro.nacional;
        document.getElementById('modalDesecho').textContent = registro.desecho;
        
        // Mostrar el modal
        document.getElementById('modalDetalles').classList.add('activo');
        
        // Añadir desenfoque al fondo para mejor enfoque en el modal
        document.querySelector('.contenedor').style.filter = 'blur(3px)';
    }
}

// Función para cerrar el modal
function cerrarModal() {
    document.getElementById('modalDetalles').classList.remove('activo');
    document.querySelector('.contenedor').style.filter = 'none';
    
    // Remover clase activa de todas las filas
    document.querySelectorAll('.tabla-registros tbody tr').forEach(fila => {
        fila.classList.remove('fila-activa');
    });
}

// Función para mostrar la confirmación
function confirmarEliminacion() {
    // Mostrar el ID en el modal de confirmación
    document.getElementById('idConfirmacion').textContent = registroSeleccionadoId;
    
    document.getElementById('idProduccion').value = registroSeleccionadoId;
    // Mostrar el modal de confirmación
    document.getElementById('confirmacionOverlay').classList.add('activo');
}

// Función para cancelar la confirmación
function cancelarConfirmacion() {
    document.getElementById('confirmacionOverlay').classList.remove('activo');
}

// Función para eliminar el registro
function eliminarRegistro() {
    if (!registroSeleccionadoId) return;
    
    // Eliminar el registro del array de datos
    const indice = datosProduccion.findIndex(r => r.id === registroSeleccionadoId);
    if (indice !== -1) {
        datosProduccion.splice(indice, 1);
    }
    
    // Actualizar la tabla con los datos procesados
    cargarTabla(procesarDatos(datosProduccion));
    
    // Ocultar los modales
    document.getElementById('confirmacionOverlay').classList.remove('activo');
    document.getElementById('modalDetalles').classList.remove('activo');
    document.querySelector('.contenedor').style.filter = 'none';
    
    // Mostrar mensaje de éxito
    mostrarNotificacion(`Registro ${registroSeleccionadoId} eliminado correctamente`, 'success');
    
    // Limpiar el ID seleccionado
    registroSeleccionadoId = null;
}

// Función para mostrar notificaciones temporales
function mostrarNotificacion(mensaje, tipo) {
    // Crear el elemento de notificación
    const notificacion = document.createElement('div');
    notificacion.className = `notificacion notificacion-${tipo}`;
    notificacion.innerHTML = `
        <div class="notificacion-icono">
            <i class="fas fa-${tipo === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        </div>
        <div class="notificacion-mensaje">${mensaje}</div>
    `;
    
    // Crear o obtener el contenedor de notificaciones
    let contenedorNotificaciones = document.getElementById('contenedor-notificaciones');
    if (!contenedorNotificaciones) {
        contenedorNotificaciones = document.createElement('div');
        contenedorNotificaciones.id = 'contenedor-notificaciones';
        document.body.appendChild(contenedorNotificaciones);
    }
    
    // Añadir la notificación al contenedor
    contenedorNotificaciones.appendChild(notificacion);
    
    // Animar la entrada
    setTimeout(() => {
        notificacion.style.opacity = '1';
        notificacion.style.transform = 'translateX(0)';
    }, 10);
    
    // Eliminar la notificación después de 3 segundos
    setTimeout(() => {
        notificacion.style.opacity = '0';
        notificacion.style.transform = 'translateX(100%)';
        setTimeout(() => {
            notificacion.remove();
        }, 300);
    }, 3000);
}

// Inicializar búsqueda
document.getElementById('buscarRegistro').addEventListener('input', function(e) {
    const terminoBusqueda = e.target.value.toLowerCase();
    
    // Filtrar los datos
    const datosProcesados = procesarDatos(datosProduccion);
    const datosFiltrados = datosProcesados.filter(registro => {
        return (
            (registro.id2 || registro.id).toString().toLowerCase().includes(terminoBusqueda) ||
            registro.sector.toLowerCase().includes(terminoBusqueda) ||
            registro.fecha.toLowerCase().includes(terminoBusqueda) ||
            registro.cajas.toString().includes(terminoBusqueda) ||
            registro.exportacion.toString().includes(terminoBusqueda) ||
            registro.nacional.toString().includes(terminoBusqueda) ||
            registro.desecho.toString().includes(terminoBusqueda)
        );
    });
    
    // Actualizar la tabla con los datos filtrados
    cargarTabla(datosFiltrados);
});

// Inicializar filtros
document.getElementById('filtroSector').addEventListener('change', function() {
    aplicarFiltros();
});

document.getElementById('filtroFecha').addEventListener('change', function() {
    aplicarFiltros();
});

// FUNCIÓN APLICAR FILTROS CORREGIDA
function aplicarFiltros() {
    const filtroSector = document.getElementById('filtroSector').value;
    const filtroFecha = document.getElementById('filtroFecha').value;
    const terminoBusqueda = document.getElementById('buscarRegistro').value.toLowerCase();
    
    // Obtener fechas ISO para comparación
    const hoyISO = getLocalISODate();
    const inicioSemanaISO = getStartOfWeekDate();
    const inicioMesISO = getStartOfMonthDate();
    
    // Procesar los datos para tener cajas totales y fechas ISO
    const datosProcesados = procesarDatos(datosProduccion);
    
    // Filtrar los datos
    const datosFiltrados = datosProcesados.filter(registro => {
        let cumpleFiltroSector = true;
        let cumpleFiltroFecha = true;
        let cumpleBusqueda = true;
        
        // Filtro por sector
        if (filtroSector && registro.sector !== filtroSector) {
            cumpleFiltroSector = false;
        }
        
        // Filtro por fecha usando fechas ISO para comparación correcta
        if (filtroFecha) {
            if (filtroFecha === 'hoy' && registro.fechaISO !== hoyISO) {
                cumpleFiltroFecha = false;
            } else if (filtroFecha === 'semana' && registro.fechaISO < inicioSemanaISO) {
                cumpleFiltroFecha = false;
            } else if (filtroFecha === 'mes' && registro.fechaISO < inicioMesISO) {
                cumpleFiltroFecha = false;
            }
        }
        
        // Término de búsqueda
        if (terminoBusqueda) {
            cumpleBusqueda = (
                (registro.id2 || registro.id).toString().toLowerCase().includes(terminoBusqueda) ||
                registro.sector.toLowerCase().includes(terminoBusqueda) ||
                registro.fecha.toLowerCase().includes(terminoBusqueda) ||
                registro.cajas.toString().includes(terminoBusqueda) ||
                registro.exportacion.toString().includes(terminoBusqueda) ||
                registro.nacional.toString().includes(terminoBusqueda) ||
                registro.desecho.toString().includes(terminoBusqueda)
            );
        }
        
        return cumpleFiltroSector && cumpleFiltroFecha && cumpleBusqueda;
    });
    
    // Actualizar la tabla con los datos filtrados
    cargarTabla(datosFiltrados);
}

// Efecto de tecla Escape para cerrar modales
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModal();
        cancelarConfirmacion();
    }
});

// Cerrar modales al hacer clic fuera de ellos
document.getElementById('modalDetalles').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModal();
    }
});

document.getElementById('confirmacionOverlay').addEventListener('click', function(e) {
    if (e.target === this) {
        cancelarConfirmacion();
    }
});

// Función para poblar el filtro de sectores
function populateFiltroSector() {
    const sectores = [
        { value: 'Sector A', label: 'Sector A' },
        { value: 'Sector B', label: 'Sector B' },
        { value: 'Sector C', label: 'Sector C' },
        { value: 'Sector D', label: 'Sector D' }
    ];
    
    const filtro = document.getElementById('filtroSector');
    // Limpia las opciones anteriores salvo la por defecto
    filtro.innerHTML = '<option value="">Todos los sectores</option>';
    sectores.forEach(sec => {
        const opt = document.createElement('option');
        opt.value = sec.value;
        opt.textContent = sec.label;
        filtro.appendChild(opt);
    });
}
    </script>
</body>
</html>