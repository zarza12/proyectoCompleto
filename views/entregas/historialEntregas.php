<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once '../../controllers/daoEntregas.php';
$nombre = $_SESSION['nombre'] ?? 'Invitado';
$avatar = substr($nombre, 0, 1);
// No cierres el PHP, simplemente empieza el HTML

$daoEntregas = new daoEntregas();
$listar = $daoEntregas->listarEntregas();
$listarJSON = json_encode($listar);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Entregas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Añadir jsPDF para generar PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
    <style>
        /* Reset para evitar desbordamiento */
        html, body {
            overflow-x: hidden;
            width: 100%;
            margin: 0;
            padding: 0;
        }
        
        :root {
            --color-primario: #4A235A; /* Color zarzamora oscuro */
            --color-secundario: #7D3C98; /* Color zarzamora medio */
            --color-acento: #A569BD; /* Color zarzamora claro */
            --color-resalte: #D2B4DE; /* Color zarzamora muy claro */
            --color-fondo: #F8F9F9;
            --color-blanco: #FFFFFF;
            --color-texto: #333333;
            --color-borde: #E5E8E8;
            --color-error: #E74C3C;
            --color-exito: #2ECC71;
            --fuente-principal: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            --sombra-suave: 0 4px 6px rgba(0, 0, 0, 0.1);
            --sombra-media: 0 6px 12px rgba(0, 0, 0, 0.15);
            --borde-radio: 10px;
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
            color: var(--color-texto);
            line-height: 1.6;
            padding: 0;
            width: 100%;
        }
        
        /* Contenedor principal ajustado para el menú */
        .contenido-principal {
            width: calc(100% - var(--menu-width-closed));
            margin-left: var(--menu-width-closed);
            transition: margin-left 0.3s ease, width 0.3s ease;
            padding: 20px;
            padding-top: 10px;
            box-sizing: border-box;
        }
        
        /* Ajuste cuando el menú está abierto */
        #menuPrincipal:hover ~ .contenido-principal {
            width: calc(100% - var(--menu-width-open));
            margin-left: var(--menu-width-open);
        }
        
        /* Título estilizado para el historial de entregas */
        .titulo-principal {
            background: linear-gradient(to right, var(--color-primario), var(--color-secundario));
            color: white;
            padding: 25px;
            border-radius: var(--borde-radio);
            margin-bottom: 30px;
            box-shadow: var(--sombra-media);
            position: relative;
            overflow: hidden;
            text-align: center;
        }
        
        .titulo-principal h1 {
            font-size: 32px;
            margin-bottom: 5px;
            position: relative;
            z-index: 1;
        }
        
        .titulo-principal p {
            font-size: 16px;
            opacity: 0.9;
            max-width: 700px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }
        
        .titulo-principal .icono-decorativo {
            position: absolute;
            right: 30px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 70px;
            opacity: 0.2;
            z-index: 0;
        }
        
        .titulo-principal .linea-decorativa {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 4px;
            width: 100%;
            background: linear-gradient(90deg, transparent, var(--color-resalte), transparent);
        }
        
        /* Sección de filtros simplificada */
        .seccion-filtros {
            background-color: var(--color-blanco);
            border-radius: var(--borde-radio);
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: var(--sombra-suave);
        }
        
        .titulo-filtros {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            color: var(--color-primario);
            font-size: 16px;
            font-weight: 600;
        }
        
        .titulo-filtros i {
            margin-right: 10px;
        }
        
        .filtros-compactos {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .filtros-compactos .campo-filtro {
            flex: 1;
        }
        
        .input-filtro,
        .select-filtro {
            padding: 10px 12px;
            border: 1px solid var(--color-borde);
            border-radius: 6px;
            font-size: 14px;
            background-color: white;
            outline: none;
            transition: var(--transicion);
        }
        
        .input-filtro:focus,
        .select-filtro:focus {
            border-color: var(--color-acento);
            box-shadow: 0 0 0 3px rgba(165, 105, 189, 0.1);
        }
        
        .select-filtro {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23333' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 15px;
            padding-right: 40px;
        }
        
        /* Tabla de datos */
        .tabla-contenedor {
            background-color: var(--color-blanco);
            border-radius: var(--borde-radio);
            box-shadow: var(--sombra-suave);
            overflow-x: auto; /* Permite deslizamiento horizontal */
            margin-bottom: 20px;
            width: 100%;
        }
        
        .tabla-historial {
            width: 100%;
            border-collapse: collapse;
        }
        
        .tabla-historial th {
            background-color: var(--color-primario);
            color: white;
            font-weight: 600;
            padding: 12px 15px;
            text-align: left;
            font-size: 14px;
        }
        
        .tabla-historial td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--color-borde);
        }
        
        .tabla-historial tr:hover {
            background-color: rgba(210, 180, 222, 0.2);
            transition: background-color 0.2s ease;
        }
        
        .tabla-historial tr:last-child td {
            border-bottom: none;
        }
        
        /* Indicadores */
        .indicador-cantidad {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: auto;
            min-width: 30px;
            height: 25px;
            padding: 0 8px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 600;
        }
        
        .indicador-exportacion {
            background-color: rgba(46, 204, 113, 0.2);
            color: #27ae60;
        }
        
        .indicador-nacional {
            background-color: rgba(52, 152, 219, 0.2);
            color: #2980b9;
        }
        
        .indicador-desecho {
            background-color: rgba(231, 76, 60, 0.2);
            color: #c0392b;
        }
        
        /* Botones de acción en tabla */
        .acciones-fila {
            display: flex;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-accion {
            width: 32px;
            height: 32px;
            border-radius: 4px;
            border: none;
            background-color: transparent;
            color: var(--color-texto);
            cursor: pointer;
            transition: var(--transicion);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-accion:hover {
            background-color: #f5f5f5;
            transform: translateY(-2px);
        }
        
        .btn-ver {
            color: var(--color-acento);
        }
        
        .btn-editar {
            color: var(--color-warning);
        }
        
        .btn-eliminar {
            color: var(--color-error);
        }
        
        /* Paginación */
        .paginacion {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-top: 15px;
        }
        
        .navegacion-paginas {
            display: flex;
            gap: 5px;
        }
        
        .btn-pagina {
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--color-borde);
            border-radius: 4px;
            background-color: white;
            cursor: pointer;
            transition: var(--transicion);
            font-weight: 500;
            color: var(--color-texto);
        }
        
        .btn-pagina:hover {
            background-color: #f5f5f5;
        }
        
        .btn-pagina.activa {
            background-color: var(--color-primario);
            color: white;
            border-color: var(--color-primario);
        }
        
        /* Modal */
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
            z-index: 1100; /* Mayor que el menú */
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .modal-overlay.activo {
            opacity: 1;
            visibility: visible;
        }
        
        .modal-contenido {
            background-color: white;
            border-radius: var(--borde-radio);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 700px;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-cabecera {
            padding: 20px;
            border-bottom: 1px solid var(--color-borde);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            background-color: white;
            z-index: 1;
        }
        
        .modal-titulo {
            font-size: 18px;
            color: var(--color-primario);
            font-weight: 600;
        }
        
        .modal-cerrar {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: var(--color-texto);
            transition: var(--transicion);
        }
        
        .modal-cerrar:hover {
            color: var(--color-error);
            transform: rotate(90deg);
        }
        
        .modal-cuerpo {
            padding: 20px;
        }
        
        /* Botón de Descargar PDF */
        .btn-pdf {
            display: flex;
            align-items: center;
            gap: 8px;
            background-color: var(--color-primario);
            color: white;
            border: none;
            border-radius: 6px;
            padding: 8px 16px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .btn-pdf:hover {
            background-color: var(--color-secundario);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .btn-pdf i {
            font-size: 16px;
        }
        
        /* Estilos para el reporte (sin rectángulos) */
        .detalle-seccion {
            margin-bottom: 25px;
            border-bottom: 1px solid var(--color-borde);
            padding-bottom: 20px;
        }
        
        .detalle-seccion:last-child {
            border-bottom: none;
        }
        
        .detalle-titulo {
            margin-bottom: 15px;
            font-weight: 600;
            color: var(--color-primario);
            display: flex;
            align-items: center;
            padding-bottom: 8px;
        }
        
        .detalle-titulo i {
            margin-right: 10px;
        }
        
        .detalle-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        
        .detalle-item {
            margin-bottom: 12px;
        }
        
        .detalle-etiqueta {
            font-weight: 600;
            color: var(--color-primario);
            margin-bottom: 5px;
            font-size: 13px;
        }
        
        .detalle-valor {
            font-size: 16px;
        }
        
        .detalle-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }
        
        .detalle-card {
            padding: 15px;
            border-radius: 8px;
            border-left: 3px solid;
        }
        
        .card-exportacion {
            background-color: rgba(46, 204, 113, 0.1);
            border-color: #2ecc71;
        }
        
        .card-cantidad {
            background-color: rgba(52, 152, 219, 0.1);
            border-color: #3498db;
        }
        
        .card-valor {
            background-color: rgba(155, 89, 182, 0.1);
            border-color: #9b59b6;
        }
        
        .card-etiqueta {
            font-weight: 600;
            margin-bottom: 5px;
            font-size: 13px;
        }
        
        .card-exportacion .card-etiqueta {
            color: #27ae60;
        }
        
        .card-cantidad .card-etiqueta {
            color: #2980b9;
        }
        
        .card-valor .card-etiqueta {
            color: #8e44ad;
        }
        
        .card-contenido {
            font-size: 22px;
            font-weight: 600;
        }
        
        /* Responsividad */
        @media (max-width: 1200px) {
            .detalle-cards {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .contenido-principal {
                width: 100%;
                margin-left: 0;
                padding: 20px;
                padding-top: 60px; /* Espacio para el botón de toggle */
            }
            
            #menuPrincipal:hover ~ .contenido-principal {
                width: 100%;
                margin-left: 0;
            }
            
            #menuPrincipal.active ~ .contenido-principal {
                width: 100%;
                margin-left: 0;
            }
            
            .filtros-compactos {
                flex-direction: column;
            }
            
            .paginacion {
                flex-direction: column;
                gap: 15px;
                align-items: flex-end;
            }
            
            .detalle-grid,
            .detalle-cards {
                grid-template-columns: 1fr;
            }
            
            .titulo-principal h1 {
                font-size: 24px;
            }
            
            .titulo-principal p {
                font-size: 14px;
            }
            
            .titulo-principal .icono-decorativo {
                font-size: 50px;
                right: 10px;
            }
        }
    </style>
</head>
<body>
<?php include '../../views/menuA.php'; ?>

<div class="contenido-principal">
    <!-- Título principal estilizado en lugar de las tarjetas de estadísticas -->
    <div class="titulo-principal">
        <h1>Historial de Entregas</h1>
        
        <div class="icono-decorativo"><i class="fas fa-truck-loading"></i></div>
        <div class="linea-decorativa"></div>
    </div>
    
    <!-- Sección de filtros simplificada -->
    <div class="seccion-filtros">
        <div class="titulo-filtros">
            <i class="fas fa-filter"></i> Filtros y búsqueda
        </div>
        <div class="filtros-compactos">
            <input type="date" class="input-filtro" id="fechaInicio" value="2025-02-15" onchange="filtrarEntregas()">
            <input type="date" class="input-filtro" id="fechaFin" value="2025-03-16" onchange="filtrarEntregas()">
            <select class="select-filtro" id="filtroCalidad" onchange="filtrarEntregas()">
                <option value="">Todas las calidades</option>
                <option value="exportacion">Exportación</option>
                <option value="nacional">Nacional</option>
                <option value="desecho">Desecho</option>
            </select>
        </div>
    </div>
    
    <!-- Tabla de historial de entregas -->
    <div class="tabla-contenedor">
        <table class="tabla-historial" id="tablaHistorialEntregas">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>FECHA</th>
                    <th>CALIDAD</th>
                    <th>CANTIDAD</th>
                    <th>DESTINO</th>
                    <th>TRANSPORTISTA</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody id="tbody-entregas">
                <!-- Las filas se generarán dinámicamente con JavaScript -->
            </tbody>
        </table>
    </div>
    
    <!-- Paginación -->
    <div class="paginacion">
        <div class="navegacion-paginas">
            <button class="btn-pagina activa">1</button>
            <button class="btn-pagina">2</button>
            <button class="btn-pagina">3</button>
            <button class="btn-pagina"><i class="fas fa-chevron-right"></i></button>
        </div>
    </div>
</div>

<!-- Modal de detalles de entrega -->
<div class="modal-overlay" id="modalDetallesEntrega">
    <div class="modal-contenido">
        <div class="modal-cabecera">
            <h3 class="modal-titulo">Detalles de Entrega</h3>
            <div style="display: flex; gap: 10px; align-items: center;">
                <button id="btnDescargarPDF" class="btn-pdf" onclick="generarPDF()">
                    <i class="fas fa-file-pdf"></i> Descargar PDF
                </button>
                <button class="modal-cerrar" onclick="cerrarModal()"><i class="fas fa-times"></i></button>
            </div>
        </div>
        <div class="modal-cuerpo" id="contenido-reporte">
            <!-- Ficha de detalle rediseñada sin rectángulos -->
            <div class="detalle-seccion">
                <div class="detalle-titulo">
                    <i class="fas fa-info-circle"></i> Información General
                </div>
                <div class="detalle-grid">
                    <div class="detalle-item">
                        <div class="detalle-etiqueta">ID</div>
                        <div class="detalle-valor" id="detalle-id"></div>
                    </div>
                    <div class="detalle-item">
                        <div class="detalle-etiqueta">Fecha</div>
                        <div class="detalle-valor" id="detalle-fecha"></div>
                    </div>
                    <div class="detalle-item">
                        <div class="detalle-etiqueta">Destino</div>
                        <div class="detalle-valor" id="detalle-destino"></div>
                    </div>
                    <div class="detalle-item">
                        <div class="detalle-etiqueta">Transportista</div>
                        <div class="detalle-valor" id="detalle-transportista"></div>
                    </div>
                </div>
            </div>
            
            <div class="detalle-seccion">
                <div class="detalle-titulo">
                    <i class="fas fa-box"></i> Información de Carga
                </div>
                <div class="detalle-cards">
                    <div class="detalle-card card-exportacion">
                        <div class="card-etiqueta">Calidad</div>
                        <div class="card-contenido" id="detalle-calidad"></div>
                    </div>
                    <div class="detalle-card card-cantidad">
                        <div class="card-etiqueta">Cantidad</div>
                        <div class="card-contenido" id="detalle-cantidad"></div>
                    </div>
                    <div class="detalle-card card-valor" style="display: none">
                        <div class="card-etiqueta">Valor</div>
                        <div class="card-contenido" id="detalle-valor"></div>
                    </div>
                </div>
            </div>
            
            <div class="detalle-seccion">
                <div class="detalle-titulo">
                    <i class="fas fa-info-circle"></i> Información Adicional
                </div>
                <div class="detalle-grid">
                    <div class="detalle-item">
                        <div class="detalle-etiqueta">Registrado por</div>
                        <div class="detalle-valor" id="detalle-usuario"></div>
                    </div>
                    <div class="detalle-item">
                        <div class="detalle-etiqueta">Comentarios</div>
                        <div class="detalle-valor" id="detalle-comentarios"></div>
                    </div>
                </div>
            </div>
            
            <div class="detalle-seccion">
                <div class="detalle-titulo">
                    <i class="fas fa-truck"></i> Datos de Transporte
                </div>
                <div class="detalle-grid">
                    <div class="detalle-item">
                        <div class="detalle-etiqueta">Conductor</div>
                        <div class="detalle-valor" id="detalle-conductor"></div>
                    </div>
                    <div class="detalle-item">
                        <div class="detalle-etiqueta">Placa del vehículo</div>
                        <div class="detalle-valor" id="detalle-placa"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    
<script>
    // Datos de entregas
    const datosEntregas = <?php echo $listarJSON; ?>;
    
    // Variable para almacenar la entrega actual
    let entregaActual = null;
    
    // Función para cargar las entregas en la tabla
    function cargarEntregas(entregas) {
        const tbody = document.getElementById('tbody-entregas');
        tbody.innerHTML = '';
        
        if (entregas.length === 0) {
            const tr = document.createElement('tr');
            tr.innerHTML = '<td colspan="7" style="text-align: center;">No hay entregas que coincidan con los filtros</td>';
            tbody.appendChild(tr);
            return;
        }
        
        entregas.forEach(entrega => {
            const tr = document.createElement('tr');
            
            // Hacer la fila clickeable para ver detalles
            tr.style.cursor = 'pointer';
            tr.onclick = function() {
                verDetalles(entrega.id);
            };
            
            // Determinar la clase del indicador según la calidad
            let indicadorClase = 'indicador-nacional';
            if (entrega.calidad === 'exportacion') {
                indicadorClase = 'indicador-exportacion';
            } else if (entrega.calidad === 'desecho') {
                indicadorClase = 'indicador-desecho';
            }
            
            // Capitalizar la primera letra de la calidad para mostrar
            const calidadMostrar = entrega.calidad.charAt(0).toUpperCase() + entrega.calidad.slice(1);
            
            tr.innerHTML = `
                <td>${entrega.id}</td>
                <td>${entrega.fecha}</td>
                <td>${calidadMostrar}</td>
                <td><span class="indicador-cantidad ${indicadorClase}">${entrega.cantidad}</span></td>
                <td>${entrega.empresa}</td>
                <td>${entrega.transportista}</td>
                <td>
                    <div class="acciones-fila">
                        <button class="btn-accion btn-ver" onclick="event.stopPropagation(); verDetalles('${entrega.id}')"><i class="fas fa-eye"></i></button>
                        <button class="btn-accion btn-editar" onclick="event.stopPropagation();"><i class="fas fa-edit"></i></button>
                        <button class="btn-accion btn-eliminar" onclick="event.stopPropagation();"><i class="fas fa-trash"></i></button>
                    </div>
                </td>
            `;
            
            tbody.appendChild(tr);
        });
    }
    
    // Función para filtrar entregas según los criterios seleccionados
    function filtrarEntregas() {
        const fechaInicio = document.getElementById('fechaInicio').value;
        const fechaFin = document.getElementById('fechaFin').value;
        const calidadSeleccionada = document.getElementById('filtroCalidad').value.toLowerCase();
        
        // Convertir fechas para comparación (ya están en formato YYYY-MM-DD)
        const fechaInicioObj = fechaInicio ? new Date(fechaInicio) : null;
        const fechaFinObj = fechaFin ? new Date(fechaFin) : null;
        
        // Filtrar el array de entregas
        const entregasFiltradas = datosEntregas.filter(entrega => {
            // Convertir fecha de la entrega (ya en formato YYYY-MM-DD)
            const fechaEntrega = new Date(entrega.fecha);
            
            // Filtrar por fecha
            if (fechaInicioObj && fechaEntrega < fechaInicioObj) return false;
            if (fechaFinObj && fechaEntrega > fechaFinObj) return false;
            
            // Filtrar por calidad
            if (calidadSeleccionada && entrega.calidad !== calidadSeleccionada) return false;
            
            return true;
        });
        
        // Actualizar la tabla con los resultados filtrados
        cargarEntregas(entregasFiltradas);
    }
    
    // Función para abrir el modal con detalles
    function verDetalles(id) {
        const entrega = datosEntregas.find(item => item.id === id);
        if (!entrega) return;
        
        // Guardar la entrega actual para poder generar el PDF
        entregaActual = entrega;
        
        // Llenar datos básicos
        document.getElementById('detalle-id').textContent = entrega.id;
        document.getElementById('detalle-fecha').textContent = entrega.fecha;
        document.getElementById('detalle-destino').textContent = entrega.empresa;
        document.getElementById('detalle-transportista').textContent = entrega.transportista;
        document.getElementById('detalle-usuario').textContent = '<?php echo $nombre; ?>';
        document.getElementById('detalle-comentarios').textContent = 'Entrega registrada correctamente';
        document.getElementById('detalle-conductor').textContent = entrega.transportista;
        document.getElementById('detalle-placa').textContent = entrega.placa || 'Sin registro';
        
        // Llenar datos de carga
        // Capitalizar la primera letra de la calidad para mostrar
        const calidadMostrar = entrega.calidad.charAt(0).toUpperCase() + entrega.calidad.slice(1);
        document.getElementById('detalle-calidad').textContent = calidadMostrar;
        document.getElementById('detalle-cantidad').textContent = entrega.cantidad;
        document.getElementById('detalle-valor').textContent = entrega.valor || `${entrega.cantidad * 15}.00`;
        
        // Mostrar el modal
        document.getElementById('modalDetallesEntrega').classList.add('activo');
    }
    
    // Función para cerrar el modal
    function cerrarModal() {
        document.getElementById('modalDetallesEntrega').classList.remove('activo');
    }
    
    // Función para generar el PDF
    function generarPDF() {
    if (!entregaActual) return;
    
    // Importar la librería jsPDF
    const { jsPDF } = window.jspdf;
    
    // Crear un nuevo documento PDF
    const doc = new jsPDF();
    
    // Configurar colores más claros y atractivos
    const colorPrimario = [122, 68, 149]; // Zarzamora más claro
    const colorSecundario = [165, 105, 189]; // Color zarzamora claro
    const colorAccento = [210, 180, 222]; // Color zarzamora muy claro
    const colorTextoOscuro = [60, 60, 60];
    const colorTextoClaro = [255, 255, 255];
    const colorFondo = [250, 250, 253]; // Fondo casi blanco con toque lavanda
    
    // Añadir fondo para toda la página
    doc.setFillColor(colorFondo[0], colorFondo[1], colorFondo[2]);
    doc.rect(0, 0, doc.internal.pageSize.getWidth(), doc.internal.pageSize.getHeight(), 'F');
    
    // Añadir encabezado con gradiente más suave
    doc.setFillColor(colorPrimario[0], colorPrimario[1], colorPrimario[2]);
    doc.rect(0, 0, doc.internal.pageSize.getWidth(), 35, 'F');
    
    // Añadir detalles decorativos con colores más claros
    doc.setFillColor(colorAccento[0], colorAccento[1], colorAccento[2]);
    doc.circle(doc.internal.pageSize.getWidth() - 20, 18, 12, 'F');
    doc.circle(20, doc.internal.pageSize.getHeight() - 20, 8, 'F');
    
    // Logo y nombre de la empresa
    doc.setFontSize(20);
    doc.setFont("helvetica", "bold");
    doc.setTextColor(colorTextoClaro[0], colorTextoClaro[1], colorTextoClaro[2]);
    doc.text("AGRÍCOLA", 20, 18);
    doc.setFontSize(10);
    doc.text("SISTEMA DE GESTIÓN DE ENTREGAS", 20, 28);
    
    // Borde para el reporte con color más suave
    doc.setDrawColor(colorSecundario[0], colorSecundario[1], colorSecundario[2]);
    doc.setLineWidth(0.5);
    doc.roundedRect(10, 40, doc.internal.pageSize.getWidth() - 20, doc.internal.pageSize.getHeight() - 55, 5, 5);
    
    // Título del reporte
    doc.setFontSize(22);
    doc.setTextColor(colorTextoClaro[0], colorTextoClaro[1], colorTextoClaro[2]);
    doc.text("REPORTE DE ENTREGA", doc.internal.pageSize.getWidth() / 2, 22, { align: "center" });
    
    // Identificador de entrega con fondo claro (no negro)
    doc.setFillColor(243, 231, 254); // Lavanda muy claro
    doc.roundedRect(15, 45, doc.internal.pageSize.getWidth() - 30, 20, 3, 3, 'F');
    
    // Borde decorativo para el identificador
    doc.setDrawColor(colorSecundario[0], colorSecundario[1], colorSecundario[2]);
    doc.setLineWidth(0.7);
    doc.roundedRect(15, 45, doc.internal.pageSize.getWidth() - 30, 20, 3, 3, 'S');
    
    doc.setTextColor(colorPrimario[0], colorPrimario[1], colorPrimario[2]);
    doc.setFontSize(16);
    doc.setFont("helvetica", "bold");
    doc.text(`ENTREGA: ${entregaActual.id}`, doc.internal.pageSize.getWidth() / 2, 58, { align: "center" });
    
    // Fecha de generación
    doc.setFontSize(8);
    doc.setTextColor(colorTextoClaro[0], colorTextoClaro[1], colorTextoClaro[2]);
    const fechaActual = new Date().toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' });
    doc.text(`Generado: ${fechaActual}`, doc.internal.pageSize.getWidth() - 15, 15, { align: "right" });
    
    // Sección de información principal con color más claro
    doc.setFillColor(colorSecundario[0], colorSecundario[1], colorSecundario[2]);
    doc.rect(15, 75, 6, 22, 'F');
    
    doc.setFontSize(14);
    doc.setTextColor(colorPrimario[0], colorPrimario[1], colorPrimario[2]);
    doc.text("INFORMACIÓN GENERAL", 30, 88);
    
    // Tabla de datos principales con fondo más claro
    let y = 105;
    doc.setFillColor(247, 245, 250); // Color muy tenue lavanda
    doc.roundedRect(15, y, doc.internal.pageSize.getWidth() - 30, 50, 3, 3, 'F');
    
    doc.setFontSize(10);
    doc.setTextColor(colorTextoOscuro[0], colorTextoOscuro[1], colorTextoOscuro[2]);
    
    // Fila 1
    doc.setFont("helvetica", "bold");
    doc.text("Fecha:", 25, y + 12);
    doc.text("Destino:", 25, y + 24);
    doc.text("Transportista:", 25, y + 36);
    
    // Datos fila 1
    doc.setFont("helvetica", "normal");
    doc.text(entregaActual.fecha, 70, y + 12);
    doc.text(entregaActual.empresa, 70, y + 24);
    doc.text(entregaActual.transportista, 70, y + 36);
    
    // Columna 2
    doc.setFont("helvetica", "bold");
    doc.text("Email:", 110, y + 12);
    doc.text("Calidad:", 110, y + 24);
    doc.text("Cantidad:", 110, y + 36);
    
    // Datos columna 2
    doc.setFont("helvetica", "normal");
    doc.text(entregaActual.email || "No disponible", 145, y + 12);
    
    const calidadMostrar = entregaActual.calidad.charAt(0).toUpperCase() + entregaActual.calidad.slice(1);
    doc.text(calidadMostrar, 145, y + 24);
    doc.text(`${entregaActual.cantidad} kg`, 145, y + 36);
    
    // Destacar la calidad y cantidad con el mismo fondo lavanda claro que el ID de entrega
    y = 170;
    doc.setFillColor(243, 231, 254); // Mismo color lavanda muy claro que usamos para ENTREGA: ENT-003
    doc.roundedRect(15, y, doc.internal.pageSize.getWidth() - 30, 40, 4, 4, 'F');
    
    // Borde decorativo para el resumen
    doc.setDrawColor(colorSecundario[0], colorSecundario[1], colorSecundario[2]);
    doc.setLineWidth(0.7);
    doc.roundedRect(15, y, doc.internal.pageSize.getWidth() - 30, 40, 4, 4, 'S');
    
    doc.setFontSize(16);
    doc.setFont("helvetica", "bold");
    doc.setTextColor(colorPrimario[0], colorPrimario[1], colorPrimario[2]);
    doc.text("RESUMEN DE ENTREGA", doc.internal.pageSize.getWidth() / 2, y + 15, { align: "center" });
    
    // Valor calculado
   // const valorCalculado = `${entregaActual.cantidad * 15}.00`;
   
   //doc.setFontSize(18);
    //doc.text(`Valor Total: ${valorCalculado}`, doc.internal.pageSize.getWidth() / 2, y + 30, { align: "center" });
    
    // Área para firmas
    y = 230;
    doc.setLineWidth(0.2);
    doc.setDrawColor(colorSecundario[0], colorSecundario[1], colorSecundario[2]);
    
    // Líneas de firma
    doc.line(40, y, 90, y);
    doc.line(doc.internal.pageSize.getWidth() - 40, y, doc.internal.pageSize.getWidth() - 90, y);
    
    doc.setFontSize(9);
    doc.setTextColor(colorPrimario[0], colorPrimario[1], colorPrimario[2]);
    doc.text("Firma del Entregador", 65, y + 7, { align: "center" });
    doc.text("Firma del Receptor", doc.internal.pageSize.getWidth() - 65, y + 7, { align: "center" });
    
    // Footer con diseño elegante más claro
    doc.setFillColor(colorSecundario[0], colorSecundario[1], colorSecundario[2]);
    doc.rect(0, doc.internal.pageSize.getHeight() - 15, doc.internal.pageSize.getWidth(), 15, 'F');
    
    // Texto del footer
    doc.setTextColor(colorTextoClaro[0], colorTextoClaro[1], colorTextoClaro[2]);
    doc.setFontSize(9);
    doc.text("SISTEMA DE GESTIÓN DE ENTREGAS | AGRÍCOLA 2025", doc.internal.pageSize.getWidth() / 2, 
            doc.internal.pageSize.getHeight() - 5, { align: "center" });
    
    // Guardar el PDF con nombre descriptivo
    doc.save(`Reporte_Entrega_${entregaActual.id}_${new Date().toISOString().slice(0,10)}.pdf`);
}
    
    // Evento para cerrar modal con Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            cerrarModal();
        }
    });
    
    // Cerrar modal al hacer clic fuera
    document.getElementById('modalDetallesEntrega').addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModal();
        }
    });
    
    // Cargar datos iniciales al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        // Cargar todas las entregas al inicio
        cargarEntregas(datosEntregas);
        
        // Configurar los eventos de los filtros
        //document.getElementById('fechaInicio').addEventListener('change', filtrarEntregas);
        //document.getElementById('fechaFin').addEventListener('change', filtrarEntregas);
        document.getElementById('filtroCalidad').addEventListener('change', filtrarEntregas);
    });
</script>
</body>
</html>