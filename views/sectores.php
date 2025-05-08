<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once  '../../controllers/daoSector.php';
include_once  '../../models/Sector.php';

if (isset($_POST['registrarSector']) && $_POST['registrarSector'] === 'registrarSector') {
  // Recibir datos del formulario en PHP

  $nombre      = $_POST['nombreSector'];
  $descripcion = $_POST['descripcionSector'];
  $fecha       = $_POST['fechaRegistro'];
  $nombreJunto = str_replace(' ', '_', $nombre);

  // Pasar los 4 como parámetros
  $sector = new Sector(null,$nombre, $nombreJunto, $descripcion, $fecha);
  $daoSectores = new daoSector();
   

  $registo = $daoSectores->registrarPersonas($sector);

  if ($registo) {
      echo "
      <script>
         
              alert('Registro exitoso');
              window.location.href = 'sectores.php';
          
      </script>";
      
  } else {
      mostrarMensaje("Error al insertar el registro.");
     
  }
}
$daoSectores2 = new daoSector();
$listarSectores = $daoSectores2->listarSectores();
$sectoresJSON = json_encode($listarSectores);

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro de Sectores</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --color-primario: #4A235A; /* Color zarzamora oscuro */
      --color-secundario: #7D3C98; /* Color zarzamora medio */
      --color-acento: #A569BD; /* Color zarzamora claro */
      --color-resalte: #D2B4DE; /* Color zarzamora muy claro */
      --color-texto: #FFFFFF;
      --color-texto-oscuro: #333333;
      --color-borde: #E0E0E0;
      --color-fondo: #F5F5F5;
      --sombra: 0 8px 20px rgba(0, 0, 0, 0.1);
      --sombra-suave: 0 4px 10px rgba(0, 0, 0, 0.05);
      --borde-radio: 12px;
      --transicion: all 0.3s ease;
    }
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: var(--color-fondo);
      color: var(--color-texto-oscuro);
      line-height: 1.6;
    }
    /* Barra superior de degradado */
    .barra-superior {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      height: 6px;
      background: linear-gradient(90deg, var(--color-primario), var(--color-acento), var(--color-secundario));
      z-index: 1000;
      animation: gradientAnimation 6s ease infinite;
      background-size: 200% 200%;
    }
    @keyframes gradientAnimation {
      0% {background-position: 0% 50%}
      50% {background-position: 100% 50%}
      100% {background-position: 0% 50%}
    }
    /* Información de usuario */
    .info-usuario {
      position: fixed;
      top: 20px;
      right: 30px;
      display: flex;
      align-items: center;
      background-color: var(--color-primario);
      padding: 8px 18px;
      border-radius: 25px;
      color: var(--color-texto);
      font-size: 15px;
      box-shadow: var(--sombra);
      z-index: 990;
      transition: var(--transicion);
    }
    .info-usuario:hover {
      background-color: var(--color-secundario);
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }
    .avatar-usuario {
      width: 32px;
      height: 32px;
      background-color: var(--color-acento);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 12px;
      font-weight: bold;
      box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.2);
    }
    /* Contenedor principal */
    .contenedor {
      max-width: 1200px;
      margin: 0 auto;
      padding: 40px 25px;
      padding-top: 70px; /* Espacio para la barra superior y perfil */
    }
    /* Encabezado de la página */
    .encabezado-pagina {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 35px;
      border-bottom: 2px solid var(--color-resalte);
      padding-bottom: 20px;
    }
    .titulo-pagina {
      display: flex;
      align-items: center;
    }
    .titulo-pagina h1 {
      color: var(--color-primario);
      font-size: 32px;
      margin-right: 15px;
      font-weight: 700;
      letter-spacing: 0.5px;
    }
    .icono-seccion {
      font-size: 30px;
      color: var(--color-acento);
      margin-right: 18px;
      background-color: rgba(165, 105, 189, 0.1);
      padding: 12px;
      border-radius: 12px;
    }
    
    /* Formulario de registro */
    .formulario-contenedor {
      background-color: white;
      border-radius: var(--borde-radio);
      box-shadow: var(--sombra);
      overflow: hidden;
      margin-bottom: 30px;
      border: 1px solid rgba(165, 105, 189, 0.1);
    }
    .formulario-cabecera {
      background: linear-gradient(120deg, var(--color-primario), var(--color-secundario));
      color: white;
      padding: 20px 25px;
      display: flex;
      align-items: center;
    }
    .formulario-cabecera h3 {
      margin: 0;
      font-size: 20px;
      font-weight: 600;
      display: flex;
      align-items: center;
      letter-spacing: 0.5px;
    }
    .formulario-cabecera h3 i {
      margin-right: 12px;
      font-size: 22px;
    }
    .formulario {
      padding: 25px;
    }
    .campo-grupo {
      margin-bottom: 20px;
    }
    .campo-grupo label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: var(--color-primario);
    }
    .campo-input {
      width: 100%;
      padding: 12px 15px;
      border: 1px solid var(--color-borde);
      border-radius: 8px;
      font-size: 16px;
      transition: var(--transicion);
    }
    .campo-input:focus {
      outline: none;
      border-color: var(--color-acento);
      box-shadow: 0 0 0 3px rgba(165, 105, 189, 0.2);
    }
    .campo-fecha {
      background-color: #f9f9f9;
      cursor: not-allowed;
    }
    .boton-guardar {
      background: linear-gradient(120deg, var(--color-primario), var(--color-secundario));
      color: white;
      border: none;
      border-radius: 8px;
      padding: 14px 25px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: var(--transicion);
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }
    .boton-guardar:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }
    .boton-guardar i {
      margin-right: 10px;
    }
    .boton-reset {
      background-color: #f5f5f5;
      color: var(--color-texto-oscuro);
      border: 1px solid var(--color-borde);
      border-radius: 8px;
      padding: 14px 25px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: var(--transicion);
      margin-left: 15px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }
    .boton-reset:hover {
      background-color: #e9e9e9;
    }
    .boton-reset i {
      margin-right: 10px;
    }
    
    /* Tabla de sectores */
    .tabla-contenedor {
      background-color: white;
      border-radius: var(--borde-radio);
      box-shadow: var(--sombra);
      overflow: hidden;
      margin-bottom: 30px;
      border: 1px solid rgba(165, 105, 189, 0.1);
    }
    .tabla-cabecera {
      background: linear-gradient(120deg, var(--color-primario), var(--color-secundario));
      color: white;
      padding: 20px 25px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .tabla-cabecera h3 {
      margin: 0;
      font-size: 20px;
      font-weight: 600;
      display: flex;
      align-items: center;
      letter-spacing: 0.5px;
    }
    .tabla-cabecera h3 i {
      margin-right: 12px;
      font-size: 22px;
    }
    .buscador {
      display: flex;
      align-items: center;
      background-color: rgba(255, 255, 255, 0.15);
      border-radius: 25px;
      padding: 8px 18px;
      transition: var(--transicion);
      border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .buscador:hover {
      background-color: rgba(255, 255, 255, 0.25);
    }
    .buscador input {
      background: none;
      border: none;
      color: white;
      padding: 5px;
      outline: none;
      font-size: 15px;
      width: 200px;
    }
    .buscador input::placeholder {
      color: rgba(255, 255, 255, 0.7);
    }
    .buscador i {
      margin-right: 10px;
      font-size: 16px;
    }
    .tabla-sectores {
      width: 100%;
      border-collapse: collapse;
    }
    .tabla-sectores th,
    .tabla-sectores td {
      padding: 16px 20px;
      text-align: left;
      border-bottom: 1px solid var(--color-borde);
    }
    .tabla-sectores th {
      background-color: #faf7fb;
      font-weight: 600;
      color: var(--color-primario);
      letter-spacing: 0.5px;
    }
    .tabla-sectores tr:hover {
      background-color: rgba(210, 180, 222, 0.1);
    }
    .acciones {
      display: flex;
      gap: 12px;
      justify-content: center;
    }
    .boton-accion {
      width: 38px;
      height: 38px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      border: none;
      cursor: pointer;
      transition: var(--transicion);
      color: white;
    }
    .boton-editar {
      background-color: var(--color-secundario);
    }
    .boton-editar:hover {
      background-color: var(--color-primario);
      transform: translateY(-3px);
      box-shadow: 0 5px 15px rgba(125, 60, 152, 0.3);
    }
    .boton-eliminar {
      background-color: #e74c3c;
    }
    .boton-eliminar:hover {
      background-color: #c0392b;
      transform: translateY(-3px);
      box-shadow: 0 5px 15px rgba(231, 76, 60, 0.3);
    }
    
    /* Estilo para modales */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0, 0, 0, 0.5);
    }
    .modal-contenido {
      background-color: white;
      margin: 10% auto;
      padding: 0;
      width: 500px;
      max-width: 90%;
      border-radius: var(--borde-radio);
      box-shadow: var(--sombra);
      animation: modalAnimation 0.3s ease;
    }
    @keyframes modalAnimation {
      from {opacity: 0; transform: translateY(-50px);}
      to {opacity: 1; transform: translateY(0);}
    }
    .modal-cabecera {
      background: linear-gradient(120deg, var(--color-primario), var(--color-secundario));
      color: white;
      padding: 20px 25px;
      border-top-left-radius: var(--borde-radio);
      border-top-right-radius: var(--borde-radio);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .modal-cabecera h4 {
      margin: 0;
      font-size: 20px;
      font-weight: 600;
      display: flex;
      align-items: center;
    }
    .modal-cabecera h4 i {
      margin-right: 12px;
      font-size: 22px;
    }
    .cerrar-modal {
      color: white;
      font-size: 28px;
      cursor: pointer;
      transition: var(--transicion);
    }
    .cerrar-modal:hover {
      opacity: 0.7;
    }
    .modal-cuerpo {
      padding: 25px;
    }
    .modal-pie {
      padding: 15px 25px 25px;
      text-align: right;
      display: flex;
      justify-content: flex-end;
      gap: 15px;
    }
    .id-sector {
      font-weight: bold;
      color: var(--color-primario);
      background-color: rgba(165, 105, 189, 0.05);
      padding: 2px 8px;
      border-radius: 4px;
      display: inline-block;
    }
    .mensaje-alerta {
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-weight: 500;
    }
    .mensaje-error {
      background-color: #fdecea;
      color: #e74c3c;
      border-left: 4px solid #e74c3c;
    }
    .mensaje-exito {
      background-color: #e8f8f5;
      color: #27ae60;
      border-left: 4px solid #27ae60;
    }
    
    /* Estilos responsivos */
    @media (max-width: 768px) {
      .tabla-cabecera {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
      }
      .contenedor {
        padding: 20px 15px;
        padding-top: 70px;
      }
      .formulario {
        padding: 15px;
      }
      .tabla-contenedor {
        overflow-x: auto;
      }
      .icono-seccion {
        font-size: 24px;
        padding: 8px;
      }
      .titulo-pagina h1 {
        font-size: 24px;
      }
      .modal-contenido {
        margin: 20% auto;
      }
    }
  </style>
</head>
<body>
<?php include '../views/menuA.php'; ?>
  <!-- Barra superior -->
  <div class="contenedor">
    <div class="encabezado-pagina">
      <div class="titulo-pagina">
        <i class="fas fa-layer-group icono-seccion"></i>
        <h1>Gestión de Sectores</h1>
      </div>
    </div>
    
    <!-- Formulario de registro -->
    <div class="formulario-contenedor">
      <div class="formulario-cabecera">
        <h3><i class="fas fa-plus-circle"></i> Registrar Nuevo Sector</h3>
      </div>
      <form id="formularioSector" class="formulario">
        <div class="campo-grupo">
          <label for="nombreSector">Nombre del Sector</label>
          <input type="text" id="nombreSector" class="campo-input" placeholder="Ingrese el nombre del sector" required>
        </div>
        <div class="campo-grupo">
          <label for="descripcionSector">Descripción</label>
          <input type="text" id="descripcionSector" class="campo-input" placeholder="Ingrese la descripción del sector" required>
        </div>
        <div class="campo-grupo">
          <label for="fechaRegistro">Fecha de Registro</label>
          <input type="text" id="fechaRegistro" class="campo-input campo-fecha" readonly>
        </div>
        <div>
          <button type="submit" class="boton-guardar"><i class="fas fa-save"></i> Guardar Sector</button>
          <button type="reset" class="boton-reset"><i class="fas fa-sync-alt"></i> Limpiar</button>
        </div>
      </form>
    </div>
    
    <!-- Tabla de sectores -->
    <div class="tabla-contenedor">
      <div class="tabla-cabecera">
        <h3><i class="fas fa-table"></i> Listado de Sectores</h3>
        <div class="buscador">
          <i class="fas fa-search"></i>
          <input type="text" id="buscadorSector" placeholder="Buscar sector...">
        </div>
      </div>
      <table class="tabla-sectores">
        <thead>
          <tr>
            <th>ID</th>
            <th>Fecha</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody id="tablaSectores">
          <!-- Los registros se cargarán dinámicamente -->
        </tbody>
      </table>
    </div>
  </div>
  
  <!-- Modal Editar Sector -->
  <div id="modalEditar" class="modal">
    <div class="modal-contenido">
      <div class="modal-cabecera">
        <h4><i class="fas fa-edit"></i> Editar Sector</h4>
        <span class="cerrar-modal" id="cerrarModalEditar">&times;</span>
      </div>
      <form id="formularioSector" name="formularioSector" class="formulario">
        <div class="campo-grupo">
          <label for="nombreSector">Nombre del Sector</label>
          <input type="text" id="nombreSector" name="nombreSector" class="campo-input" placeholder="Ingrese el nombre del sector" required>
        </div>
        <div class="campo-grupo">
          <label for="descripcionSector">Descripción</label>
          <input type="text" id="descripcionSector" name="descripcionSector" class="campo-input" placeholder="Ingrese la descripción del sector" required>
        </div>
        <div class="campo-grupo">
          <label for="fechaRegistro">Fecha de Registro</label>
          <input type="text" id="fechaRegistro" name="fechaRegistro" class="campo-input campo-fecha" readonly>
        </div>
        <div>
          <button type="submit" class="boton-guardar" value="registrarSector" name="registrarSector"><i class="fas fa-save"></i> Guardar Sector</button>
          <button type="reset" class="boton-reset"><i class="fas fa-sync-alt"></i> Limpiar</button>
        </div>
      </form>

      <div class="modal-pie">
        <button type="button" class="boton-reset" id="cancelarEditar"><i class="fas fa-times"></i> Cancelar</button>
        <button type="button" class="boton-guardar" id="guardarEditar"><i class="fas fa-save"></i> Guardar Cambios</button>
      </div>
    </div>
  </div>
  
  <!-- Modal Eliminar Sector -->
  <div id="modalEliminar" class="modal">
    <div class="modal-contenido">
      <div class="modal-cabecera">
        <h4><i class="fas fa-trash-alt"></i> Eliminar Sector</h4>
        <span class="cerrar-modal" id="cerrarModalEliminar">&times;</span>
      </div>
      <div class="modal-cuerpo">
        <p>¿Está seguro que desea eliminar el sector <span id="nombreSectorEliminar" class="id-sector"></span>?</p>
        <p>Esta acción no se puede deshacer.</p>
        <input type="hidden" id="eliminarId">
      </div>
      <div class="modal-pie">
        <button type="button" class="boton-reset" id="cancelarEliminar"><i class="fas fa-times"></i> Cancelar</button>
        <button type="button" class="boton-eliminar" id="confirmarEliminar"><i class="fas fa-trash-alt"></i> Eliminar</button>
      </div>
    </div>
  </div>
  
  <script>
    // Datos de ejemplo para iniciar la tabla
    /*[
    let sectores = <?php echo $sectoresJSON; ?>
      { id: 1, fecha: '2025-04-09', nombre: 'Sector Norte', descripcion: 'Área de producción norte' },
      { id: 2, fecha: '2025-04-08', nombre: 'Sector Sur', descripcion: 'Área de producción sur' },
      { id: 3, fecha: '2025-04-07', nombre: 'Sector Este', descripcion: 'Área de distribución este' }
    ];*/
    
    // Función para formatear fecha (YYYY-MM-DD)
    function formatearFecha(fecha) {
      const año = fecha.getFullYear();
      const mes = String(fecha.getMonth() + 1).padStart(2, '0');
      const dia = String(fecha.getDate()).padStart(2, '0');
      return `${año}-${mes}-${dia}`;
    }
    
    // Formatear fecha para mostrar
    function formatearFechaLegible(fechaStr) {
      const [year, month, day] = fechaStr.split('-');
      const fecha = new Date(Date.UTC(year, month - 1, day));
      
      const opciones = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        timeZone: 'UTC'
      };
      
      return fecha.toLocaleDateString('es-ES', opciones)
        .replace(/^\w/, (c) => c.toUpperCase());
    }
    
    // Función para actualizar la tabla de sectores
    function actualizarTablaSectores(sectoresFiltrados = null) {
      const tablaSectores = document.getElementById('tablaSectores');
      tablaSectores.innerHTML = '';
      
      const sectoresAMostrar = sectoresFiltrados || sectores;
      
      if (sectoresAMostrar.length === 0) {
        tablaSectores.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 30px;">No se encontraron sectores</td></tr>';
        return;
      }
      
      sectoresAMostrar.forEach(sector => {
        const fila = document.createElement('tr');
        fila.innerHTML = `
          <td><span class="id-sector">${sector.id}</span></td>
          <td>${formatearFechaLegible(sector.fecha)}</td>
          <td>${sector.nombre}</td>
          <td>${sector.descripcion}</td>
          <td class="acciones">
            <button class="boton-accion boton-editar" onclick="abrirModalEditar(${sector.id})">
              <i class="fas fa-edit"></i>
            </button>
            <button class="boton-accion boton-eliminar" onclick="abrirModalEliminar(${sector.id})">
              <i class="fas fa-trash-alt"></i>
            </button>
          </td>
        `;
        tablaSectores.appendChild(fila);
      });
    }
    
    // Función para buscar sectores
    function buscarSectores(termino) {
      if (!termino.trim()) {
        actualizarTablaSectores();
        return;
      }
      
      const terminoLower = termino.toLowerCase();
      const sectoresFiltrados = sectores.filter(sector => 
        sector.nombre.toLowerCase().includes(terminoLower) || 
        sector.descripcion.toLowerCase().includes(terminoLower)
      );
      
      actualizarTablaSectores(sectoresFiltrados);
    }
    
    // Funciones para los modales
    function abrirModalEditar(id) {
      const sector = sectores.find(s => s.id === id);
      if (!sector) return;
      
      document.getElementById('editarId').value = sector.id;
      document.getElementById('editarNombre').value = sector.nombre;
      document.getElementById('editarDescripcion').value = sector.descripcion;
      document.getElementById('editarFecha').value = sector.fecha;
      
      document.getElementById('modalEditar').style.display = 'block';
    }
    
    function cerrarModalEditar() {
      document.getElementById('modalEditar').style.display = 'none';
    }
    
    function abrirModalEliminar(id) {
      const sector = sectores.find(s => s.id === id);
      if (!sector) return;
      
      document.getElementById('eliminarId').value = sector.id;
      document.getElementById('nombreSectorEliminar').textContent = sector.nombre;
      
      document.getElementById('modalEliminar').style.display = 'block';
    }
    
    function cerrarModalEliminar() {
      document.getElementById('modalEliminar').style.display = 'none';
    }
    
    // Función para guardar un nuevo sector
    /*function guardarSector(event) {
      event.preventDefault();
      
      const nombre = document.getElementById('nombreSector').value.trim();
      const descripcion = document.getElementById('descripcionSector').value.trim();
      const fecha = document.getElementById('fechaRegistro').value;
      
      if (!nombre || !descripcion) {
        alert('Por favor complete todos los campos');
        return;
      }
      
      // Generar ID (en una aplicación real, esto vendría del backend)
      const nuevoId = sectores.length > 0 ? Math.max(...sectores.map(s => s.id)) + 1 : 1;
      
      // Crear nuevo sector
      const nuevoSector = {
        id: nuevoId,
        fecha: fecha,
        nombre: nombre,
        descripcion: descripcion
      };
      
      // Agregar a la lista
      sectores.unshift(nuevoSector);
      
      // Actualizar tabla
      actualizarTablaSectores();
      
      // Limpiar formulario
      document.getElementById('formularioSector').reset();
      actualizarFechaActual();
      
      // Mostrar mensaje de éxito (podría implementarse con una notificación)
      alert('Sector guardado con éxito');
    }*/
    
    // Función para editar sector
    function editarSector() {
      const id = parseInt(document.getElementById('editarId').value);
      const nombre = document.getElementById('editarNombre').value.trim();
      const descripcion = document.getElementById('editarDescripcion').value.trim();
      
      if (!nombre || !descripcion) {
        alert('Por favor complete todos los campos');
        return;
      }
      
      // Encontrar y actualizar el sector
      const index = sectores.findIndex(s => s.id === id);
      if (index === -1) return;
      
      sectores[index].nombre = nombre;
      sectores[index].descripcion = descripcion;
      
      // Actualizar tabla
      actualizarTablaSectores();
      
      // Cerrar modal
      cerrarModalEditar();
      
      // Mostrar mensaje de éxito (podría implementarse con una notificación)
      alert('Sector actualizado con éxito');
    }
    
    // Función para eliminar sector
    function eliminarSector() {
      const id = parseInt(document.getElementById('eliminarId').value);
      
      // Filtrar la lista para eliminar el sector
      sectores = sectores.filter(s => s.id !== id);
      
      // Actualizar tabla
      actualizarTablaSectores();
      
      // Cerrar modal
      cerrarModalEliminar();
      
      // Mostrar mensaje de éxito
      alert('Sector eliminado con éxito');
    }
    
    // Función para actualizar la fecha actual en el formulario
    function actualizarFechaActual() {
      const fechaActual = new Date();
      document.getElementById('fechaRegistro').value = formatearFecha(fechaActual);
    }
    
    // Eventos del DOM
    document.addEventListener('DOMContentLoaded', function() {
      // Inicializar fecha actual
      actualizarFechaActual();
      
      // Cargar tabla inicial
      actualizarTablaSectores();
      
      // Eventos del formulario principal

      
      // Eventos del buscador
      document.getElementById('buscadorSector').addEventListener('input', function() {
        buscarSectores(this.value);
      });
      
      // Eventos del modal de editar
      document.getElementById('cerrarModalEditar').addEventListener('click', cerrarModalEditar);
      document.getElementById('cancelarEditar').addEventListener('click', cerrarModalEditar);
      document.getElementById('guardarEditar').addEventListener('click', editarSector);
      
      // Eventos del modal de eliminar
      document.getElementById('cerrarModalEliminar').addEventListener('click', cerrarModalEliminar);
      document.getElementById('cancelarEliminar').addEventListener('click', cerrarModalEliminar);
      document.getElementById('confirmarEliminar').addEventListener('click', eliminarSector);
    });
    
    // Cerrar modales si se hace clic fuera de ellos
    window.addEventListener('click', function(event) {
      const modalEditar = document.getElementById('modalEditar');
      const modalEliminar = document.getElementById('modalEliminar');
      
      if (event.target === modalEditar) {
        cerrarModalEditar();
      }
      
      if (event.target === modalEliminar) {
        cerrarModalEliminar();
      }
    });
  </script>
</body>
</html>