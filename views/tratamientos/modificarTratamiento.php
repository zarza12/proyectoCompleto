<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once '../../controllers/daoTratamiento.php';
include_once  '../../models/Tratamiento.php';

if (isset($_POST['modificarTratamiento']) && $_POST['modificarTratamiento'] === 'modificarTratamiento') {
    // echo "aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa";
 
    $idReceta = $_POST['idReceta'];

    $fechaModificacionReceta = $_POST['fechaModificacionReceta2'];
    $sectorRecetaModificar = $_POST['sectorRecetaModificar'];
    $frecuenciaRecetaModificar = $_POST['frecuenciaRecetaModificar'];
    $observacionesRecetaModificar = $_POST['observacionesRecetaModificar'];
     
     // Capturar arrays de fumigantes
     $fumigante_nombres     = $_POST['fumigante_nombre'];
     $fumigante_cantidades  = $_POST['fumigante_cantidad'];
     $fumigante_unidades    = $_POST['fumigante_unidad'];
     
     $tratamiento = new Tratamiento(
         null , 
         $fechaModificacionReceta,
         $sectorRecetaModificar,
         $frecuenciaRecetaModificar,
         $observacionesRecetaModificar,
         $fumigante_nombres,
         $fumigante_cantidades,
         $fumigante_unidades
 
     );  
     $daoTratamiento = new daoTratamiento();
     $eliminar=$daoTratamiento->deleteTratamiento($idReceta);
     if($eliminar){
        $registo = $daoTratamiento->registrarTratamiento($tratamiento);
        if ($registo) {
            echo "
            <script>
                alert('Modificar exitoso');
                window.location.href = 'modificarTratamiento.php';
            </script>";
        } else {
            mostrarMensaje("Error al modificar el registro.");
           
        }
     }else{
        mostrarMensaje("Error al modificar el registro.");
     }
    
    
 
 }
 

