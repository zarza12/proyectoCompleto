<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once '../../controllers/daoProduccion.php';

$daoProduccion = new daoProduccion();
$listarProducciones = $daoProduccion->listarProduccion();

// Preparar datos para JavaScript
$produccionesJS = [];
foreach ($listarProducciones as $produccion) {
    $produccionesJS[] = [
        'id'          => $produccion->getId(),
        'id2'         => 'PROD-' . str_pad($produccion->getId(), 3, '0', STR_PAD_LEFT),
        'sector'      => $produccion->getSectorProduccion(),
        'fecha'       => $produccion->getFechaProduccion(),
        'exportacion' => (int)$produccion->getCalidadExportacion(),
        'nacional'    => (int)$produccion->getCalidadNacional(),
        'desecho'     => (int)$produccion->getCalidadDesecho()
    ];
}
$produccionesJSON = json_encode($produccionesJS);
// No cierres el PHP, simplemente empieza el HTML
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Producción</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        
        /* Estilo para el título principal */
        .titulo-principal {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--color-resalte);
        }
        
        .titulo-principal h1 {
            font-size: 26px;
            color: var(--color-primario);
            font-weight: 700;
            display: flex;
            align-items: center;
        }
        
        .titulo-principal h1 i {
            margin-right: 12px;
            font-size: 24px;
            color: var(--color-acento);
        }
        
        /* Nuevos estilos para la sección de filtros */
        .seccion-filtros {
            background: linear-gradient(to right, #f9f4fc, #f3eaf7); /* Gradiente suave */
            border-radius: var(--borde-radio);
            padding: 22px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border-left: 4px solid var(--color-acento);
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
            color: var(--color-acento);
            font-size: 18px;
        }
        
        .filtros-menu {
            display: flex;
            align-items: center;
            gap: 18px;
        }
        
        .filtro-item {
            flex: 1;
            min-width: 200px;
            max-width: 250px;
        }
        
        .filtro-label {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
            font-weight: 500;
            color: var(--color-secundario);
        }
        
        .input-filtro,
        .select-filtro {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #e8d8f3; /* Borde más suave */
            border-radius: 8px;
            font-size: 14px;
            background-color: white;
            outline: none;
            transition: var(--transicion);
        }
        
        .input-filtro:focus,
        .select-filtro:focus {
            border-color: var(--color-acento);
            box-shadow: 0 0 0 3px rgba(165, 105, 189, 0.15);
        }
        
        .btn-filtro {
            padding: 11px 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transicion);
        }
        
        .btn-limpiar-filtro {
            background-color: #e8d8f3;
            color: var(--color-secundario);
        }
        
        .btn-limpiar-filtro:hover {
            background-color: #d9c4e8;
            transform: translateY(-2px);
        }
        
        .btn-filtro i {
            margin-right: 8px;
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
            background-color: rgba(210, 180, 222, 0.1);
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
            color: var(--color-secundario);
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
        
        /* Estilos para el botón de PDF */
        .acciones-modal {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .btn-modal-pdf {
            background-color: var(--color-primario);
            color: white;
            border: none;
            border-radius: 6px;
            padding: 8px 15px;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: var(--transicion);
        }
        
        .btn-modal-pdf:hover {
            background-color: var(--color-secundario);
        }
        
        /* Estilos para el PDF */
        .pdf-container {
            display: none;
        }
        
        /* Responsividad */
        @media (max-width: 1200px) {
            .filtros-menu {
                flex-wrap: wrap;
            }
            
            .filtro-item {
                min-width: 150px;
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
            
            .filtros-menu {
                flex-direction: column;
                align-items: stretch;
            }
            
            .filtro-item {
                max-width: 100%;
            }
            
            .botones-filtro {
                flex-direction: column;
                width: 100%;
            }
            
            .btn-filtro {
                width: 100%;
            }
            
            .paginacion {
                flex-direction: column;
                gap: 15px;
                align-items: flex-end;
            }
        }
        
        /* Cursor pointer para las filas de tabla */
        .tabla-historial tr {
            cursor: pointer;
        }
        
        /* Estilo para contador de resultados */
        .contador-resultados {
            text-align: right;
            margin-bottom: 10px;
            font-size: 14px;
            color: var(--color-primario);
            font-weight: 500;
            line-height: 1.4;
        }
    </style>
</head>
<body>
<?php include '../../views/menuA.php'; ?>

<div class="contenido-principal">
    <!-- Título del historial de producción -->
    <div class="titulo-principal">
        <h1><i class="fas fa-history"></i> Historial de Producción</h1>
    </div>
    
    <!-- Sección de filtros con nuevo estilo -->
    <div class="seccion-filtros">
        <div class="titulo-filtros">
            <i class="fas fa-filter"></i> Filtros de producción
        </div>
        <div class="filtros-menu">
            <div class="filtro-item">
                <label class="filtro-label">Desde:</label>
                <input type="date" class="input-filtro" id="fechaInicio" value="2025-02-15" onchange="aplicarFiltros()">
            </div>
            
            <div class="filtro-item">
                <label class="filtro-label">Hasta:</label>
                <input type="date" class="input-filtro" id="fechaFin" value="2025-03-16" onchange="aplicarFiltros()">
            </div>
            
            <div class="filtro-item">
                <label class="filtro-label">Sector:</label>
                <select class="select-filtro" id="filtroSector" onchange="aplicarFiltros()">
                    <option value="">Todos los sectores</option>
                    <!-- Se llenará dinámicamente con JavaScript -->
                </select>
            </div>
            
            <div class="botones-filtro">
                <button class="btn-filtro btn-limpiar-filtro" onclick="limpiarFiltros()">
                    <i class="fas fa-sync-alt"></i> Limpiar filtros
                </button>
            </div>
        </div>
    </div>
    
    <!-- Tabla de registros -->
    <div class="tabla-contenedor">
        <table class="tabla-historial" id="tablaHistorial">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>FECHA</th>
                    <th>SECTOR</th>
                    <th>TOTAL CAJAS</th>
                    <th>EXPORTACIÓN</th>
                    <th>NACIONAL</th>
                    <th>DESECHO</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody id="cuerpoTabla">
                <!-- Se llenará dinámicamente con JavaScript -->
            </tbody>
        </table>
    </div>
    
    <!-- Paginación -->
    <div class="paginacion">
        <div class="navegacion-paginas">
            <button class="btn-pagina activa">1</button>
        </div>
    </div>
</div>
    
<!-- Modal de detalles -->
<div class="modal-overlay" id="modalDetalles">
    <div class="modal-contenido">
        <div class="modal-cabecera">
            <h3 class="modal-titulo">Detalles de Producción</h3>
            <div class="acciones-modal">
                <button class="btn-modal-pdf" onclick="generarPDF()">
                    <i class="fas fa-file-pdf"></i> Descargar PDF
                </button>
                <button class="modal-cerrar" onclick="cerrarModal()"><i class="fas fa-times"></i></button>
            </div>
        </div>
        <div class="modal-cuerpo">
            <!-- Ficha de detalle -->
            <div style="margin-bottom: 20px; display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                <div style="background-color: #f9f9f9; padding: 15px; border-radius: 8px;">
                    <div style="font-weight: 600; color: var(--color-primario); margin-bottom: 5px; font-size: 13px;">ID</div>
                    <div style="font-size: 16px;" id="detalle-id">PROD-001</div>
                </div>
                <div style="background-color: #f9f9f9; padding: 15px; border-radius: 8px;">
                    <div style="font-weight: 600; color: var(--color-primario); margin-bottom: 5px; font-size: 13px;">Fecha</div>
                    <div style="font-size: 16px;" id="detalle-fecha">16/03/2025</div>
                </div>
                <div style="background-color: #f9f9f9; padding: 15px; border-radius: 8px;">
                    <div style="font-weight: 600; color: var(--color-primario); margin-bottom: 5px; font-size: 13px;">Sector</div>
                    <div style="font-size: 16px;" id="detalle-sector">Sector A</div>
                </div>
                <div style="background-color: #f9f9f9; padding: 15px; border-radius: 8px;">
                    <div style="font-weight: 600; color: var(--color-primario); margin-bottom: 5px; font-size: 13px;">Total Cajas</div>
                    <div style="font-size: 16px;" id="detalle-total">150</div>
                </div>
            </div>
            
            <div style="margin-bottom: 25px;">
                <div style="margin-bottom: 15px; font-weight: 600; color: var(--color-primario); display: flex; align-items: center;">
                    <i class="fas fa-chart-pie" style="margin-right: 8px;"></i> Distribución por Calidad
                </div>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
                    <div style="background-color: rgba(46, 204, 113, 0.1); padding: 15px; border-radius: 8px; border-left: 3px solid #2ecc71;">
                        <div style="font-weight: 600; color: #27ae60; margin-bottom: 5px; font-size: 13px;">Exportación</div>
                        <div style="font-size: 22px; font-weight: 600;" id="detalle-exp">120</div>
                        <div style="font-size: 13px; color: #555;" id="detalle-exp-porc">80%</div>
                    </div>
                    <div style="background-color: rgba(52, 152, 219, 0.1); padding: 15px; border-radius: 8px; border-left: 3px solid #3498db;">
                        <div style="font-weight: 600; color: #2980b9; margin-bottom: 5px; font-size: 13px;">Nacional</div>
                        <div style="font-size: 22px; font-weight: 600;" id="detalle-nac">25</div>
                        <div style="font-size: 13px; color: #555;" id="detalle-nac-porc">16.7%</div>
                    </div>
                    <div style="background-color: rgba(231, 76, 60, 0.1); padding: 15px; border-radius: 8px; border-left: 3px solid #e74c3c;">
                        <div style="font-weight: 600; color: #c0392b; margin-bottom: 5px; font-size: 13px;">Desecho</div>
                        <div style="font-size: 22px; font-weight: 600;" id="detalle-des">5</div>
                        <div style="font-size: 13px; color: #555;" id="detalle-des-porc">3.3%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts para la generación de PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    
<!-- Script para la funcionalidad de la página -->
<script>
    // Array de sectores con la nueva estructura
    const sectores = [
        { value: 'sector_a', label: 'Sector A' },
        { value: 'sector_ak', label: 'Sector Ak' },
        { value: 'sector_b', label: 'Sector B' },
        { value: 'sector_c', label: 'Sector C' },
        { value: 'sector_d', label: 'Sector D' }
    ];
    
    // Datos de producción completos
    const datosProduccion = <?php echo $produccionesJSON; ?>;
    
    // Datos filtrados (inicialmente todos)
    let datosFiltrados = [...datosProduccion];
    
    // Función para cargar dropdown de sectores
    function cargarSectores() {
        const selectSector = document.getElementById('filtroSector');
        
        // Limpiar opciones existentes excepto la primera
        while (selectSector.options.length > 1) {
            selectSector.remove(1);
        }
        
        // Agregar opciones de sectores usando la nueva estructura
        sectores.forEach(sector => {
            const option = document.createElement('option');
            option.value = sector.value;
            option.textContent = sector.label;
            selectSector.appendChild(option);
        });
    }
    
    // Función para cargar la tabla con datos
    function cargarTabla(datos) {
        const cuerpoTabla = document.getElementById('cuerpoTabla');
        cuerpoTabla.innerHTML = '';
        
        datos.forEach(item => {
            // Calcular el total sumando los tres tipos
            const total = (parseInt(item.exportacion) || 0) + 
                         (parseInt(item.nacional) || 0) + 
                         (parseInt(item.desecho) || 0);
            
            // Guardar el total calculado en el objeto
            item.total = total;
            
            const fila = document.createElement('tr');
            
            // Hacer toda la fila clickeable
            fila.style.cursor = 'pointer';
            fila.onclick = function(event) {
                // Evitar que el clic se propague si estamos haciendo clic en un botón
                if (event.target.tagName === 'BUTTON' || event.target.closest('button')) {
                    return;
                }
                verDetalles(item.id);
            };
            
            fila.innerHTML = `
                <td>${item.id2 || item.id}</td>
                <td>${item.fecha}</td>
                <td>${item.sector}</td>
                <td>${total}</td>
                <td><span class="indicador-cantidad indicador-exportacion">${item.exportacion}</span></td>
                <td><span class="indicador-cantidad indicador-nacional">${item.nacional}</span></td>
                <td><span class="indicador-cantidad indicador-desecho">${item.desecho}</span></td>
                <td>
                    <div class="acciones-fila">
                        <button class="btn-accion btn-ver" onclick="verDetalles('${item.id}')"><i class="fas fa-eye"></i></button>
                        <button class="btn-accion btn-editar"><i class="fas fa-edit"></i></button>
                        <button class="btn-accion btn-eliminar"><i class="fas fa-trash"></i></button>
                    </div>
                </td>
            `;
            
            cuerpoTabla.appendChild(fila);
        });
        
        // Actualizar estadísticas
        actualizarEstadisticas(datos);
    }
    
    // Función para actualizar estadísticas
    function actualizarEstadisticas(datos) {
        // Calcular totales
        const totalCajas = datos.reduce((suma, item) => suma + item.total, 0);
        const totalExportacion = datos.reduce((suma, item) => suma + (parseInt(item.exportacion) || 0), 0);
        const totalNacional = datos.reduce((suma, item) => suma + (parseInt(item.nacional) || 0), 0);
        const totalDesecho = datos.reduce((suma, item) => suma + (parseInt(item.desecho) || 0), 0);
        
        // Actualizar valores en contador de resultados
        let contadorElement = document.getElementById('contador-resultados');
        if (contadorElement) {
            const totalInfo = `<div style="margin-top: 5px; color: var(--color-secundario);"><i class="fas fa-boxes"></i> Total: ${totalCajas} cajas (${totalExportacion} exp, ${totalNacional} nac, ${totalDesecho} des)</div>`;
            contadorElement.innerHTML = `<i class="fas fa-table"></i> Mostrando ${datos.length} registros ${totalInfo}`;
        }
    }
    
    // Función para convertir string a fecha
    function convertirStringAFecha(fechaStr) {
        // Si es formato YYYY-MM-DD (desde input date)
        if (fechaStr.includes('-')) {
            const [anio, mes, dia] = fechaStr.split('-');
            return new Date(parseInt(anio), parseInt(mes) - 1, parseInt(dia));
        } 
        // Si es formato DD/MM/YYYY (desde nuestros datos)
        else if (fechaStr.includes('/')) {
            const [dia, mes, anio] = fechaStr.split('/');
            return new Date(parseInt(anio), parseInt(mes) - 1, parseInt(dia));
        }
        return null;
    }
    
    // Función para aplicar filtros
    // Función para aplicar filtros
    function aplicarFiltros() {
        const fechaInicio = document.getElementById('fechaInicio').value;
        const fechaFin = document.getElementById('fechaFin').value;
        const sectorSeleccionado = document.getElementById('filtroSector').value;
        
        // Convertir fechas para comparar usando nuestra función personalizada
        const fechaInicioObj = fechaInicio ? convertirStringAFecha(fechaInicio) : new Date(2000, 0, 1);
        const fechaFinObj = fechaFin ? convertirStringAFecha(fechaFin) : new Date(2099, 11, 31);
        
        // Asegurarse de que fechaFinObj incluya el final del día
        fechaFinObj.setHours(23, 59, 59, 999);
        
        // Aplicar filtros
        datosFiltrados = datosProduccion.filter(item => {
            // Convertir fecha del item usando nuestra función personalizada
            const fechaItem = convertirStringAFecha(item.fecha);
            
            // Verificar si cumple con los filtros de fecha
            const cumpleFechas = fechaItem >= fechaInicioObj && fechaItem <= fechaFinObj;
            
            // Para el sector, debemos modificar la lógica según la nueva estructura
            let cumpleSector = true;
            if (sectorSeleccionado) {
                // Buscar el sector seleccionado por value y comparar con label
                const sectorObj = sectores.find(s => s.value === sectorSeleccionado);
                if (sectorObj) {
                    cumpleSector = item.sector === sectorObj.label;
                }
            }
            
            // Debe cumplir todos los filtros
            return cumpleFechas && cumpleSector;
        });
        
        // Actualizar tabla con datos filtrados
        cargarTabla(datosFiltrados);
        
        // Actualizar contador de resultados
        actualizarContadorResultados(datosFiltrados.length);
    }
    
    // Función para actualizar el contador de resultados
    function actualizarContadorResultados(cantidad) {
        // Buscar o crear el elemento del contador
        let contador = document.getElementById('contador-resultados');
        if (!contador) {
            contador = document.createElement('div');
            contador.id = 'contador-resultados';
            contador.className = 'contador-resultados';
            contador.style.textAlign = 'right';
            contador.style.marginBottom = '10px';
            contador.style.fontSize = '14px';
            contador.style.color = 'var(--color-primario)';
            contador.style.fontWeight = '500';
            
            // Insertar antes de la tabla
            const tablaContenedor = document.querySelector('.tabla-contenedor');
            tablaContenedor.parentNode.insertBefore(contador, tablaContenedor);
        }
        
        // Actualizar el texto del contador (el contenido completo se actualizará en actualizarEstadisticas)
        contador.innerHTML = `<i class="fas fa-table"></i> Mostrando ${cantidad} registros`;
        
        // Actualizar estadísticas
        actualizarEstadisticas(datosFiltrados);
    }
    
    // Función para limpiar filtros
    function limpiarFiltros() {
        document.getElementById('fechaInicio').value = '2025-02-15';
        document.getElementById('fechaFin').value = '2025-03-16';
        document.getElementById('filtroSector').value = '';
        
        // Restaurar todos los datos
        datosFiltrados = [...datosProduccion];
        cargarTabla(datosFiltrados);
        
        // Actualizar contador de resultados
        actualizarContadorResultados(datosFiltrados.length);
    }
    
    // Función para abrir el modal con detalles
    function verDetalles(id) {
        const datos = datosProduccion.find(item => item.id == id);
        if (!datos) return;
        
        // Recalcular el total para asegurarnos de tener el valor correcto
        const total = (parseInt(datos.exportacion) || 0) + 
                     (parseInt(datos.nacional) || 0) + 
                     (parseInt(datos.desecho) || 0);
        
        // Guardar el total calculado
        datos.total = total;
        
        // Llenar datos básicos
        document.getElementById('detalle-id').textContent = datos.id2 || datos.id;
        document.getElementById('detalle-fecha').textContent = datos.fecha;
        document.getElementById('detalle-sector').textContent = datos.sector;
        document.getElementById('detalle-total').textContent = total;
        
        // Llenar datos de calidad
        document.getElementById('detalle-exp').textContent = datos.exportacion;
        document.getElementById('detalle-nac').textContent = datos.nacional;
        document.getElementById('detalle-des').textContent = datos.desecho;
        
        // Calcular porcentajes
        const porcExportacion = ((datos.exportacion / total) * 100).toFixed(1);
        const porcNacional = ((datos.nacional / total) * 100).toFixed(1);
        const porcDesecho = ((datos.desecho / total) * 100).toFixed(1);
        
        document.getElementById('detalle-exp-porc').textContent = porcExportacion + '%';
        document.getElementById('detalle-nac-porc').textContent = porcNacional + '%';
        document.getElementById('detalle-des-porc').textContent = porcDesecho + '%';
        
        // Mostrar el modal
        document.getElementById('modalDetalles').classList.add('activo');
    }
    
    // Función para cerrar el modal
    function cerrarModal() {
        document.getElementById('modalDetalles').classList.remove('activo');
    }
    
    // Función para generar el PDF
    function generarPDF() {
        // Obtener los datos del registro actual
        const id = document.getElementById('detalle-id').textContent;
        const fecha = document.getElementById('detalle-fecha').textContent;
        const sector = document.getElementById('detalle-sector').textContent;
        const total = document.getElementById('detalle-total').textContent;
        const exportacion = document.getElementById('detalle-exp').textContent;
        const nacional = document.getElementById('detalle-nac').textContent;
        const desecho = document.getElementById('detalle-des').textContent;
        const porcExportacion = document.getElementById('detalle-exp-porc').textContent;
        const porcNacional = document.getElementById('detalle-nac-porc').textContent;
        const porcDesecho = document.getElementById('detalle-des-porc').textContent;
        
        // Crear instancia de jsPDF
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        
        // Configurar fuentes y estilos
        doc.setFont("helvetica", "bold");
        doc.setFontSize(20);
        doc.setTextColor(74, 35, 90); // Color zarzamora oscuro
        
        // Encabezado con logo (se simula un logo con texto)
        doc.text("ZARZABERRY", 105, 20, { align: "center" });
        
        doc.setFontSize(14);
        doc.text("Reporte de Producción", 105, 30, { align: "center" });
        
        // Línea divisoria
        doc.setDrawColor(74, 35, 90);
        doc.setLineWidth(0.5);
        doc.line(20, 35, 190, 35);
        
        // Información general
        doc.setFontSize(12);
        doc.setFont("helvetica", "bold");
        doc.text("INFORMACIÓN GENERAL", 20, 45);
        
        doc.setFont("helvetica", "normal");
        doc.setFontSize(10);
        doc.text("ID de Producción:", 20, 55);
        doc.text(id, 70, 55);
        
        doc.text("Fecha:", 20, 65);
        doc.text(fecha, 70, 65);
        
        doc.text("Sector:", 20, 75);
        doc.text(sector, 70, 75);
        
        doc.text("Total de Cajas:", 20, 85);
        doc.text(total, 70, 85);
        
        // Distribución
        doc.setFont("helvetica", "bold");
        doc.setFontSize(12);
        doc.text("DISTRIBUCIÓN POR CALIDAD", 20, 100);
        
        // Tabla de distribución
        doc.setDrawColor(0);
        doc.setFillColor(245, 245, 245);
        doc.rect(20, 105, 170, 10, 'F');
        
        doc.setFont("helvetica", "bold");
        doc.setFontSize(10);
        doc.text("Tipo", 25, 112);
        doc.text("Cantidad", 95, 112);
        doc.text("Porcentaje", 145, 112);
        
        // Filas de datos
        doc.setFont("helvetica", "normal");
        
        // Exportación
        doc.setDrawColor(46, 204, 113);
        doc.setFillColor(240, 250, 240);
        doc.rect(20, 115, 170, 10, 'F');
        doc.setDrawColor(46, 204, 113);
        doc.setLineWidth(1);
        doc.line(20, 115, 20, 125);
        doc.text("Exportación", 25, 122);
        doc.text(exportacion, 95, 122);
        doc.text(porcExportacion, 145, 122);
        
        // Nacional
        doc.setDrawColor(52, 152, 219);
        doc.setFillColor(240, 245, 250);
        doc.rect(20, 125, 170, 10, 'F');
        doc.setDrawColor(52, 152, 219);
        doc.line(20, 125, 20, 135);
        doc.text("Nacional", 25, 132);
        doc.text(nacional, 95, 132);
        doc.text(porcNacional, 145, 132);
        
        // Desecho
        doc.setDrawColor(231, 76, 60);
        doc.setFillColor(250, 240, 240);
        doc.rect(20, 135, 170, 10, 'F');
        doc.setDrawColor(231, 76, 60);
        doc.line(20, 135, 20, 145);
        doc.text("Desecho", 25, 142);
        doc.text(desecho, 95, 142);
        doc.text(porcDesecho, 145, 142);
        
        // Gráfico (simulado con un rectángulo)
        doc.setFont("helvetica", "bold");
        doc.setFontSize(12);
        doc.text("REPRESENTACIÓN GRÁFICA", 20, 160);
        
        // Crear barras para representar proporciones
        const baseY = 170;
        const ancho = 150;
        
        // Convertir porcentajes a números
        const porcExpNum = parseFloat(porcExportacion);
        const porcNacNum = parseFloat(porcNacional);
        const porcDesNum = parseFloat(porcDesecho);
        
        // Exportación (verde)
        doc.setFillColor(46, 204, 113);
        const anchoExp = (porcExpNum / 100) * ancho;
        doc.rect(20, baseY, anchoExp, 20, 'F');
        
        // Nacional (azul)
        doc.setFillColor(52, 152, 219);
        const anchoNac = (porcNacNum / 100) * ancho;
        doc.rect(20 + anchoExp, baseY, anchoNac, 20, 'F');
        
        // Desecho (rojo)
        doc.setFillColor(231, 76, 60);
        const anchoDes = (porcDesNum / 100) * ancho;
        doc.rect(20 + anchoExp + anchoNac, baseY, anchoDes, 20, 'F');
        
        // Leyenda
        doc.setFont("helvetica", "normal");
        doc.setFontSize(9);
        doc.setFillColor(46, 204, 113);
        doc.rect(20, 200, 10, 10, 'F');
        doc.text("Exportación", 35, 207);
        
        doc.setFillColor(52, 152, 219);
        doc.rect(80, 200, 10, 10, 'F');
        doc.text("Nacional", 95, 207);
        
        doc.setFillColor(231, 76, 60);
        doc.rect(140, 200, 10, 10, 'F');
        doc.text("Desecho", 155, 207);
        
        // Pie de página
        doc.setFont("helvetica", "italic");
        doc.setFontSize(8);
        doc.setTextColor(100, 100, 100);
        doc.text("Generado el " + new Date().toLocaleDateString(), 20, 270);
        doc.text("ZarzaBerry - Sistema de Gestión de Producción", 105, 270, { align: "center" });
        doc.text("Página 1 de 1", 190, 270, { align: "right" });
        
        // Guardar el PDF
        doc.save(`Reporte_Produccion_${id}.pdf`);
    }
    
    // Evento para cerrar modal con Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            cerrarModal();
        }
    });
    
    // Cerrar modal al hacer clic fuera
    document.getElementById('modalDetalles').addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModal();
        }
    });
    
    // Inicializar al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        cargarSectores();
        cargarTabla(datosProduccion);
        actualizarContadorResultados(datosFiltrados.length);
    });
</script>
</body>
</html>