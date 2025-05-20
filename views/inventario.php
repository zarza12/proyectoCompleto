<?php 
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

include_once  '../controllers/daoInventario.php';
include_once  '../models/Inventario.php';

$daoInventario = new daoInventario();
$listaTotales = $daoInventario->obtenerTotalesInventario();

// Crear un array para los datos de JavaScript
$inventarioTotalesJS = [];
foreach ($listaTotales as $inventario) {
    $inventarioTotalesJS[] = [
        'fecha' => $inventario['fecha'],
        'totalCajas' =>(int)$inventario['totalCajas'],
        'exportacion' => (int)$inventario['totalExportacion'],
        'nacional' => (int)$inventario['totalNacional'],
        'desecho' => (int)$inventario['totalDesecho']
    ];
}

// Convertir a JSON para JavaScript
$totalInventarioJSON = json_encode($inventarioTotalesJS);


$listaVentas = $daoInventario->obtenerVentasInventario();
$ventasInventarioJS = [];
foreach ($listaVentas as $venta) {
    $ventasInventarioJS[] = [
        'fecha' => $venta['fecha'],
        'exportacion' => (int)$venta['ventaExportacion'],
        'nacional' => (int)$venta['ventaNacional'],
        'desecho' => (int)$venta['ventaDesecho']
    ];
}