$daoTratamiento = new daoTratamiento();
$listar = $daoTratamiento->listarTratamientos();
$listarJSON = json_encode($listar);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Receta de Tratamiento</title>
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
            color: var(--color-accent);
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
        
        .tabla-registros tr:hover {
            background-color: #f5f5f5;
        }
        
        .tabla-registros .fila-activa {
            background-color: rgba(165, 105, 189, 0.1);
            border-left: 4px solid var(--color-accent);
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
            transition: var(--transition);
            color: var(--color-text-secondary);
            border-radius: 4px;
        }
        
        .btn-accion:hover {
            transform: translateY(-2px);
        }
        
        .btn-editar {
            color: var(--color-accent);
        }
        
        .btn-editar:hover {
            color: var(--color-secondary);
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
        
        /* Formulario de edición */
        .formulario-edicion {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 25px;
            margin-top: 30px;
            border-top: 5px solid var(--color-accent);
        }
        
        .titulo-formulario {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--color-border);
        }
        
        .titulo-formulario h3 {
            color: var(--color-primary);
            font-size: 20px;
            margin-right: 15px;
        }
        
        .id-registro {
            background-color: var(--color-accent);
            color: white;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .detalles-item {
            background-color: rgba(165, 105, 189, 0.1);
            padding: 8px 15px;
            border-radius: var(--border-radius);
            margin-bottom: 15px;
            font-size: 14px;
            color: var(--color-secondary);
        }
        
        .detalles-item strong {
            color: var(--color-primary);
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
            color: var(--color-text-dark);
        }
        
        .form-input,
        .form-select, 
        .form-textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--color-border);
            border-radius: var(--border-radius);
            font-size: 14px;
            outline: none;
            transition: var(--transition);
        }
        
        .form-textarea {
            resize: vertical;
            min-height: 80px;
        }
        
        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            border-color: var(--color-accent);
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
        
        .required {
            color: var(--color-danger);
            margin-left: 3px;
        }
        
        /* Fumigantes */
        .fumigante-header {
            display: grid;
            grid-template-columns: 3fr 1fr 1fr 40px;
            gap: 10px;
            margin-bottom: 10px;
            font-weight: 500;
            color: var(--color-primary);
            font-size: 14px;
        }
        
        .fumigante-item {
            display: grid;
            grid-template-columns: 3fr 1fr 1fr 40px;
            gap: 10px;
            margin-bottom: 10px;
            align-items: center;
        }
        
        .btn-remove {
            background-color: var(--color-danger);
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .btn-remove:hover {
            background-color: #c0392b;
            transform: scale(1.1);
        }
        
        .btn-add {
            background-color: var(--color-accent);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            padding: 8px 15px;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            font-weight: 500;
            font-size: 14px;
            margin-top: 10px;
        }
        
        .btn-add:hover {
            background-color: var(--color-secondary);
            transform: translateY(-2px);
        }
        
        .btn-add:before {
            content: "+";
            margin-right: 5px;
            font-size: 16px;
            font-weight: bold;
        }
        
        /* Estados */
        .estado {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            text-align: center;
        }
        
        .estado-activa {
            background-color: rgba(46, 204, 113, 0.2);
            color: #27ae60;
        }
        
        .estado-pendiente {
            background-color: rgba(241, 196, 15, 0.2);
            color: #f39c12;
        }
        
        .estado-inactiva {
            background-color: rgba(231, 76, 60, 0.2);
            color: #c0392b;
        }
        
        /* Botones */
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
        
        .btn-primario {
            background-color: var(--color-primary);
            color: white;
        }
        
        .btn-primario:hover {
            background-color: var(--color-secondary);
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
        
        .btn-exportar {
            background-color: #009688;
            color: white;
        }
        
        .btn-exportar:hover {
            background-color: #00796b;
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
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .fumigante-header, .fumigante-item {
                grid-template-columns: 2fr 1fr 1fr 40px;
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
    
    <div class="contenedor">
        <div class="encabezado-pagina">
            <div class="titulo-pagina">
                <i class="fas fa-flask icono-seccion"></i>
                <h1>Modificar Receta de Tratamiento</h1>
            </div>
            <div class="botones-accion" style="display: none;">
                <button class="btn btn-exportar"><i class="fas fa-file-export"></i> Exportar Recetas</button>
            </div>
        </div>
        
        <div class="filtro-busqueda">
            <div class="busqueda">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Buscar recetas..." id="buscarReceta">
            </div>
            <div class="filtros">
                <div class="filtro">
                    <select id="filtroSector">
                        <option value="">Todos los sectores</option>
                        <option value="sector1">Sector A - Norte</option>
                        <option value="sector2">Sector B - Sur</option>
                        <option value="sector3">Sector C - Este</option>
                        <option value="sector4">Sector D - Oeste</option>
                    </select>
                </div>
                <div class="filtro">
                    <select id="filtroFrecuencia">
                        <option value="">Todas las frecuencias</option>
                        <option value="diario">Diario</option>
                        <option value="semanal">Semanal</option>
                        <option value="quincenal">Quincenal</option>
                        <option value="mensual">Mensual</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Tabla de Recetas -->
        <div class="tabla-contenedor">
            <table class="tabla-registros">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Sector</th>
                        <th>Fecha</th>
                        <th>Fumigantes</th>
                        <th>Frecuencia</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                 
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
        
        <!-- Formulario de Edición de Receta -->
        <div class="formulario-edicion" id="formularioEdicionReceta" style="display: none;">
    <div class="titulo-formulario">
        <h3>Editar Receta de Tratamiento</h3>
        <span class="id-registro" id="idRecetaMostrado">REC-001</span>
    </div>
    
    <div class="detalles-item">
        <i class="fas fa-info-circle"></i>
        <strong>Nota:</strong> Selecciona una receta de la tabla para modificar sus detalles. La fecha de modificación se actualiza automáticamente.
    </div>
    
    <form id="formularioModificarReceta" method="POST" action="modificarTratamiento.php">
  <input type="hidden" id="idReceta" name="idReceta" value="">
  <div class="form-grid">
    <div class="form-group">
      <label class="form-label">Fecha de Modificación</label>
      <input type="date" style="display: none" id="fechaModificacionReceta2" name="fechaModificacionReceta2" class="form-input">
      <input type="date" id="fechaModificacionReceta" name="fechaModificacionReceta" class="form-input" disabled>
    </div>
    <div class="form-group">
      <label class="form-label">Sector<span class="required">*</span></label>
      <select id="sectorRecetaModificar" name="sectorRecetaModificar" class="form-select" required>
        <option value="">Seleccione un sector</option>
        <option value="sector_a">Sector A</option>
        <option value="sector_b">Sector B</option>
        <option value="sector_c">Sector C</option>
        <option value="sector_d">Sector D</option>
      </select>
    </div>
    <div class="form-group">
      <label class="form-label">Frecuencia del Tratamiento<span class="required">*</span></label>
      <select id="frecuenciaRecetaModificar" name="frecuenciaRecetaModificar" class="form-select" required>
        <option value="">Seleccione la frecuencia</option>
        <option value="diario">Diario</option>
        <option value="semanal">Semanal</option>
        <option value="quincenal">Quincenal</option>
        <option value="mensual">Mensual</option>
      </select>
    </div>
  </div>
  <div class="form-group">
    <label class="form-label">Fumigantes y Cantidades<span class="required">*</span></label>
    <div class="fumigante-header">
      <div>Nombre del Fumigante</div>
      <div>Cantidad</div>
      <div>Unidad</div>
      <div></div>
    </div>
    <div id="contenedorFumigantesModificar">
      <!-- Los fumigantes se agregarán dinámicamente aquí -->
    </div>
    <button type="button" class="btn-add" onclick="agregarFumiganteModificar()">Agregar Fumigante</button>
  </div>
  <div class="form-group">
    <label class="form-label">Observaciones</label>
    <textarea id="observacionesRecetaModificar" name="observacionesRecetaModificar" class="form-textarea" placeholder="Ingrese cualquier observación relevante..."></textarea>
  </div>
  <div class="botones-form">
    <button type="button" class="btn btn-secundario" onclick="cancelarEdicionReceta()"><i class="fas fa-times"></i> Cancelar</button>
    <button type="submit" value="modificarTratamiento" id="modificarTratamiento" name="modificarTratamiento" class="btn btn-primario"><i class="fas fa-save"></i> Guardar Cambios</button>
  </div>
</form>
</div>
    </div>
    
    <script>
        // Establecer la fecha actual en el campo de fecha
        document.addEventListener('DOMContentLoaded', function() {
            const fechaHoy = new Date().toISOString().split('T')[0];
            document.getElementById('fechaModificacionReceta').value = fechaHoy;
            document.getElementById('fechaModificacionReceta2').value = fechaHoy;
            
            // Cargar los datos en la tabla al iniciar
            cargarDatosEnTabla();
        });
        
        // Función para cargar los datos en la tabla
        function cargarDatosEnTabla() {
            // Obtenemos los datos del array de recetas
            const datos = <?php echo $listarJSON; ?>

            // Obtener referencia a la tabla
            const tbody = document.querySelector('.tabla-registros tbody');
            tbody.innerHTML = ''; // Limpiar la tabla primero
            
            // Recorrer el objeto de datos y crear filas para cada receta
            for (const [id, receta] of Object.entries(datos)) {
                const fila = document.createElement('tr');
                fila.id = 'fila-' + id;
                
                // Obtener el texto del sector basado en el valor
                let sectorTexto = '';
                switch(receta.sector) {
                    case 'sector1': sectorTexto = 'Sector A - Norte'; break;
                    case 'sector2': sectorTexto = 'Sector B - Sur'; break;
                    case 'sector3': sectorTexto = 'Sector C - Este'; break;
                    case 'sector4': sectorTexto = 'Sector D - Oeste'; break;
                    default: sectorTexto = 'Desconocido';
                }
                
                // Obtener el texto de la frecuencia basado en el valor
                let frecuenciaTexto = '';
                switch(receta.frecuencia) {
                    case 'diario': frecuenciaTexto = 'Diario'; break;
                    case 'semanal': frecuenciaTexto = 'Semanal'; break;
                    case 'quincenal': frecuenciaTexto = 'Quincenal'; break;
                    case 'mensual': frecuenciaTexto = 'Mensual'; break;
                    default: frecuenciaTexto = 'Desconocido';
                }
                
                // Obtener nombres de fumigantes para mostrar en la tabla
                const nombresFumigantes = receta.fumigantes.map(f => f.nombre).join(', ');
                
                // Generar estado aleatorio para simular datos reales
                const estados = ['Activo', 'Pausado', 'Completado'];
                const estadoAleatorio = estados[Math.floor(Math.random() * estados.length)];
                
                // Crear contenido de la fila
                fila.innerHTML = `
                    <td>${id}</td>
                    <td>${sectorTexto}</td>
                    <td>${receta.fecha}</td>
                    <td>${nombresFumigantes}</td>
                    <td>${frecuenciaTexto}</td>
                    <td><span class="estado estado-${estadoAleatorio.toLowerCase()}">${estadoAleatorio}</span></td>
                    <td class="acciones">
                        <button onclick="seleccionarReceta('${id}')" class="btn-accion btn-editar" title="Editar"><i class="fas fa-pen"></i></button>
                        <button class="btn-accion btn-eliminar" title="Eliminar" style="display: none;"><i class="fas fa-trash"></i></button>
                    </td>
                `;
                
                // Hacer que toda la fila sea clickeable
                fila.addEventListener('click', function() {
                    seleccionarReceta(id);
                });
                
                tbody.appendChild(fila);
            }
            
            // Actualizar los datos en el objeto global para que estén disponibles para edición
            window.datosRecetas = datos;
        }
        
        // Función para seleccionar una receta de la tabla
        function seleccionarReceta(id) {
            // Remover clase activa de todas las filas
            document.querySelectorAll('.tabla-registros tbody tr').forEach(fila => {
                fila.classList.remove('fila-activa');
            });
            
            // Agregar clase activa a la fila seleccionada
            document.getElementById('fila-' + id).classList.add('fila-activa');
            
            // Mostrar el formulario de edición
            const formulario = document.getElementById('formularioEdicionReceta');
            formulario.style.display = 'block';
            
            // Hacer scroll suave hasta el formulario
            formulario.scrollIntoView({ behavior: 'smooth', block: 'start' });
            
            // Actualizar el ID mostrado en el formulario
            document.getElementById('idRecetaMostrado').textContent = id;
            document.getElementById('idReceta').value = id;
            
            // Cargar los datos de la receta seleccionada
            cargarDatosReceta(id);
        }
        
        // Simulación de carga de datos de receta
        function cargarDatosReceta(id) {
            // Usar los datos del objeto global
            const datos = window.datosRecetas;
            
            // Aplicar los datos al formulario
            if (datos[id]) {
                document.getElementById('sectorRecetaModificar').value = datos[id].sector;
                document.getElementById('fechaModificacionReceta').value = new Date().toISOString().split('T')[0]; // Fecha actual
                document.getElementById('frecuenciaRecetaModificar').value = datos[id].frecuencia;
                document.getElementById('observacionesRecetaModificar').value = datos[id].observaciones;
                
                // Limpiar contenedor de fumigantes
                document.getElementById('contenedorFumigantesModificar').innerHTML = '';
                
                // Agregar fumigantes
                datos[id].fumigantes.forEach(fumigante => {
                    agregarFumiganteExistente(fumigante.nombre, fumigante.cantidad, fumigante.unidad);
                });
            }
        }
        
        // Función para agregar un fumigante existente al cargar datos
        function agregarFumiganteExistente(nombre, cantidad, unidad) {
            const contenedor = document.getElementById('contenedorFumigantesModificar');
            const nuevoFumigante = document.createElement('div');
            nuevoFumigante.className = 'fumigante-item';
            nuevoFumigante.innerHTML = `
                <input type="text" name="fumigante_nombre[]" class="form-input" value="${nombre}" placeholder="Nombre del fumigante" required>
                <input type="number" name="fumigante_cantidad[]" class="form-input" value="${cantidad}" placeholder="Cantidad" step="0.01" min="0" required>
                <select class="form-select" name="fumigante_unidad[]" >
                    <option value="litros" ${unidad === 'litros' ? 'selected' : ''}>Litros</option>
                    <option value="mililitros" ${unidad === 'mililitros' ? 'selected' : ''}>Mililitros</option>
                    <option value="gramos" ${unidad === 'gramos' ? 'selected' : ''}>Gramos</option>
                    <option value="kilogramos" ${unidad === 'kilogramos' ? 'selected' : ''}>Kilogramos</option>
                </select>
                <button type="button" class="btn-remove" onclick="eliminarFumigante(this)">×</button>
            `;
            contenedor.appendChild(nuevoFumigante);
        }
        
        // Función para agregar un nuevo fumigante
        function agregarFumiganteModificar() {
            const contenedor = document.getElementById('contenedorFumigantesModificar');
            const nuevoFumigante = document.createElement('div');
            nuevoFumigante.className = 'fumigante-item';
            nuevoFumigante.innerHTML = `
                <input type="text" name="fumigante_nombre[]" class="form-input" placeholder="Nombre del fumigante" required>
                <input type="number" name="fumigante_cantidad[]" class="form-input" placeholder="Cantidad" step="0.01" min="0" required>
                <select class="form-select" name="fumigante_unidad[]">
                    <option value="litros">Litros</option>
                    <option value="mililitros">Mililitros</option>
                    <option value="gramos">Gramos</option>
                    <option value="kilogramos">Kilogramos</option>
                </select>
                <button type="button" class="btn-remove" onclick="eliminarFumigante(this)">×</button>
            `;
            contenedor.appendChild(nuevoFumigante);
        }
        
        // Función para eliminar un fumigante
        function eliminarFumigante(boton) {
            boton.closest('.fumigante-item').remove();
        }
        
        // Función para cancelar la edición
        function cancelarEdicionReceta() {
            document.getElementById('formularioEdicionReceta').style.display = 'none';
            
            // Remover clase activa de todas las filas
            document.querySelectorAll('.tabla-registros tbody tr').forEach(fila => {
                fila.classList.remove('fila-activa');
            });
        }
        
        // Función para guardar los cambios

        
        // Inicializar búsqueda
        document.getElementById('buscarReceta').addEventListener('input', function(e) {
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
        document.getElementById('filtroSector').addEventListener('change', function() {
            filtrarTabla();
        });
        
        document.getElementById('filtroFrecuencia').addEventListener('change', function() {
            filtrarTabla();
        });
        
        function filtrarTabla() {
            const filtroSector = document.getElementById('filtroSector').value.toLowerCase();
            const filtroFrecuencia = document.getElementById('filtroFrecuencia').value.toLowerCase();
            const filas = document.querySelectorAll('.tabla-registros tbody tr');
            
            filas.forEach(fila => {
                let mostrar = true;
                
                // Filtrar por sector
                if (filtroSector && !fila.cells[1].textContent.toLowerCase().includes(filtroSector)) {
                    mostrar = false;
                }
                
                // Filtrar por frecuencia
                if (filtroFrecuencia && !fila.cells[4].textContent.toLowerCase().includes(filtroFrecuencia)) {
                    mostrar = false;
                }
                
                fila.style.display = mostrar ? '' : 'none';
            });
        }
    </script>
</body>
</html>