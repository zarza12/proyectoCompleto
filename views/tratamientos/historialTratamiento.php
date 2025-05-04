<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once '../../controllers/daoTratamiento.php';
include_once  '../../models/Tratamiento.php';

$daoTratamiento = new daoTratamiento();
$listar = $daoTratamiento->listarTratamientos();
$listarJSON = json_encode($listar);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Tratamientos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Añadido jsPDF para generar PDFs -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
    <style>
        /* Estilo para el título central */
        .titulo-pagina {
            text-align: center;
            margin: 20px 0;
        }
        
        .titulo-pagina h1 {
            color: #6c216c; /* Color zarzamora */
            font-size: 28px;
            margin: 0;
            padding: 10px;
            border-bottom: 2px solid #d8c4e2;
            display: inline-block;
        }
        
        /* Estilos para filtros en una línea tipo menú */
        .menu-filtros {
            display: flex;
            align-items: center;
            background-color: #f5f5f5;
            padding: 10px 15px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .etiqueta-filtro {
            font-weight: bold;
            margin-right: 15px;
            color: #444;
        }
        
        .input-filtro, .select-filtro {
            margin: 0 8px;
            padding: 6px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .btn-limpiar-filtro {
            margin-left: auto;
            background-color: #f8f8f8;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 6px 12px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .btn-limpiar-filtro:hover {
            background-color: #e9e9e9;
        }

        /* Estilos para el modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
            animation: fadeIn 0.3s;
        }

        @keyframes fadeIn {
            from {opacity: 0}
            to {opacity: 1}
        }

        .modal-contenido {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 70%;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            position: relative;
            animation: slideDown 0.4s;
        }

        @keyframes slideDown {
            from {transform: translateY(-50px); opacity: 0;}
            to {transform: translateY(0); opacity: 1;}
        }

        .cerrar-modal {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .cerrar-modal:hover,
        .cerrar-modal:focus {
            color: black;
            text-decoration: none;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .modal-titulo {
            font-size: 1.5em;
            color: #6c216c; /* Color zarzamora */
            margin: 0;
        }

        .info-seccion {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8f5fa; /* Tono claro de zarzamora */
            border-radius: 5px;
        }

        .info-seccion h3 {
            margin-top: 0;
            color: #6c216c; /* Color zarzamora */
            font-size: 1.2em;
            border-bottom: 1px solid #d8c4e2;
            padding-bottom: 5px;
        }

        .info-seccion p {
            margin: 5px 0;
        }

        .lista-fumigantes {
            list-style-type: none;
            padding: 0;
        }

        .lista-fumigantes li {
            padding: 8px;
            margin: 5px 0;
            background-color: #f0e6f5;
            border-radius: 5px;
            border-left: 4px solid #6c216c;
        }

        .btn-descargar {
            background-color: #6c216c; /* Color zarzamora */
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: background-color 0.3s;
        }

        .btn-descargar:hover {
            background-color: #8a418a; /* Color zarzamora más claro */
        }

        /* Hacer que las filas de la tabla sean clickeables */
        #cuerpoTabla tr {
            cursor: pointer;
            transition: background-color 0.2s;
        }

        #cuerpoTabla tr:hover {
            background-color: #f0e6f5; /* Tono más claro de zarzamora */
        }

        /* Estilo para colores de zarzamora */
        .zarzamora-primary {
            color: #6c216c;
        }

        .zarzamora-bg {
            background-color: #6c216c;
            color: white;
        }

        .zarzamora-light-bg {
            background-color: #f0e6f5;
        }
    </style>
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
            --color-warning: #F39C12;
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
        
        /* Tarjetas de estadísticas */
        .tarjetas-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .tarjeta {
            background-color: var(--color-blanco);
            border-radius: var(--borde-radio);
            padding: 20px;
            box-shadow: var(--sombra-suave);
            position: relative;
            transition: var(--transicion);
            display: flex;
            flex-direction: column;
            height: 130px;
            overflow: hidden;
        }
        
        .tarjeta:hover {
            transform: translateY(-3px);
            box-shadow: var(--sombra-media);
        }
        
        .tarjeta-total {
            border-left: 4px solid var(--color-primario);
        }
        
        .tarjeta-herbicida {
            border-left: 4px solid #2ECC71;
        }
        
        .tarjeta-insecticida {
            border-left: 4px solid #3498DB;
        }
        
        .tarjeta-fungicida {
            border-left: 4px solid #F39C12;
        }
        
        .tarjeta-valor {
            font-size: 32px;
            font-weight: 700;
            color: var(--color-primario);
            margin-bottom: 8px;
        }
        
        .tarjeta-titulo {
            font-size: 14px;
            color: #777;
            font-weight: 500;
        }
        
        .icono-fondo {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 70px;
            opacity: 0.08;
            color: var(--color-primario);
        }
        
        /* Sección de filtros */
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
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .filtros-compactos .campo-filtro {
            flex: 1;
            min-width: 200px;
        }
        
        .input-filtro,
        .select-filtro {
            width: 100%;
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
        
        .filtros-compactos .btn-aplicar-filtro {
            background-color: var(--color-primario);
            color: white;
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-left: auto;
        }
        
        .filtros-compactos .btn-limpiar-filtro {
            background-color: #f1f1f1;
            color: var(--color-texto);
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
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
        
        .tabla-tratamientos {
            width: 100%;
            border-collapse: collapse;
        }
        
        .tabla-tratamientos th {
            background-color: var(--color-primario);
            color: white;
            font-weight: 600;
            padding: 12px 15px;
            text-align: left;
            font-size: 14px;
        }
        
        .tabla-tratamientos td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--color-borde);
        }
        
        .tabla-tratamientos tr:hover {
            background-color: rgba(210, 180, 222, 0.1);
        }
        
        .tabla-tratamientos tr:last-child td {
            border-bottom: none;
        }
        
        /* Indicadores */
        .indicador-frecuencia {
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
        
        .frecuencia-semanal {
            background-color: rgba(46, 204, 113, 0.2);
            color: #27ae60;
        }
        
        .frecuencia-quincenal {
            background-color: rgba(52, 152, 219, 0.2);
            color: #2980b9;
        }
        
        .frecuencia-mensual {
            background-color: rgba(243, 156, 18, 0.2);
            color: #d35400;
        }
        
        /* Chips para fumigantes */
        .fumigante-chip {
            display: inline-flex;
            align-items: center;
            background-color: var(--color-resalte);
            color: var(--color-primario);
            border-radius: 50px;
            padding: 5px 10px;
            margin: 2px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .fumigante-chip.herbicida {
            background-color: rgba(46, 204, 113, 0.2);
            color: #27ae60;
        }
        
        .fumigante-chip.insecticida {
            background-color: rgba(52, 152, 219, 0.2);
            color: #2980b9;
        }
        
        .fumigante-chip.fungicida {
            background-color: rgba(243, 156, 18, 0.2);
            color: #d35400;
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
        
        /* Responsividad */
        @media (max-width: 1200px) {
            .tarjetas-stats {
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
            
            .tarjetas-stats {
                grid-template-columns: 1fr;
            }
            
            .filtros-compactos {
                flex-direction: column;
            }
            
            .filtros-compactos .btn-aplicar-filtro,
            .filtros-compactos .btn-limpiar-filtro {
                width: 100%;
                justify-content: center;
            }
            
            .paginacion {
                flex-direction: column;
                gap: 15px;
                align-items: flex-end;
            }
        }
</style>
</head>
<body>
<?php include '../../views/menuA.php'; ?>

<!-- Título central -->
<div class="titulo-pagina">
    <h1>Historial Tratamiento</h1>
</div>

<div class="contenido-principal">

    <!-- Sección de filtros (en una línea) -->
    <div class="seccion-filtros">
        <div class="menu-filtros">
            <span class="etiqueta-filtro"><i class="fas fa-filter"></i> Filtros:</span>
            <input type="date" class="input-filtro" id="fechaInicio" onchange="aplicarFiltros()">
            <input type="date" class="input-filtro" id="fechaFin" onchange="aplicarFiltros()">
            
            <select class="select-filtro" id="filtroSector" onchange="aplicarFiltros()">
                <option value="">Todos los sectores</option>
                <option value="sector1">Sector 1</option>
                <option value="sector2">Sector 2</option>
                <option value="sector3">Sector 3</option>
                <option value="sector4">Sector 4</option>
            </select>
            
            <button class="btn-limpiar-filtro" onclick="limpiarFiltros()">
                <i class="fas fa-sync-alt"></i> Limpiar
            </button>
        </div>
    </div>
    
    <!-- Tabla de registros -->
    <div class="tabla-contenedor">
        <table class="tabla-tratamientos" id="tablaTratamientos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>FECHA</th>
                    <th>SECTOR</th>
                    <th>FUMIGANTES</th>
                    <th>FRECUENCIA</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody id="cuerpoTabla">
                <!-- La tabla se llenará mediante JavaScript -->
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

    <!-- Modal de detalles -->
    <div id="modalDetalles" class="modal">
        <div class="modal-contenido">
            <div class="modal-header">
                <h2 class="modal-titulo" id="modalTitulo">Detalles del Tratamiento</h2>
                <div>
                    <button class="btn-descargar" onclick="generarPDF()">
                        <i class="fas fa-file-pdf"></i> Descargar PDF
                    </button>
                    <span class="cerrar-modal">&times;</span>
                </div>
            </div>
            <div class="info-seccion">
                <h3><i class="fas fa-info-circle"></i> Información General</h3>
                <p><strong>ID:</strong> <span id="modalId"></span></p>
                <p><strong>Fecha:</strong> <span id="modalFecha"></span></p>
                <p><strong>Sector:</strong> <span id="modalSector"></span></p>
                <p><strong>Frecuencia:</strong> <span id="modalFrecuencia"></span></p>
            </div>
            <div class="info-seccion">
                <h3><i class="fas fa-flask"></i> Fumigantes Aplicados</h3>
                <ul class="lista-fumigantes" id="modalFumigantes">
                    <!-- Se llenará con JavaScript -->
                </ul>
            </div>
            <div class="info-seccion">
                <h3><i class="fas fa-clipboard-list"></i> Observaciones</h3>
                <p id="modalObservaciones"></p>
            </div>
        </div>
    </div>
</div>

<script>
    // Datos de tratamientos actualizados con la estructura requerida
    const datosTratamientos = <?php echo $listarJSON ; ?>
    
    /*{
        'REC-001': {
            id: 'REC-001',
            sector: 'sector1',
            fecha: '2025-04-24',
            frecuencia: 'semanal',
            observaciones: 'Aplicar preferentemente en las primeras horas de la mañana.',
            fumigantes: [
                { nombre: 'Compuesto XYZ', cantidad: '2', unidad: 'litros', tipo: 'herbicida' },
                { nombre: 'Nutriente AB', cantidad: '500', unidad: 'gramos', tipo: 'herbicida' }
            ]
        },
        'REC-002': {
            id: 'REC-002',
            sector: 'sector2',
            fecha: '2025-04-22',
            frecuencia: 'quincenal',
            observaciones: 'Mantener alejado de cultivos sensibles.',
            fumigantes: [
                { nombre: 'Imidacloprid', cantidad: '1.5', unidad: 'litros', tipo: 'insecticida' },
                { nombre: 'Clorpirifos', cantidad: '300', unidad: 'gramos', tipo: 'insecticida' }
            ]
        },
        'REC-003': {
            id: 'REC-003',
            sector: 'sector3',
            fecha: '2025-04-20',
            frecuencia: 'mensual',
            observaciones: 'Aplicar con humedad moderada en el suelo.',
            fumigantes: [
                { nombre: 'Azoxistrobina', cantidad: '3', unidad: 'litros', tipo: 'fungicida' },
                { nombre: 'Tebuconazol', cantidad: '250', unidad: 'gramos', tipo: 'fungicida' }
            ]
        },
        'REC-004': {
            id: 'REC-004',
            sector: 'sector4',
            fecha: '2025-04-18',
            frecuencia: 'semanal',
            observaciones: 'No aplicar en condiciones de viento.',
            fumigantes: [
                { nombre: 'Deltametrina', cantidad: '2.5', unidad: 'litros', tipo: 'insecticida' },
                { nombre: 'Lambda-cihalotrina', cantidad: '400', unidad: 'gramos', tipo: 'insecticida' }
            ]
        },
        'REC-005': {
            id: 'REC-005',
            sector: 'sector1',
            fecha: '2025-04-16',
            frecuencia: 'quincenal',
            observaciones: 'Rotar con otros compuestos para evitar resistencia.',
            fumigantes: [
                { nombre: 'Atrazina', cantidad: '3', unidad: 'litros', tipo: 'herbicida' },
                { nombre: 'Pendimetalina', cantidad: '350', unidad: 'gramos', tipo: 'herbicida' }
            ]
        },
        'REC-006': {
            id: 'REC-006',
            sector: 'sector2',
            fecha: '2025-04-14',
            frecuencia: 'mensual',
            observaciones: 'Aplicar después del riego para mayor efectividad.',
            fumigantes: [
                { nombre: 'Mancozeb', cantidad: '2', unidad: 'litros', tipo: 'fungicida' },
                { nombre: 'Metalaxil', cantidad: '450', unidad: 'gramos', tipo: 'fungicida' }
            ]
        }
    };*/
    
    // Función para llenar la tabla con los datos
    function llenarTabla() {
        // Simplemente usamos la función de aplicarFiltros, que actualizará la tabla con todos los datos
        // si no hay filtros aplicados
        aplicarFiltros();
    }
    
    // Función para manejar el modal
    const modal = document.getElementById("modalDetalles");
    const span = document.getElementsByClassName("cerrar-modal")[0];
    
    // Función para mostrar el modal con los detalles del tratamiento
    function mostrarModal(id) {
        const tratamiento = datosTratamientos[id];
        if (!tratamiento) return;
        
        // Llenar los datos en el modal
        document.getElementById("modalId").textContent = tratamiento.id;
        document.getElementById("modalTitulo").textContent = `Detalles del Tratamiento ${tratamiento.id}`;
        
        // Convertir formato de fecha si es necesario
        let fecha = tratamiento.fecha || "";
        if (fecha && fecha.includes('-')) {
            const partesFecha = fecha.split('-');
            fecha = partesFecha[2] + '/' + partesFecha[1] + '/' + partesFecha[0];
        }
        document.getElementById("modalFecha").textContent = fecha;
        
        document.getElementById("modalSector").textContent = tratamiento.sector || "";
        document.getElementById("modalFrecuencia").textContent = tratamiento.frecuencia ? 
            (tratamiento.frecuencia.charAt(0).toUpperCase() + tratamiento.frecuencia.slice(1)) : "";
        document.getElementById("modalObservaciones").textContent = tratamiento.observaciones || "";
        
        // Llenar lista de fumigantes
        const listaFumigantes = document.getElementById("modalFumigantes");
        listaFumigantes.innerHTML = "";
        
        if (tratamiento.fumigantes && tratamiento.fumigantes.length > 0) {
            tratamiento.fumigantes.forEach(f => {
                const li = document.createElement("li");
                li.innerHTML = `<strong>${f.nombre}:</strong> ${f.cantidad} ${f.unidad}`;
                listaFumigantes.appendChild(li);
            });
        } else {
            const li = document.createElement("li");
            li.textContent = "No hay fumigantes registrados";
            listaFumigantes.appendChild(li);
        }
        
        // Mostrar el modal
        modal.style.display = "block";
    }
    
    // Cerrar el modal cuando se hace clic en la X
    span.onclick = function() {
        modal.style.display = "none";
    }
    
    // Cerrar el modal cuando se hace clic fuera de él
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
    
    // Función para limpiar filtros
    function limpiarFiltros() {
        // Establecer las fechas de inicio y fin al mes actual
        document.getElementById('fechaInicio').value = obtenerPrimerDiaMes();
        document.getElementById('fechaFin').value = obtenerUltimoDiaMes();
        document.getElementById('filtroSector').value = '';
        
        // Aplicar filtros automáticamente después de limpiar
        aplicarFiltros();
    }
    
    // Función para aplicar filtros (actualización automática)
    function aplicarFiltros() {
        const fechaInicio = document.getElementById('fechaInicio').value;
        const fechaFin = document.getElementById('fechaFin').value;
        const sectorSeleccionado = document.getElementById('filtroSector').value;
        
        // Filtrar los registros según los criterios seleccionados
        const registrosFiltrados = {};
        
        Object.entries(datosTratamientos).forEach(([id, tratamiento]) => {
            // Verificar si hay fecha antes de intentar convertir
            if (tratamiento.fecha) {
                // Convertir fechas para comparación
                const fechaTratamiento = new Date(tratamiento.fecha.split('/').reverse().join('-'));
                const fechaInicioObj = fechaInicio ? new Date(fechaInicio) : null;
                const fechaFinObj = fechaFin ? new Date(fechaFin) : null;
                
                // Comprobar si el tratamiento cumple con los filtros
                const cumpleFecha = (!fechaInicioObj || fechaTratamiento >= fechaInicioObj) && 
                                   (!fechaFinObj || fechaTratamiento <= fechaFinObj);
                
                const cumpleSector = !sectorSeleccionado || tratamiento.sector === sectorSeleccionado;
                
                // Si cumple con todos los filtros, añadirlo a los resultados
                if (cumpleFecha && cumpleSector) {
                    registrosFiltrados[id] = tratamiento;
                }
            } else {
                // Si no hay fecha pero cumple con el filtro de sector, añadirlo
                const cumpleSector = !sectorSeleccionado || tratamiento.sector === sectorSeleccionado;
                if (cumpleSector) {
                    registrosFiltrados[id] = tratamiento;
                }
            }
        });
        
        // Actualizar la tabla con los registros filtrados
        actualizarTablaConFiltros(registrosFiltrados);
    }
    
    // Función para actualizar la tabla con los registros filtrados
    function actualizarTablaConFiltros(registrosFiltrados) {
        const cuerpoTabla = document.getElementById('cuerpoTabla');
        cuerpoTabla.innerHTML = '';
        
        if (Object.keys(registrosFiltrados).length === 0) {
            // Si no hay resultados, mostrar mensaje
            const fila = document.createElement('tr');
            fila.innerHTML = '<td colspan="6" style="text-align: center;">No se encontraron registros con los filtros seleccionados</td>';
            cuerpoTabla.appendChild(fila);
            return;
        }
        
        // Llenar la tabla con los registros filtrados
        Object.values(registrosFiltrados).forEach(tratamiento => {
            // Convertir formato de fecha si es necesario y verificar si existe
            let fecha = tratamiento.fecha || "";
            if (fecha && fecha.includes('-')) {
                const partesFecha = fecha.split('-');
                fecha = partesFecha[2] + '/' + partesFecha[1] + '/' + partesFecha[0];
            }
            
            // Crear la fila
            const fila = document.createElement('tr');
            fila.onclick = function() { mostrarModal(tratamiento.id); };
            
            // Clases de frecuencia
            let claseFrec = 'frecuencia-semanal';
            if (tratamiento.frecuencia) {
                if (tratamiento.frecuencia.toLowerCase() === 'quincenal') {
                    claseFrec = 'frecuencia-quincenal';
                } else if (tratamiento.frecuencia.toLowerCase() === 'mensual') {
                    claseFrec = 'frecuencia-mensual';
                }
            }
            
            // Crear las celdas (modificando para mostrar solo el nombre del fumigante sin el tipo)
            fila.innerHTML = `
                <td>${tratamiento.id}</td>
                <td>${fecha}</td>
                <td>${tratamiento.sector || ""}</td>
                <td>
                    ${tratamiento.fumigantes ? tratamiento.fumigantes.map(f => 
                        `<span class="fumigante-chip ${f.tipo}">${f.nombre}</span>`
                    ).join('') : ""}
                </td>
                <td><span class="indicador-frecuencia ${claseFrec}">${tratamiento.frecuencia ? (tratamiento.frecuencia.charAt(0).toUpperCase() + tratamiento.frecuencia.slice(1)) : ""}</span></td>
                <td>
                    <div class="acciones-fila">
                        <button class="btn-accion btn-ver" onclick="event.stopPropagation(); mostrarModal('${tratamiento.id}')"><i class="fas fa-eye"></i></button>
                        <button class="btn-accion btn-editar" onclick="event.stopPropagation();"><i class="fas fa-edit"></i></button>
                        <button class="btn-accion btn-eliminar" onclick="event.stopPropagation();"><i class="fas fa-trash"></i></button>
                    </div>
                </td>
            `;
            
            cuerpoTabla.appendChild(fila);
        });
    }
    
    // Función para generar el PDF con los datos del tratamiento
    function generarPDF() {
        // Obtener los datos del modal actual
        const id = document.getElementById("modalId").textContent;
        const tratamiento = datosTratamientos[id];
        if (!tratamiento) return;
        
        // Crear instancia de jsPDF
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        
        // Configurar colores para el PDF (colores de zarzamora)
        const colorZarzamora = [108, 33, 108]; // RGB para #6c216c
        const colorClaro = [240, 230, 245]; // RGB para #f0e6f5
        
        // Añadir encabezado
        doc.setFillColor(colorZarzamora[0], colorZarzamora[1], colorZarzamora[2]);
        doc.rect(0, 0, 210, 30, 'F');
        doc.setTextColor(255, 255, 255);
        doc.setFontSize(22);
        doc.text(`Detalles del Tratamiento ${id}`, 105, 15, { align: 'center' });
        
        // Información general
        doc.setFillColor(colorClaro[0], colorClaro[1], colorClaro[2]);
        doc.rect(10, 40, 190, 40, 'F');
        doc.setTextColor(colorZarzamora[0], colorZarzamora[1], colorZarzamora[2]);
        doc.setFontSize(16);
        doc.text('Información General', 15, 50);
        
        doc.setTextColor(0, 0, 0);
        doc.setFontSize(12);
        let fecha = tratamiento.fecha || "";
        if (fecha && fecha.includes('-')) {
            const partesFecha = fecha.split('-');
            fecha = partesFecha[2] + '/' + partesFecha[1] + '/' + partesFecha[0];
        }
        
        doc.text(`ID: ${tratamiento.id}`, 20, 60);
        doc.text(`Fecha: ${fecha}`, 20, 70);
        doc.text(`Sector: ${tratamiento.sector || ""}`, 120, 60);
        doc.text(`Frecuencia: ${tratamiento.frecuencia ? tratamiento.frecuencia.charAt(0).toUpperCase() + tratamiento.frecuencia.slice(1) : ""}`, 120, 70);
        
        // Fumigantes
        doc.setFillColor(colorClaro[0], colorClaro[1], colorClaro[2]);
        doc.rect(10, 90, 190, 50, 'F');
        doc.setTextColor(colorZarzamora[0], colorZarzamora[1], colorZarzamora[2]);
        doc.setFontSize(16);
        doc.text('Fumigantes Aplicados', 15, 100);
        
        doc.setTextColor(0, 0, 0);
        doc.setFontSize(12);
        let yPos = 110;
        
        if (tratamiento.fumigantes && tratamiento.fumigantes.length > 0) {
            tratamiento.fumigantes.forEach(f => {
                doc.text(`• ${f.nombre}: ${f.cantidad} ${f.unidad}`, 20, yPos);
                yPos += 10;
            });
        } else {
            doc.text("• No hay fumigantes registrados", 20, yPos);
        }
        
        // Observaciones
        doc.setFillColor(colorClaro[0], colorClaro[1], colorClaro[2]);
        doc.rect(10, 150, 190, 40, 'F');
        doc.setTextColor(colorZarzamora[0], colorZarzamora[1], colorZarzamora[2]);
        doc.setFontSize(16);
        doc.text('Observaciones', 15, 160);
        
        doc.setTextColor(0, 0, 0);
        doc.setFontSize(12);
        
        // Manejar texto largo de observaciones con saltos de línea
        const observaciones = tratamiento.observaciones || "Sin observaciones";
        const splitObservaciones = doc.splitTextToSize(observaciones, 170);
        doc.text(splitObservaciones, 20, 170);
        
        // Pie de página
        doc.setFillColor(colorZarzamora[0], colorZarzamora[1], colorZarzamora[2]);
        doc.rect(0, 280, 210, 15, 'F');
        doc.setTextColor(255, 255, 255);
        doc.setFontSize(10);
        const fechaActual = new Date().toLocaleDateString();
        doc.text(`Generado el ${fechaActual}`, 105, 288, { align: 'center' });
        
        // Guardar el PDF
        doc.save(`Tratamiento_${id}.pdf`);
    }
    
    // Función para obtener el primer día del mes actual en formato YYYY-MM-DD
    function obtenerPrimerDiaMes() {
        const hoy = new Date();
        const primerDia = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        return primerDia.toISOString().split('T')[0]; // Formato YYYY-MM-DD
    }
    
    // Función para obtener el último día del mes actual en formato YYYY-MM-DD
    function obtenerUltimoDiaMes() {
        const hoy = new Date();
        const ultimoDia = new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0);
        return ultimoDia.toISOString().split('T')[0]; // Formato YYYY-MM-DD
    }
    
    // Cargar la tabla al iniciar la página
    document.addEventListener('DOMContentLoaded', function() {
        // Establecer las fechas de inicio y fin al mes actual
        document.getElementById('fechaInicio').value = obtenerPrimerDiaMes();
        document.getElementById('fechaFin').value = obtenerUltimoDiaMes();
        
        llenarTabla();
    });
</script>
</body>
</html>