?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro de Inventario</title>
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
    /* Tabla de registros */
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
    .filtro-fecha {
      display: flex;
      align-items: center;
      background-color: rgba(255, 255, 255, 0.15);
      border-radius: 25px;
      padding: 8px 18px;
      transition: var(--transicion);
      border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .filtro-fecha:hover {
      background-color: rgba(255, 255, 255, 0.25);
    }
    .filtro-fecha input {
      background: none;
      border: none;
      color: white;
      padding: 5px;
      outline: none;
      font-size: 15px;
    }
    .filtro-fecha input::placeholder {
      color: rgba(255, 255, 255, 0.7);
    }
    .filtro-fecha i {
      margin-right: 10px;
      font-size: 16px;
    }
    .filtro-tiempo {
      background-color: rgba(255, 255, 255, 0.15);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 25px;
      padding: 8px 18px;
      color: white;
      outline: none;
      cursor: pointer;
      font-size: 15px;
      transition: var(--transicion);
    }
    .filtro-tiempo:hover {
      background-color: rgba(255, 255, 255, 0.25);
    }
    .filtro-tiempo option {
      background-color: var(--color-primario);
      color: white;
    }
    .tabla-registros {
      width: 100%;
      border-collapse: collapse;
    }
    .tabla-registros th,
    .tabla-registros td {
      padding: 18px 20px;
      text-align: left;
      border-bottom: 1px solid var(--color-borde);
    }
    .tabla-registros th {
      background-color: #faf7fb;
      font-weight: 600;
      color: var(--color-primario);
      letter-spacing: 0.5px;
    }
    .tabla-registros tr:hover {
      background-color: rgba(210, 180, 222, 0.1);
    }
    .fecha-encabezado {
      background-color: var(--color-resalte);
      font-weight: bold;
    }
    .fecha-encabezado td {
      font-size: 16px;
      color: var(--color-primario);
      padding: 15px 20px;
    }
    .fecha-encabezado td i {
      margin-right: 12px;
    }
    .fecha-actual {
      background: linear-gradient(120deg, rgba(165, 105, 189, 0.3), rgba(210, 180, 222, 0.3));
    }
    .fila-restante {
      background-color: #F9F9F9;
      color: var(--color-secundario);
      font-style: italic;
    }
    .fila-restante td {
      border-top: 1px dashed var(--color-borde);
    }
    .fila-restante i {
      margin-right: 8px;
      opacity: 0.8;
    }
    .fila-totales {
      font-weight: bold;
      background-color: rgba(250, 250, 250, 0.7);
    }
    .fila-totales td {
      border-bottom: 2px solid var(--color-borde);
    }
    .valor-destacado {
      font-weight: bold;
      color: var(--color-primario);
      background-color: rgba(165, 105, 189, 0.05);
      padding: 2px 8px;
      border-radius: 4px;
      display: inline-block;
    }
    .fila-espacio {
      height: 15px;
      background-color: var(--color-fondo);
    }
    .fila-espacio td {
      border: none;
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
      .tabla-contenedor {
        overflow-x: auto;
        margin: 0 -15px;
        border-radius: 0;
      }
      .icono-seccion {
        font-size: 24px;
        padding: 8px;
      }
      .titulo-pagina h1 {
        font-size: 24px;
      }
    }
  </style>
</head>
<body>
  <?php include '../views/menuA.php'; ?>
  <!-- Barra superior -->
  <div class="barra-superior"></div>
  <div class="contenedor">
    <div class="encabezado-pagina">
      <div class="titulo-pagina">
        <i class="fas fa-boxes-stacked icono-seccion"></i>
        <h1>Registro de Inventario</h1>
      </div>
    </div>
    <!-- Tabla de inventario -->
    <div class="tabla-contenedor">
      <div class="tabla-cabecera">
        <h3><i class="fas fa-list"></i> Registro de Inventario</h3>
        <div style="display: flex; gap: 15px;">
          <div class="filtro-fecha">
            <i class="fas fa-calendar"></i>
            <input type="date" id="selectorFecha" placeholder="Filtrar por fecha">
          </div>
          <select id="filtroTiempo" class="filtro-tiempo">
            <option value="todos">Todos los registros</option>
            <option value="mes">Mes actual</option>
            <option value="semana" style="display: none;" >Semana actual</option>
            <option value="hoy" selected>Hoy</option>
          </select>
        </div>
      </div>
      <table class="tabla-registros">
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Total Cajas</th>
            <th>Exportación</th>
            <th>Nacional</th>
            <th>Desecho</th>
          </tr>
        </thead>
        <tbody id="tablaInventario">
          <!-- Se cargarán los registros agrupados -->
        </tbody>
      </table>
    </div>
  </div>
  
  <script>
    // Array con datos planeados (lo que se esperaba entregar)
    const inventarioData = <?php echo $totalInventarioJSON; ?>;
    console.log(inventarioData);
  /* const inventarioData = [
      { fecha: '2025-04-21', totalCajas: 3, exportacion: 1, nacional: 1, desecho: 1 },
      { fecha: '2025-04-21', totalCajas: 3, exportacion: 1, nacional: 1, desecho: 1 },
      { fecha: '2025-04-21', totalCajas: 3, exportacion: 1, nacional: 1, desecho: 1 },
      { fecha: '2025-04-04', totalCajas: 100, exportacion: 70, nacional: 15, desecho: 15 },
      { fecha: '2025-04-07', totalCajas: 180, exportacion: 130, nacional: 30, desecho: 20 },
      { fecha: '2025-03-03', totalCajas: 120, exportacion: 90, nacional: 20, desecho: 10 }
    ];*/
    
    // Array con las ventas realizadas (lo entregado)
    const ventasRealizadas =<?php echo json_encode($ventasInventarioJS); ?>;
    console.log(ventasRealizadas);
    /*const ventasRealizadas = [
      { fecha: '2025-04-05', exportacion: 1, nacional: 20, desecho: 1 },
      { fecha: '2025-04-04', exportacion: 200, nacional: 40, desecho: 20 },
      { fecha: '2025-04-03', exportacion: 120, nacional: 30, desecho: 15 },
      { fecha: '2025-03-03', exportacion: 80, nacional: 10, desecho: 5 }
    ];*/
    
    // Obtener la fecha actual
    const hoy = new Date();
    const fechaHoy = formatearFecha(hoy);
    
    // Eventos para filtrar por fecha o período
    document.getElementById('selectorFecha').addEventListener('change', function() {
      filtrarPorFecha(this.value);
    });
    
    document.getElementById('filtroTiempo').addEventListener('change', function() {
      const opcionSeleccionada = this.value;
      if (opcionSeleccionada !== 'todos') {
        document.getElementById('selectorFecha').value = '';
      }
      filtrarPorPeriodo(opcionSeleccionada);
    });
    
    // Función para obtener las ventas realizadas para una fecha (suma si hay varios registros)
    function obtenerVentasRealizadas(fecha) {
      const registros = ventasRealizadas.filter(item => item.fecha === fecha);
      let sumaExportacion = 0, sumaNacional = 0, sumaDesecho = 0;
      registros.forEach(item => {
        sumaExportacion += item.exportacion;
        sumaNacional += item.nacional;
        sumaDesecho += item.desecho;
      });
      return { exportacion: sumaExportacion, nacional: sumaNacional, desecho: sumaDesecho };
    }
    
    // Filtrar por fecha específica
    function filtrarPorFecha(fecha) {
      if (!fecha) {
        mostrarTodosLosRegistros();
        return;
      }
      document.getElementById('filtroTiempo').value = 'todos';
      const registrosFiltrados = inventarioData.filter(item => item.fecha === fecha);
      actualizarTablaConEncabezadosFecha(registrosFiltrados);
    }
    
    // Filtrar por período (hoy, semana, mes o todos)
    function filtrarPorPeriodo(periodo) {
      const hoy = new Date();
      let registrosFiltrados = [];
      switch(periodo) {
        case 'todos':
          mostrarTodosLosRegistros();
          return;
        case 'hoy':
          const fechaHoy = formatearFecha(hoy);
          registrosFiltrados = inventarioData.filter(item => item.fecha === fechaHoy);
          actualizarTablaConEncabezadosFecha(registrosFiltrados);
          break;
        case 'semana':
          const primerDiaSemana = new Date(hoy);
          const diaSemana = hoy.getDay();
          const diferenciaDias = diaSemana === 0 ? 6 : diaSemana - 1;
          primerDiaSemana.setDate(hoy.getDate() - diferenciaDias);
          registrosFiltrados = inventarioData.filter(item => {
            const fechaRegistro = new Date(item.fecha);
            return fechaRegistro >= primerDiaSemana && fechaRegistro <= hoy;
          });
          actualizarTablaConEncabezadosFecha(registrosFiltrados);
          break;
        case 'mes':
          const mesActual = hoy.getMonth();
          const añoActual = hoy.getFullYear();
          registrosFiltrados = inventarioData.filter(item => {
            const fechaRegistro = new Date(item.fecha);
            return fechaRegistro.getMonth() === mesActual && fechaRegistro.getFullYear() === añoActual;
          });
          actualizarTablaConEncabezadosFecha(registrosFiltrados);
          break;
      }
    }
    
    // Mostrar todos los registros
    function mostrarTodosLosRegistros() {
      actualizarTablaConEncabezadosFecha(inventarioData);
    }
    
    // Actualizar la tabla agrupando los registros por fecha y mostrando los totales planeados y lo entregado
    function actualizarTablaConEncabezadosFecha(registros) {
      const tablaBody = document.getElementById('tablaInventario');
      tablaBody.innerHTML = '';
      if (registros.length === 0) {
        const fila = document.createElement('tr');
        fila.innerHTML = '<td colspan="5" style="text-align: center; padding: 30px;">No se encontraron registros para este período</td>';
        tablaBody.appendChild(fila);
        return;
      }
      
      // Agrupar por fecha
      const registrosPorFecha = agruparPorFecha(registros);
      const fechasOrdenadas = Object.keys(registrosPorFecha).sort().reverse();
      
      fechasOrdenadas.forEach((fecha, index) => {
        // Fila de encabezado con la fecha
        const filaFecha = document.createElement('tr');
        filaFecha.className = 'fecha-encabezado';
        const esHoy = fecha === fechaHoy;
        if (esHoy) { filaFecha.classList.add('fecha-actual'); }
        const fechaFormateada = formatearFechaLegible(fecha);
        filaFecha.innerHTML = `
          <td colspan="5">
            <i class="fas fa-calendar-day"></i> 
            ${fechaFormateada}
            ${esHoy ? '<span style="background-color: var(--color-acento); color: white; padding: 3px 8px; border-radius: 12px; font-size: 12px; margin-left: 10px;">HOY</span>' : ''}
          </td>
        `;
        tablaBody.appendChild(filaFecha);
        
        // Sumar los valores planeados de la fecha
        let totalCajas = 0, totalExportacion = 0, totalNacional = 0, totalDesecho = 0;
        registrosPorFecha[fecha].forEach(item => {
          totalCajas += item.totalCajas;
          totalExportacion += item.exportacion;
          totalNacional += item.nacional;
          totalDesecho += item.desecho;
        });
        
        // Mostrar fila con totales planeados
        const filaTotales = document.createElement('tr');
        filaTotales.className = 'fila-totales';
        filaTotales.innerHTML = `
          <td>${fecha}</td>
          <td><span class="valor-destacado">${totalCajas}</span></td>
          <td><span class="valor-destacado">${totalExportacion}</span></td>
          <td><span class="valor-destacado">${totalNacional}</span></td>
          <td><span class="valor-destacado">${totalDesecho}</span></td>
        `;
        tablaBody.appendChild(filaTotales);
        
        // Obtener las ventas realizadas para esa fecha
        const ventas = obtenerVentasRealizadas(fecha);
        // Calcular lo pendiente (lo que falta por entregar) restando las ventas realizadas de lo planeado
        const pendienteTotal = totalCajas - (ventas.exportacion + ventas.nacional + ventas.desecho);
        const pendienteExportacion = totalExportacion - ventas.exportacion;
        const pendienteNacional = totalNacional - ventas.nacional;
        const pendienteDesecho = totalDesecho - ventas.desecho;
        
        // Mostrar fila con lo pendiente por entregar
        const filaPendientes = document.createElement('tr');
        filaPendientes.className = 'fila-restante';
        filaPendientes.innerHTML = `
          <td><i class="fas fa-clock"></i> Pendiente por entregar</td>
          <td>${pendienteTotal}</td>
          <td>${pendienteExportacion}</td>
          <td>${pendienteNacional}</td>
          <td>${pendienteDesecho}</td>
        `;
        tablaBody.appendChild(filaPendientes);
        
        // Espacio entre fechas
        if (index < fechasOrdenadas.length - 1) {
          const filaEspacio = document.createElement('tr');
          filaEspacio.className = 'fila-espacio';
          filaEspacio.innerHTML = '<td colspan="5"></td>';
          tablaBody.appendChild(filaEspacio);
        }
      });
    }
    
    // Función para agrupar registros por fecha
    function agruparPorFecha(registros) {
      const grupos = {};
      registros.forEach(registro => {
        if (!grupos[registro.fecha]) {
          grupos[registro.fecha] = [];
        }
        grupos[registro.fecha].push(registro);
      });
      return grupos;
    }
    
    // Función auxiliar para formatear fecha (YYYY-MM-DD)
    function formatearFecha(fecha) {
      const año = fecha.getFullYear();
      const mes = String(fecha.getMonth() + 1).padStart(2, '0');
      const dia = String(fecha.getDate()).padStart(2, '0');
      return `${año}-${mes}-${dia}`;
    }
    
    // Usar Date.UTC para forzar el día correcto en cualquier zona horaria
    function formatearFechaLegible(fechaStr) {
      // "2025-04-07" -> year=2025, month=04, day=07
      const [year, month, day] = fechaStr.split('-');
      // Crear la fecha en UTC (mes en JS va de 0 a 11)
      const fecha = new Date(Date.UTC(year, month - 1, day));
      
      const opciones = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        timeZone: 'UTC'
      };
      // Convertir a cadena legible en español
      return fecha.toLocaleDateString('es-ES', opciones)
                  .replace(/^\w/, (c) => c.toUpperCase());
    }
    
    // Inicializar la tabla con registros de hoy
    document.addEventListener('DOMContentLoaded', function() {
      filtrarPorPeriodo('hoy');
    });
  </script>
</body>
</html>
