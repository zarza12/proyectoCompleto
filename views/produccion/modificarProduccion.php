<?php 
include_once '../../controllers/daoProduccion.php';
include_once  '../../models/Produccion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['modificarProduccionBtn']) && $_POST['modificarProduccionBtn'] === 'modificarProduccionBtn') {

    // Recibir datos del formulario en PHP
    $idRegistro          = $_POST['idRegistro'];
    $sectorProduccion    = $_POST['sectorModificar'] ;
    $fechaProduccion     = null;
    $calidadExportacion  = $_POST['exportacionModificar'];
    $calidadNacional     = $_POST['nacionalModificar'];
    $calidadDesecho      = $_POST['desechoModificar'];
    $totalCajas          = $_POST['totalCajasModificar2'] ;
    
    

    $produccion = new Produccion(
        $idRegistro , 
        $sectorProduccion,
        $fechaProduccion,
        $calidadExportacion,
        $calidadNacional,
        $calidadDesecho,
        $totalCajas
    );  
    $daoProduccion = new daoProduccion();
   

    $registo = $daoProduccion->modificarProduccion($produccion);
    

    if ($registo) {
        echo "
        <script>
            alert('Modificado exitoso');
            window.location.href = 'modificarProduccion.php';
        </script>";
    } else {
        mostrarMensaje("Error al modificar el registro.");
       
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
    <title>Modificar Producción</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --color-primary: #4A235A;
            --color-secondary: #7D3C98;
            --color-accent: #A569BD;
            --color-hover: #D2B4DE;
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
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: var(--font-primary);
            background-color: var(--color-background);
            color: var(--color-text-dark);
            line-height: 1.6;
        }
        .barra-superior {
            position: fixed; top: 0; left: 0; right: 0; height: 4px;
            background: linear-gradient(90deg, var(--color-primary), var(--color-accent), var(--color-secondary));
            z-index: 1000;
        }
        .info-usuario { /* ... */ }
        .contenedor {
            max-width: 1200px; margin: 0 auto; padding: 50px 20px 30px;
        }
        .encabezado-pagina { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid var(--color-border); padding-bottom: 15px; }
        .titulo-pagina h1 { color: var(--color-primary); font-size: 28px; margin-right: 15px; }
        .icono-seccion { font-size: 24px; color: var(--color-accent); margin-right: 15px; }
        .botones-accion button { /* ... */ }
        .filtro-busqueda { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px; }
        .busqueda {
            display: flex; align-items: center; background-color: white;
            border: 1px solid var(--color-border); border-radius: var(--border-radius);
            padding: 10px 15px; width: 300px; box-shadow: var(--shadow);
        }
        .busqueda input { border: none; background: none; outline: none; font-size: 14px; flex: 1; }
        .busqueda i { color: var(--color-secondary); }
        .filtros { display: flex; gap: 15px; }
        .filtro { position: relative; }
        .filtro select {
            appearance: none; background-color: white; border: 1px solid var(--color-border);
            border-radius: var(--border-radius); padding: 10px 35px 10px 15px;
            font-size: 14px; color: var(--color-text-dark); cursor: pointer;
            outline: none; box-shadow: var(--shadow); min-width: 150px;
        }
        .filtro::after {
            content: '\f0d7'; font-family: 'Font Awesome 6 Free'; font-weight: 900;
            color: var(--color-secondary); position: absolute; right: 15px; top: 50%;
            transform: translateY(-50%); pointer-events: none;
        }
        .tabla-contenedor { background-color: white; border-radius: var(--border-radius); box-shadow: var(--shadow); overflow: hidden; margin-bottom: 30px; }
        .tabla-registros { width: 100%; border-collapse: collapse; }
        .tabla-registros th, .tabla-registros td {
            padding: 12px 15px; text-align: left; border-bottom: 1px solid var(--color-border);
        }
        .tabla-registros th {
            background-color: var(--color-primary); color: var(--color-text);
            font-weight: 600; text-transform: uppercase; font-size: 13px; letter-spacing: 0.5px;
        }
        .tabla-registros tr:hover { background-color: #f5f5f5; }
        .fila-activa { background-color: rgba(165, 105, 189, 0.1); border-left: 4px solid var(--color-accent); }
        .fila-activa:hover { background-color: rgba(165, 105, 189, 0.15); }
        .btn-accion { background: none; border: none; cursor: pointer; padding: 5px; font-size: 16px; transition: var(--transition); color: var(--color-text-secondary); border-radius: 4px; }
        .btn-editar { color: var(--color-accent); }
        .btn-eliminar { color: var(--color-danger); }
        .paginacion { display: flex; justify-content: flex-end; align-items: center; margin-top: 20px; gap: 5px; }
        .btn-pagina { background-color: white; border: 1px solid var(--color-border); border-radius: 4px; padding: 8px 12px; cursor: pointer; transition: var(--transition); color: var(--color-text-dark); font-weight: 500; }
        .btn-pagina.activa { background-color: var(--color-primary); color: white; border-color: var(--color-primary); }
        .formulario-edicion { display: none; background-color: white; border-radius: var(--border-radius); box-shadow: var(--shadow); padding: 25px; margin-top: 30px; border-top: 5px solid var(--color-accent); }
        .titulo-formulario h3 { color: var(--color-primary); font-size: 20px; margin-right: 15px; }
        .id-registro { background-color: var(--color-accent); color: white; padding: 4px 10px; border-radius: 15px; font-size: 14px; font-weight: 600; }
        .detalles-item { background-color: rgba(165, 105, 189, 0.1); padding: 8px 15px; border-radius: var(--border-radius); margin-bottom: 15px; font-size: 14px; color: var(--color-secondary); }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 25px; }
        .form-group { margin-bottom: 15px; }
        .form-label { display: block; margin-bottom: 8px; font-weight: 500; color: var(--color-text-dark); }
        .form-input, .form-select {
            width: 100%; padding: 12px 15px; border: 1px solid var(--color-border);
            border-radius: var(--border-radius); font-size: 14px; outline: none; transition: var(--transition);
        }
        .form-select { appearance: none; padding-right: 35px; background-repeat: no-repeat; background-position: right 10px center; background-size: 16px; }
        .seccion-calidad h3 { color: var(--color-primary); margin-bottom: 15px; font-size: 18px; display: flex; align-items: center; }
        .calidad-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px; }
        .calidad-item { background-color: #f9f9f9; padding: 15px; border-radius: var(--border-radius); border: 1px solid var(--color-border); transition: var(--transition); }
        .calidad-item label { display: flex; align-items: center; margin-bottom: 10px; font-weight: 500; color: var(--color-primary); }
        .calidad-total { margin-top: 15px; background-color: rgba(165, 105, 189, 0.1); padding: 10px 15px; border-radius: var(--border-radius); display: flex; justify-content: space-between; align-items: center; font-weight: 500; }
        .botones-form { display: flex; justify-content: flex-end; gap: 15px; margin-top: 30px; padding-top: 20px; border-top: 1px solid var(--color-border); }
        .btn { padding: 12px 20px; border: none; border-radius: var(--border-radius); cursor: pointer; font-weight: 600; font-size: 14px; display: flex; align-items: center; transition: var(--transition); }
        .btn-primario { background-color: var(--color-primary); color: white; }
        .btn-secundario { background-color: #f1f1f1; color: var(--color-text-dark); }
        .btn-exportar { background-color: #009688; color: white; }
        .estado { display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; text-transform: uppercase; text-align: center; }
        .estado-exportacion { background-color: rgba(46, 204, 113, 0.2); color: #27ae60; }
        .estado-nacional { background-color: rgba(52, 152, 219, 0.2); color: #2980b9; }
        .estado-desecho { background-color: rgba(231, 76, 60, 0.2); color: #c0392b; }
        @media (max-width: 768px) {
            .filtro-busqueda { flex-direction: column; gap: 10px; }
            .busqueda, .filtro select { width: 100%; }
            .form-grid, .calidad-grid { grid-template-columns: 1fr; }
            .tabla-registros { display: block; overflow-x: auto; }
            .botones-form { flex-direction: column; }
            .btn { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body>
    <?php include '../../views/menuA.php'; ?>
    <div class="barra-superior"></div>
    <div class="contenedor">
        <div class="encabezado-pagina">
            <div class="titulo-pagina">
                <i class="fas fa-edit icono-seccion"></i>
                <h1>Modificar Producción</h1>
            </div>
            <div class="botones-accion">
                <button class="btn btn-exportar"><i class="fas fa-file-export"></i> Exportar Datos</button>
            </div>
        </div>

        <div class="filtro-busqueda">
            <div class="busqueda"><i class="fas fa-search"></i><input type="text" placeholder="Buscar registros..." id="buscarRegistro"></div>
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
                <tbody></tbody>
            </table>
        </div>

        <div class="paginacion">
            <button class="btn-pagina"><i class="fas fa-chevron-left"></i></button>
            <button class="btn-pagina activa">1</button>
            <button class="btn-pagina">2</button>
            <button class="btn-pagina">3</button>
            <button class="btn-pagina"><i class="fas fa-chevron-right"></i></button>
        </div>

        <div class="formulario-edicion" id="formularioEdicion">
            <div class="titulo-formulario">
                <h3>Editar Registro de Producción</h3>
                <span class="id-registro" id="idRegistroMostrado">PROD-001</span>
            </div>
            <div class="detalles-item"><i class="fas fa-info-circle"></i><strong>Nota:</strong> Selecciona un registro de la tabla para modificar sus datos.</div>
    <form id="formularioModificar" method="POST" action="modificarProduccion.php">
        <input type="hidden" id="idRegistro" name="idRegistro" value="">
        <div class="form-grid">
        <div class="form-group">
            <label class="form-label">Sector de Producción</label>
            <select id="sectorModificar" name="sectorModificar" class="form-select"></select>
        </div>
        <div class="form-group">
            <label class="form-label">Fecha de Producción</label>
            <input type="date" id="fechaModificar" name="fechaModificar" class="form-input" disabled>
        </div>
        </div>
        <div class="seccion-calidad">
        <h3><i class="fas fa-clipboard-check"></i> Calidad del Lote</h3>
        <div class="calidad-grid">
            <div class="calidad-item">
                <label><i class="fas fa-globe"></i> Exportación</label>
                <input type="number" id="exportacionModificar" name="exportacionModificar" class="form-input" min="0" oninput="actualizarTotalCajas()">
            </div>
            <div class="calidad-item">
                <label><i class="fas fa-flag"></i> Nacional</label>
                <input type="number" id="nacionalModificar" name="nacionalModificar" class="form-input" min="0" oninput="actualizarTotalCajas()">
            </div>
            <div class="calidad-item">
                <label><i class="fas fa-trash-alt"></i> Desecho</label>
                <input type="number" id="desechoModificar" name="desechoModificar" class="form-input" min="0" oninput="actualizarTotalCajas()">
            </div>
        </div>
        <div class="calidad-total">Total de cajas: <span id="totalCajasModificar">0</span></div>
        <input type="text" id="totalCajasModificar2" name="totalCajasModificar2" style="display: none;">
        </div>
        <div class="botones-form">
        <button type="button" id="btnCancelar" name="btnCancelar" class="btn btn-secundario" onclick="cancelarEdicion()"><i class="fas fa-times"></i> Cancelar</button>
        <button type="submit" id="modificarProduccionBtn" name="modificarProduccionBtn" value="modificarProduccionBtn" class="btn btn-primario"><i class="fas fa-save"></i> Guardar Cambios</button>
        </div>
    </form>

        </div>
    </div>

<!-- No modificaré la parte PHP superior, solo mostraré las correcciones en el JavaScript -->

<script>
    // Sectores disponibles
    const sectores = [
        { value: 'sector_a', label: 'Sector A' },
        { value: 'sector_ak', label: 'Sector Ak' },
        { value: 'sector_b', label: 'Sector B' },
        { value: 'sector_c', label: 'Sector C' },
        { value: 'sector_d', label: 'Sector D' }
    ];

    // Datos de producción desde PHP
    const datosRegistros = <?php echo $produccionesJSON; ?>;

    // Llenar selects de sector
    function populateSectorSelects() {
        const filtro = document.getElementById('filtroSector');
        const modificar = document.getElementById('sectorModificar');
        sectores.forEach(opt => {
            const o1 = document.createElement('option');
            o1.value = opt.value;
            o1.textContent = opt.label;
            filtro.appendChild(o1);
            const o2 = o1.cloneNode(true);
            modificar.appendChild(o2);
        });
    }

    // Formatea sector para mostrar
    function formatSectorText(v) {
        const m = sectores.reduce((acc, s) => (acc[s.value] = s.label, acc), {});
        return m[v] || v;
    }

    // Formatea fecha ISO a DD/MM/YYYY
    function formatDate(iso) {
        if (!iso) return '';
        const [y, m, d] = iso.split('-');
        return `${d}/${m}/${y}`;
    }

    // Convertir fecha DD/MM/YYYY a formato ISO YYYY-MM-DD
    function parseDate(dateString) {
        if (!dateString) return '';
        const [d, m, y] = dateString.split('/');
        return `${y}-${m}-${d}`;
    }

    // Obtener fecha de hoy en formato ISO local (sin ajuste de zona horaria)
    function getLocalISODate() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    // Obtener fecha de inicio de semana en formato ISO
    function getStartOfWeekDate() {
        const now = new Date();
        const day = now.getDay(); // 0 = domingo, 1 = lunes, etc.
        const diff = now.getDate() - day + (day === 0 ? -6 : 1); // Ajuste para que la semana comience en lunes
        const startOfWeek = new Date(now.setDate(diff));
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

    // Genera la tabla de registros
    function generarTabla() {
        const tbody = document.querySelector('.tabla-registros tbody');
        tbody.innerHTML = '';
        datosRegistros.forEach(reg => {
            const total = reg.exportacion + reg.nacional + reg.desecho;
            const tr = document.createElement('tr');
            tr.id = 'fila-' + reg.id;
            tr.dataset.fecha = reg.fecha; // Guardamos la fecha ISO original como atributo data
            tr.addEventListener('click', () => seleccionarRegistro(reg.id));
            tr.innerHTML = `
                <td>${reg.id2}</td>
                <td>${formatSectorText(reg.sector)}</td>
                <td>${formatDate(reg.fecha)}</td>
                <td>${total}</td>
                <td><span class="estado estado-exportacion">${reg.exportacion}</span></td>
                <td><span class="estado estado-nacional">${reg.nacional}</span></td>
                <td><span class="estado estado-desecho">${reg.desecho}</span></td>
                <td>
                    <button class="btn-accion btn-editar"><i class="fas fa-pen"></i></button>
                    <button class="btn-accion btn-eliminar"><i class="fas fa-trash"></i></button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    // Seleccionar un registro para editar
    function seleccionarRegistro(id) {
        document.querySelectorAll('.tabla-registros tbody tr').forEach(f => f.classList.remove('fila-activa'));
        const fila = document.getElementById('fila-' + id);
        fila.classList.add('fila-activa');
        document.getElementById('formularioEdicion').style.display = 'block';
        document.getElementById('idRegistroMostrado').textContent = 'PROD-' + String(id).padStart(3, '0');
        document.getElementById('idRegistro').value = id;
        cargarDatosRegistro(id);
    }

    // Cargar datos en el formulario
    function cargarDatosRegistro(id) {
        const r = datosRegistros.find(item => item.id === id);
        if (!r) return;
        document.getElementById('sectorModificar').value = r.sector;
        document.getElementById('fechaModificar').value = r.fecha;
        document.getElementById('exportacionModificar').value = r.exportacion;
        document.getElementById('nacionalModificar').value = r.nacional;
        document.getElementById('desechoModificar').value = r.desecho;
        actualizarTotalCajas();
    }

    // Actualizar total de cajas en el formulario
    function actualizarTotalCajas() {
        const e = parseInt(document.getElementById('exportacionModificar').value) || 0;
        const n = parseInt(document.getElementById('nacionalModificar').value) || 0;
        const d = parseInt(document.getElementById('desechoModificar').value) || 0;
        document.getElementById('totalCajasModificar').textContent = e + n + d;
        document.getElementById('totalCajasModificar2').value = e + n + d;
    }

    // Cancelar edición
    function cancelarEdicion() {
        document.getElementById('formularioEdicion').style.display = 'none';
        document.querySelectorAll('.tabla-registros tbody tr').forEach(f => f.classList.remove('fila-activa'));
    }

    // Filtros y búsqueda - CORREGIDO
    function filtrarTabla() {
        const fs = document.getElementById('filtroSector').value.toLowerCase();
        const ff = document.getElementById('filtroFecha').value;
        const hoyISO = getLocalISODate();
        const inicioSemanaISO = getStartOfWeekDate();
        const inicioMesISO = getStartOfMonthDate();
        
        const tbody = document.querySelectorAll('.tabla-registros tbody tr');
        tbody.forEach(f => {
            const cols = f.cells;
            let show = true;
            
            // Filtro por sector
            if (fs && cols[1].textContent.toLowerCase() !== formatSectorText(fs).toLowerCase()) {
                show = false;
            }
            
            // Filtro por fecha - usamos la fecha ISO guardada en data-fecha
            if (ff && show) {
                const filaFechaISO = f.dataset.fecha;
                
                if (ff === 'hoy' && filaFechaISO !== hoyISO) {
                    show = false;
                } else if (ff === 'semana' && filaFechaISO < inicioSemanaISO) {
                    show = false;
                } else if (ff === 'mes' && filaFechaISO < inicioMesISO) {
                    show = false;
                }
            }
            
            f.style.display = show ? '' : 'none';
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        populateSectorSelects();
        generarTabla();
        document.getElementById('buscarRegistro').addEventListener('input', e => {
            const q = e.target.value.toLowerCase();
            document.querySelectorAll('.tabla-registros tbody tr')
                .forEach(f => f.style.display = f.textContent.toLowerCase().includes(q) ? '' : 'none');
        });
        document.getElementById('filtroSector').addEventListener('change', filtrarTabla);
        document.getElementById('filtroFecha').addEventListener('change', filtrarTabla);
    });
</script>
</body>
</html>
