<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}



include_once  '../controllers/daoInventario.php';
include_once  '../models/Inventario.php';

$daoInventario = new daoInventario();
$listaTotales = $daoInventario->obtenerTotalesInventario();

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
  <title>Registro de Ventas Realizadas</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/regression@2.0.1/dist/regression.min.js"></script>
  <style>
    :root {
      --color-primario: #4A235A;
      --color-secundario: #7D3C98;
      --color-acento: #A569BD;
      --color-resalte: #D2B4DE;
      --color-texto: #FFFFFF;
      --color-texto-oscuro: #333333;
      --color-borde: #E0E0E0;
      --color-fondo: #F5F5F5;
      --sombra: 0 8px 20px rgba(0, 0, 0, 0.1);
      --sombra-suave: 0 4px 10px rgba(0, 0, 0, 0.05);
      --borde-radio: 12px;
      --transicion: all 0.3s ease;
    }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: var(--color-fondo);
      color: var(--color-texto-oscuro);
      line-height: 1.6;
    }
    .barra-superior {
      position: fixed; top: 0; left: 0; right: 0; height: 6px;
      background: linear-gradient(90deg, var(--color-primario), var(--color-acento), var(--color-secundario));
      z-index: 1000; animation: gradientAnimation 6s ease infinite;
      background-size: 200% 200%;
    }
    @keyframes gradientAnimation {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }
    .info-usuario {
      position: fixed; top: 20px; right: 30px; display: flex; align-items: center;
      background-color: var(--color-primario); padding: 8px 18px; border-radius: 25px;
      color: var(--color-texto); font-size: 15px; box-shadow: var(--sombra); z-index: 990;
      transition: var(--transicion);
    }
    .info-usuario:hover {
      background-color: var(--color-secundario);
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }
    .avatar-usuario {
      width: 32px; height: 32px; background-color: var(--color-acento); border-radius: 50%;
      display: flex; align-items: center; justify-content: center; margin-right: 12px;
      font-weight: bold; box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.2);
    }
    .contenedor {
      max-width: 1200px; margin: 0 auto; padding: 40px 25px; padding-top: 70px;
    }
    .encabezado-pagina {
      display: flex; justify-content: space-between; align-items: center;
      margin-bottom: 35px; border-bottom: 2px solid var(--color-resalte); padding-bottom: 20px;
    }
    .titulo-pagina { display: flex; align-items: center; }
    .titulo-pagina h1 {
      color: var(--color-primario); font-size: 32px; margin-right: 15px; font-weight: 700;
      letter-spacing: 0.5px;
    }
    .icono-seccion {
      font-size: 30px; color: var(--color-acento); margin-right: 18px;
      background-color: rgba(165, 105, 189, 0.1); padding: 12px; border-radius: 12px;
    }
    .filtros-contenedor {
      display: flex; flex-wrap: wrap; gap: 15px;
      background: linear-gradient(120deg, var(--color-primario), var(--color-secundario));
      padding: 20px; border-radius: var(--borde-radio); margin-bottom: 25px; box-shadow: var(--sombra);
    }
    .filtro-grupo {
      display: flex; flex-direction: column; flex: 1; min-width: 150px;
    }
    .filtro-label {
      color: var(--color-texto); margin-bottom: 8px; font-weight: 600; font-size: 14px;
    }
    .filtro-select {
      background-color: rgba(255, 255, 255, 0.15);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 8px; padding: 10px 15px; color: white; outline: none; cursor: pointer;
      transition: var(--transicion);
    }
    .filtro-select:hover, .filtro-select:focus {
      background-color: rgba(255, 255, 255, 0.25);
    }
    .filtro-select option {
      background-color: var(--color-primario); color: white;
    }
    .filtro-fecha {
      background-color: rgba(255, 255, 255, 0.15);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 8px; padding: 9px 15px; color: white; outline: none;
    }
    .filtro-fecha:hover, .filtro-fecha:focus {
      background-color: rgba(255, 255, 255, 0.25);
    }
    .fila-stats { display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 25px; }
    .tarjeta-stat {
      flex: 1; min-width: 200px; background: white; border-radius: var(--borde-radio);
      padding: 20px; box-shadow: var(--sombra-suave); border-left: 5px solid var(--color-acento);
      transition: var(--transicion);
    }
    .tarjeta-stat:hover { transform: translateY(-5px); box-shadow: var(--sombra); }
    .tarjeta-stat.exportacion { border-left-color: #8E44AD; }
    .tarjeta-stat.nacional { border-left-color: #3498DB; }
    .tarjeta-stat.desecho { border-left-color: #E74C3C; }
    .tarjeta-stat.total { border-left-color: #2ECC71; }
    .stat-titulo { font-size: 14px; color: #777; margin-bottom: 5px; display: flex; align-items: center; }
    .stat-titulo i { margin-right: 8px; color: var(--color-secundario); }
    .stat-valor { font-size: 28px; font-weight: 700; color: var(--color-primario); }
    .fila-graficos { display: flex; flex-wrap: wrap; gap: 25px; margin-bottom: 30px; }
    .grafico-contenedor {
      flex: 1; min-width: 280px; background: white; border-radius: var(--borde-radio);
      padding: 20px; box-shadow: var(--sombra);
    }
    .grafico-cabecera {
      display: flex; justify-content: space-between; align-items: center;
      margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid var(--color-borde);
    }
    .grafico-titulo { font-size: 18px; font-weight: 600; color: var(--color-primario); }
    .grafico-controles { display: flex; gap: 10px; }
    .grafico-control {
      background-color: var(--color-fondo); border: 1px solid var(--color-borde);
      border-radius: 4px; padding: 5px 10px; font-size: 13px; cursor: pointer;
      transition: var(--transicion);
    }
    .grafico-control:hover, .grafico-control.activo {
      background-color: var(--color-resalte); color: var(--color-primario);
    }
    .canvas-container { position: relative; height: 300px; width: 100%; }
    .tabla-contenedor {
      background-color: white; border-radius: var(--borde-radio); box-shadow: var(--sombra);
      overflow: hidden; margin-bottom: 30px; border: 1px solid rgba(165, 105, 189, 0.1);
    }
    .tabla-cabecera {
      background: linear-gradient(120deg, var(--color-primario), var(--color-secundario));
      color: white; padding: 20px 25px; display: flex; justify-content: space-between;
      align-items: center;
    }
    .tabla-cabecera h3 {
      margin: 0; font-size: 20px; font-weight: 600; display: flex;
      align-items: center; letter-spacing: 0.5px;
    }
    .tabla-cabecera h3 i { margin-right: 12px; font-size: 22px; }
    .tabla-registros { width: 100%; border-collapse: collapse; }
    .tabla-registros th,
    .tabla-registros td {
      padding: 16px 20px; text-align: left; border-bottom: 1px solid var(--color-borde);
    }
    .tabla-registros th {
      background-color: #faf7fb; font-weight: 600; color: var(--color-primario);
      letter-spacing: 0.5px;
    }
    .tabla-registros tr:hover { background-color: rgba(210, 180, 222, 0.1); }
    .tabla-paginacion {
      display: flex; justify-content: space-between; align-items: center;
      padding: 15px 20px; background-color: #faf7fb; border-top: 1px solid var(--color-borde);
    }
    .pagina-info { color: #777; font-size: 14px; }
    .pagina-controles { display: flex; gap: 10px; }
    .pagina-boton {
      background-color: white; border: 1px solid var(--color-borde); border-radius: 4px;
      padding: 5px 12px; cursor: pointer; transition: var(--transicion);
    }
    .pagina-boton:hover, .pagina-boton.activo {
      background-color: var(--color-primario); color: white; border-color: var(--color-primario);
    }
    .pagina-boton.disabled { opacity: 0.5; cursor: not-allowed; }
    .badge {
      padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;
    }
    .badge-exportacion { background-color: rgba(142, 68, 173, 0.1); color: #8E44AD; }
    .badge-nacional { background-color: rgba(52, 152, 219, 0.1); color: #3498DB; }
    .badge-desecho { background-color: rgba(231, 76, 60, 0.1); color: #E74C3C; }
    @media (max-width: 768px) {
      .filtros-contenedor { flex-direction: column; }
      .contenedor { padding: 20px 15px; padding-top: 70px; }
      .tabla-contenedor { overflow-x: auto; margin: 0 -15px; width: calc(100% + 30px); }
      .icono-seccion { font-size: 24px; padding: 8px; }
      .titulo-pagina h1 { font-size: 24px; }
      .tabla-cabecera { flex-direction: column; gap: 15px; align-items: flex-start; }
    }
  </style>
</head>
<body>
<?php include '../views/menuA.php'; ?>
  <div class="contenedor">
    <div class="encabezado-pagina">
      <div class="titulo-pagina">
        <i class="fas fa-chart-line icono-seccion"></i>
        <h1>Ventas Realizadas</h1>
      </div>
    </div>
    
    <!-- Filtros superiores -->
    <div class="filtros-contenedor">
      <div class="filtro-grupo">
        <label class="filtro-label">Tipo de Filtro</label>
        <select id="tipoFiltro" class="filtro-select" style="display: none;">
          <option value="dia">Día</option>
          <option value="semana" selected>Semana</option>
          <option value="mes">Mes</option>
          <option value="anio">Año</option>
        </select>
      </div>
      <div class="filtro-grupo" style="display: none;">
        <label class="filtro-label">Periodo</label>
        <select id="periodoFiltro" class="filtro-select">
          <option value="actual" selected>Actual</option>
          <option value="anterior">Anterior</option>
          <option value="personalizado">Personalizado</option>
        </select>
      </div>
      <div class="filtro-grupo">
        <label class="filtro-label">Categoría</label>
        <select id="categoriaFiltro" class="filtro-select">
          <option value="todas" selected>Todas</option>
          <option value="exportacion">Exportación</option>
          <option value="nacional">Nacional</option>
          <option value="desecho">Desecho</option>
        </select>
      </div>
      <div class="filtro-grupo" id="fechaPersonalizada" style="display: none;">
        <label class="filtro-label">Fecha</label>
        <input type="date" class="filtro-fecha" id="fechaSeleccionada">
      </div>
    </div>
    
    <!-- Tarjetas de estadísticas -->
    <div class="fila-stats">
      <div class="tarjeta-stat total">
        <div class="stat-titulo">
          <i class="fas fa-boxes-stacked"></i> Total Ventas
        </div>
        <div class="stat-valor" id="totalVentas">0</div>
      </div>
      <div class="tarjeta-stat exportacion">
        <div class="stat-titulo">
          <i class="fas fa-ship"></i> Exportación
        </div>
        <div class="stat-valor" id="totalExportacion">0</div>
      </div>
      <div class="tarjeta-stat nacional">
        <div class="stat-titulo">
          <i class="fas fa-truck"></i> Nacional
        </div>
        <div class="stat-valor" id="totalNacional">0</div>
      </div>
      <div class="tarjeta-stat desecho">
        <div class="stat-titulo">
          <i class="fas fa-recycle"></i> Desecho
        </div>
        <div class="stat-valor" id="totalDesecho">0</div>
      </div>
    </div>
    
    <!-- Fila de gráficos -->
    <div class="fila-graficos">
      <!-- Gráfico de tendencia -->
      <div class="grafico-contenedor">
        <div class="grafico-cabecera">
          <div class="grafico-titulo">
            <i class="fas fa-chart-line"></i> Tendencia de Ventas
          </div>
          <div class="grafico-controles" style="display: none;">
            <button class="grafico-control activo" data-periodo="7">7D</button>
            <button class="grafico-control" data-periodo="14">14D</button>
            <button class="grafico-control" data-periodo="30">30D</button>
          </div>
        </div>
        <div class="canvas-container">
          <canvas id="graficoTendencia"></canvas>
        </div>
      </div>
      <!-- Gráfico de distribución -->
      <div class="grafico-contenedor">
        <div class="grafico-cabecera">
          <div class="grafico-titulo">
            <i class="fas fa-chart-pie"></i> Distribución por Categoría
          </div>
          <div class="grafico-controles">
            <button class="grafico-control activo" data-tipo="pie">Pie</button>
            <button class="grafico-control" data-tipo="bar">Barras</button>
          </div>
        </div>
        <div class="canvas-container">
          <canvas id="graficoDistribucion"></canvas>
        </div>
      </div>
    </div>
    
    <!-- Gráfico de predicción -->
    <div class="fila-graficos">
      <div class="grafico-contenedor" style="flex: 100%;">
        <div class="grafico-cabecera">
          <div class="grafico-titulo">
            <i class="fas fa-chart-line"></i> Predicción de Ventas (Machine Learning)
          </div>
          <div class="grafico-controles">
            <button class="grafico-control activo" data-prediccion="mes">1 Mes</button>
            <button class="grafico-control" data-prediccion="semana">1 Semana</button>
            <button class="grafico-control" data-prediccion="anio">1 Año</button>
          </div>
        </div>
        <div class="canvas-container">
          <canvas id="graficoPrediccion"></canvas>
        </div>
      </div>
    </div>
    
    <!-- Tabla de registros -->
    <div class="tabla-contenedor">
      <div class="tabla-cabecera">
        <h3><i class="fas fa-list"></i> Registro Detallado de Ventas</h3>
      </div>
      <table class="tabla-registros">
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Categoría</th>
            <th>Cantidad</th>
            <th>% del Total</th>
            <th>ID</th>
          </tr>
        </thead>
        <tbody id="tablaVentas">
          <!-- Se llenará dinámicamente -->
        </tbody>
      </table>
      <div class="tabla-paginacion">
        <div class="pagina-info">Mostrando <span id="registrosMostrados">0-0</span> de <span id="totalRegistros">0</span> registros</div>
        <div class="pagina-controles">
          <button class="pagina-boton disabled">Anterior</button>
          <button class="pagina-boton activo">1</button>
          <button class="pagina-boton">2</button>
          <button class="pagina-boton">3</button>
          <button class="pagina-boton">4</button>
          <button class="pagina-boton">5</button>
          <button class="pagina-boton">Siguiente</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script>
 // Variables globales para los gráficos
let graficoTendencia = null;
let graficoDistribucion = null;
let graficoPrediccion = null;
// Datos proporcionados
const ventasRealizadas =<?php echo json_encode($ventasInventarioJS); ?>;
/* [
  { fecha: '2025-04-05', exportacion: 1, nacional: 20, desecho: 133 },
  { fecha: '2025-04-04', exportacion: 200, nacional: 40, desecho: 433 },
  { fecha: '2025-04-03', exportacion: 120, nacional: 30, desecho: 13 },
  { fecha: '2025-03-03', exportacion: 80, nacional: 10, desecho: 3 }
];*/
// Transformar datos para que coincidan con la estructura esperada
const datosVentas = [];
// Procesar ventasRealizadas
ventasRealizadas.forEach(venta => {
  if (venta.exportacion > 0) {
    datosVentas.push({
      id: `VE-${venta.fecha.replace(/-/g, '').substring(2)}`,
      fecha: venta.fecha,
      categoria: 'exportacion',
      cantidad: venta.exportacion
    });
  }
  if (venta.nacional > 0) {
    datosVentas.push({
      id: `VN-${venta.fecha.replace(/-/g, '').substring(2)}`,
      fecha: venta.fecha,
      categoria: 'nacional',
      cantidad: venta.nacional
    });
  }
  if (venta.desecho > 0) {
    datosVentas.push({
      id: `VD-${venta.fecha.replace(/-/g, '').substring(2)}`,
      fecha: venta.fecha,
      categoria: 'desecho',
      cantidad: venta.desecho
    });
  }
});
// Calcular totales para las tarjetas de estadísticas
function calcularTotales(datos = datosVentas) {
  const totalExportacion = datos
    .filter(v => v.categoria === 'exportacion')
    .reduce((sum, v) => sum + v.cantidad, 0);
  
  const totalNacional = datos
    .filter(v => v.categoria === 'nacional')
    .reduce((sum, v) => sum + v.cantidad, 0);
  
  const totalDesecho = datos
    .filter(v => v.categoria === 'desecho')
    .reduce((sum, v) => sum + v.cantidad, 0);
  
  const totalGeneral = totalExportacion + totalNacional + totalDesecho;
  
  return {
    total: totalGeneral,
    exportacion: totalExportacion,
    nacional: totalNacional,
    desecho: totalDesecho
  };
}
// Actualizar tarjetas de estadísticas
function actualizarEstadisticas(datos = datosVentas) {
  const totales = calcularTotales(datos);
  
  document.getElementById('totalVentas').textContent = totales.total;
  document.getElementById('totalExportacion').textContent = totales.exportacion;
  document.getElementById('totalNacional').textContent = totales.nacional;
  document.getElementById('totalDesecho').textContent = totales.desecho;
}
// Función para obtener datos de tendencia
function obtenerTendencia(datos = datosVentas, dias = 7) {
  // Ordenar por fecha
  const datosOrdenados = [...datos].sort((a, b) => new Date(a.fecha) - new Date(b.fecha));
  
  // Obtener fechas únicas
  const fechasUnicas = [...new Set(datosOrdenados.map(item => item.fecha))].sort();
  
  // Tomar las últimas N fechas
  const ultimasFechas = fechasUnicas.slice(-dias);
  
  const fechas = [], exportacion = [], nacional = [], desecho = [];
  
  ultimasFechas.forEach(fecha => {
    const ventasDelDia = datosOrdenados.filter(item => item.fecha === fecha);
    const [year, month, day] = fecha.split('-');
    fechas.push(`${day}/${month}`);
    
    let totalExp = 0, totalNac = 0, totalDes = 0;
    ventasDelDia.forEach(venta => {
      if (venta.categoria === 'exportacion') totalExp += venta.cantidad;
      else if (venta.categoria === 'nacional') totalNac += venta.cantidad;
      else if (venta.categoria === 'desecho') totalDes += venta.cantidad;
    });
    
    exportacion.push(totalExp);
    nacional.push(totalNac);
    desecho.push(totalDes);
  });
  
  return { fechas, exportacion, nacional, desecho };
}
// Función para obtener datos de distribución
function obtenerDistribucion(datos = datosVentas) {
  const totales = calcularTotales(datos);
  
  return {
    labels: ['Exportación', 'Nacional', 'Desecho'],
    valores: [totales.exportacion, totales.nacional, totales.desecho],
    colores: ['#8E44AD', '#3498DB', '#E74C3C']
  };
}
// Función para actualizar la tabla de ventas
function actualizarTablaVentas(datos = datosVentas) {
  const tablaBody = document.getElementById('tablaVentas');
  tablaBody.innerHTML = '';
  
  // Si no hay datos, mostrar mensaje
  if (datos.length === 0) {
    const fila = document.createElement('tr');
    fila.innerHTML = '<td colspan="5" style="text-align:center;">No hay datos disponibles</td>';
    tablaBody.appendChild(fila);
    
    // Actualizar información de paginación
    document.getElementById('registrosMostrados').textContent = '0-0';
    document.getElementById('totalRegistros').textContent = '0';
    return;
  }
  
  // Ordenar por fecha descendente
  const datosOrdenados = [...datos].sort((a, b) => new Date(b.fecha) - new Date(a.fecha));
  
  // Calcular total general para porcentajes
  const totalGeneral = calcularTotales(datos).total;
  
  // Mostrar todos los registros (o limitar a 20 si hay muchos)
  const maxRegistros = 20;
  const registrosMostrar = datosOrdenados.slice(0, maxRegistros);
  
  registrosMostrar.forEach(venta => {
    const fila = document.createElement('tr');
    const [year, month, day] = venta.fecha.split('-');
    const fechaFormateada = `${day}/${month}/${year}`;
    const porcentaje = totalGeneral > 0 ? ((venta.cantidad / totalGeneral) * 100).toFixed(1) : 0;
    
    const nombreCategoria = venta.categoria === 'exportacion' ? 'Exportación' :
                          venta.categoria === 'nacional' ? 'Nacional' : 'Desecho';
    
    const badgeClass = `badge badge-${venta.categoria}`;
    
    fila.innerHTML = `
      <td>${fechaFormateada}</td>
      <td><span class="${badgeClass}">${nombreCategoria}</span></td>
      <td>${venta.cantidad}</td>
      <td>${porcentaje}%</td>
      <td>#${venta.id}</td>
    `;
    
    tablaBody.appendChild(fila);
  });
  
  // Actualizar información de paginación
  document.getElementById('registrosMostrados').textContent = `1-${registrosMostrar.length}`;
  document.getElementById('totalRegistros').textContent = datosOrdenados.length;
  
  // Añadir entrada en consola para depuración
  console.log('Datos en tabla:', registrosMostrar);
}
// Función para crear modelo de predicción con regresión lineal
function crearModeloPrediccion(datos, categoria) {
  // Convertir fechas a días desde la primera fecha
  const fechasUnicas = [...new Set(datos.map(item => item.fecha))].sort();
  if (fechasUnicas.length < 2) {
    // No hay suficientes datos para crear un modelo
    return {
      predict: () => 0,
      r2: 0,
      string: 'No hay suficientes datos'
    };
  }
  const primeraFecha = new Date(fechasUnicas[0]);
  
  // Preparar datos para la regresión
  const puntos = datos
    .filter(v => v.categoria === categoria)
    .map(v => {
      const fecha = new Date(v.fecha);
      const diasDesdeInicio = (fecha - primeraFecha) / (1000 * 60 * 60 * 24);
      return [diasDesdeInicio, v.cantidad];
    });
  
  // Aplicar regresión lineal solo si hay suficientes puntos
  if (puntos.length < 2) {
    return {
      predict: () => 0,
      r2: 0,
      string: 'No hay suficientes datos'
    };
  }
  const resultado = regression.linear(puntos);
  
  return {
    predict: (dias) => Math.max(0, Math.round(resultado.predict(dias)[1])),
    r2: resultado.r2,
    string: resultado.string
  };
}
// Función para generar predicciones
function generarPredicciones() {
  // Obtener el tipo de predicción seleccionado
  const tipoPrediccion = document.querySelector('.grafico-control[data-prediccion].activo')?.getAttribute('data-prediccion') || 'mes';
  
  // Crear modelos para cada categoría
  const modeloExp = crearModeloPrediccion(datosVentas, 'exportacion');
  const modeloNac = crearModeloPrediccion(datosVentas, 'nacional');
  const modeloDes = crearModeloPrediccion(datosVentas, 'desecho');
  
  // Obtener fechas únicas ordenadas
  const fechasUnicas = [...new Set(datosVentas.map(item => item.fecha))].sort();
  if (fechasUnicas.length < 1) return { labels: [], exportacion: [], nacional: [], desecho: [], r2: {} };
  
  const primeraFecha = new Date(fechasUnicas[0]);
  const ultimaFecha = new Date(fechasUnicas[fechasUnicas.length - 1]);
  
  // Calcular días desde la primera fecha para la última fecha
  const diasUltimaFecha = (ultimaFecha - primeraFecha) / (1000 * 60 * 60 * 24);
  
  // Determinar el período de predicción según la selección
  let periodosPrediccion = [];
  let etiquetas = [];
  
  // Usamos la fecha actual como punto de inicio para las predicciones
  const fechaActual = new Date();
  
  if (tipoPrediccion === 'semana') {
    // Predicción para 7 días (1 semana)
    for (let i = 1; i <= 7; i++) {
      const fecha = new Date(fechaActual);
      fecha.setDate(fechaActual.getDate() + i);
      periodosPrediccion.push(diasUltimaFecha + i);
      etiquetas.push(`${fecha.getDate()}/${fecha.getMonth() + 1}/${fecha.getFullYear()}`);
    }
  } else if (tipoPrediccion === 'mes') {
    // Predicción para 30 días (1 mes)
    for (let i = 1; i <= 30; i++) {
      const fecha = new Date(fechaActual);
      fecha.setDate(fechaActual.getDate() + i);
      periodosPrediccion.push(diasUltimaFecha + i);
      // Mostrar solo algunas etiquetas para no saturar
      if (i % 5 === 0 || i === 1 || i === 30) {
        etiquetas.push(`${fecha.getDate()}/${fecha.getMonth() + 1}/${fecha.getFullYear()}`);
      } else {
        etiquetas.push('');
      }
    }
  } else {
    // Predicción para 365 días (1 año)
    for (let i = 1; i <= 365; i += 30) { // Un punto por mes aproximadamente
      const fecha = new Date(fechaActual);
      fecha.setDate(fechaActual.getDate() + i);
      periodosPrediccion.push(diasUltimaFecha + i);
      etiquetas.push(`${fecha.getMonth() + 1}/${fecha.getFullYear()}`);
    }
  }
  
  // Generar predicciones para cada categoría
  const predExportacion = periodosPrediccion.map(d => modeloExp.predict(d));
  const predNacional = periodosPrediccion.map(d => modeloNac.predict(d));
  const predDesecho = periodosPrediccion.map(d => modeloDes.predict(d));
  
  return {
    labels: etiquetas,
    exportacion: predExportacion,
    nacional: predNacional,
    desecho: predDesecho,
    r2: {
      exportacion: modeloExp.r2,
      nacional: modeloNac.r2,
      desecho: modeloDes.r2
    }
  };
}
// Función para actualizar el gráfico de predicción
function actualizarGraficoPrediccion() {
  const predData = generarPredicciones();
  const ctx = document.getElementById('graficoPrediccion').getContext('2d');
  
  // Si ya existe un gráfico, destruirlo antes de crear uno nuevo
  if (graficoPrediccion) {
    graficoPrediccion.destroy();
  }
  
  graficoPrediccion = new Chart(ctx, {
    type: 'line',
    data: {
      labels: predData.labels,
      datasets: [
        {
          label: 'Exportación (Predicción)',
          data: predData.exportacion,
          borderColor: '#8E44AD',
          backgroundColor: 'rgba(142, 68, 173, 0.1)',
          borderWidth: 3,
          borderDash: [5, 5],
          fill: false
        },
        {
          label: 'Nacional (Predicción)',
          data: predData.nacional,
          borderColor: '#3498DB',
          backgroundColor: 'rgba(52, 152, 219, 0.1)',
          borderWidth: 3,
          borderDash: [5, 5],
          fill: false
        },
        {
          label: 'Desecho (Predicción)',
          data: predData.desecho,
          borderColor: '#E74C3C',
          backgroundColor: 'rgba(231, 76, 60, 0.1)',
          borderWidth: 3,
          borderDash: [5, 5],
          fill: false
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: { 
          beginAtZero: true, 
          grid: { display: true, color: 'rgba(0,0,0,0.05)' } 
        },
        x: { 
          grid: { display: false },
          ticks: {
            autoSkip: false,
            maxRotation: 45,
            minRotation: 45
          }
        }
      },
      plugins: {
        legend: { position: 'top' },
        tooltip: { mode: 'index', intersect: false }
      }
    }
  });
}
// Función para inicializar gráfico de tendencia
function inicializarGraficoTendencia(datos = datosVentas) {
  const ctx = document.getElementById('graficoTendencia').getContext('2d');
  const tendenciaData = obtenerTendencia(datos, 7);
  
  graficoTendencia = new Chart(ctx, {
    type: 'line',
    data: {
      labels: tendenciaData.fechas,
      datasets: [
        {
          label: 'Exportación',
          data: tendenciaData.exportacion,
          borderColor: '#8E44AD',
          backgroundColor: 'rgba(142, 68, 173, 0.1)',
          borderWidth: 3,
          tension: 0.3,
          fill: true
        },
        {
          label: 'Nacional',
          data: tendenciaData.nacional,
          borderColor: '#3498DB',
          backgroundColor: 'rgba(52, 152, 219, 0.1)',
          borderWidth: 3,
          tension: 0.3,
          fill: true
        },
        {
          label: 'Desecho',
          data: tendenciaData.desecho,
          borderColor: '#E74C3C',
          backgroundColor: 'rgba(231, 76, 60, 0.1)',
          borderWidth: 3,
          tension: 0.3,
          fill: true
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: { beginAtZero: true, grid: { display: true, color: 'rgba(0,0,0,0.05)' } },
        x: { grid: { display: false } }
      },
      plugins: {
        legend: { position: 'top' },
        tooltip: { mode: 'index', intersect: false }
      }
    }
  });
}
// Función para inicializar gráfico de distribución
function inicializarGraficoDistribucion(datos = datosVentas) {
  const ctx = document.getElementById('graficoDistribucion').getContext('2d');
  const distribucionData = obtenerDistribucion(datos);
  
  graficoDistribucion = new Chart(ctx, {
    type: 'pie',
    data: {
      labels: distribucionData.labels,
      datasets: [{
        data: distribucionData.valores,
        backgroundColor: distribucionData.colores,
        borderWidth: 0
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { position: 'right' },
        tooltip: {
          callbacks: {
            label: function(context) {
              const label = context.label || '';
              const value = context.raw || 0;
              const total = context.dataset.data.reduce((a, b) => a + b, 0);
              const percentage = Math.round((value / total) * 100);
              return `${label}: ${value} (${percentage}%)`;
            }
          }
        }
      }
    }
  });
}
// Función para aplicar filtros automáticamente
function aplicarFiltros() {
  const categoriaFiltro = document.getElementById('categoriaFiltro').value;
  const periodoFiltro = document.getElementById('periodoFiltro').value;
  const tipoFiltro = document.getElementById('tipoFiltro').value;
  const fechaSeleccionada = document.getElementById('fechaSeleccionada').value;
  
  let datosFiltrados = [...datosVentas];
  
  // Filtrar por categoría
  if (categoriaFiltro !== 'todas') {
    datosFiltrados = datosFiltrados.filter(v => v.categoria === categoriaFiltro);
  }
  
  // Filtrar por periodo
  if (periodoFiltro === 'personalizado' && fechaSeleccionada) {
    datosFiltrados = datosFiltrados.filter(v => v.fecha === fechaSeleccionada);
  } else if (periodoFiltro === 'anterior') {
    // Lógica simplificada para periodo anterior (día anterior)
    const hoy = new Date();
    const fechaAyer = new Date(hoy);
    fechaAyer.setDate(hoy.getDate() - 1);
    const fechaAyerStr = fechaAyer.toISOString().split('T')[0];
    datosFiltrados = datosFiltrados.filter(v => v.fecha === fechaAyerStr);
  }
  
  // Actualizar la interfaz con los datos filtrados
  actualizarEstadisticas(datosFiltrados);
  actualizarTablaVentas(datosFiltrados);
  
  // Actualizar gráficos con los datos filtrados
  const periodoGrafico = document.querySelector('.grafico-control[data-periodo].activo')?.getAttribute('data-periodo') || 7;
  const tendenciaData = obtenerTendencia(datosFiltrados, parseInt(periodoGrafico));
  
  if (graficoTendencia) {
    graficoTendencia.data.labels = tendenciaData.fechas;
    graficoTendencia.data.datasets[0].data = tendenciaData.exportacion;
    graficoTendencia.data.datasets[1].data = tendenciaData.nacional;
    graficoTendencia.data.datasets[2].data = tendenciaData.desecho;
    graficoTendencia.update();
  }
  
  const distribucionData = obtenerDistribucion(datosFiltrados);
  if (graficoDistribucion) {
    graficoDistribucion.data.datasets[0].data = distribucionData.valores;
    graficoDistribucion.update();
  }
  
  // Actualizar gráfico de predicción
  actualizarGraficoPrediccion();
}
// Inicializar todo cuando el DOM esté cargado
document.addEventListener('DOMContentLoaded', function() {
  // Verificar que datosVentas se haya llenado correctamente desde ventasRealizadas
  console.log('Datos de ventas cargados:', datosVentas);
  
  // Configurar eventos para los filtros (automáticos)
  document.getElementById('tipoFiltro').addEventListener('change', aplicarFiltros);
  document.getElementById('periodoFiltro').addEventListener('change', function() {
    document.getElementById('fechaPersonalizada').style.display = 
      this.value === 'personalizado' ? 'block' : 'none';
    aplicarFiltros();
  });
  document.getElementById('categoriaFiltro').addEventListener('change', aplicarFiltros);
  document.getElementById('fechaSeleccionada').addEventListener('change', aplicarFiltros);
  
  // Configurar eventos para los controles de los gráficos
  document.querySelectorAll('.grafico-control[data-periodo]').forEach(button => {
    button.addEventListener('click', function() {
      document.querySelectorAll('.grafico-control[data-periodo]').forEach(btn => {
        btn.classList.remove('activo');
      });
      this.classList.add('activo');
      aplicarFiltros();
    });
  });
  
  document.querySelectorAll('.grafico-control[data-tipo]').forEach(button => {
    button.addEventListener('click', function() {
      document.querySelectorAll('.grafico-control[data-tipo]').forEach(btn => {
        btn.classList.remove('activo');
      });
      this.classList.add('activo');
      
      const tipo = this.getAttribute('data-tipo');
      graficoDistribucion.config.type = tipo;
      
      // Actualizar opciones según el tipo de gráfico
      if (tipo === 'bar') {
        graficoDistribucion.options.scales = {
          y: { beginAtZero: true, grid: { display: true, color: 'rgba(0,0,0,0.05)' } },
          x: { grid: { display: false } }
        };
      } else {
        graficoDistribucion.options.scales = {};
      }
      
      graficoDistribucion.update();
    });
  });
  
  // Configurar eventos para los controles de predicción
  document.querySelectorAll('.grafico-control[data-prediccion]').forEach(button => {
    button.addEventListener('click', function() {
      document.querySelectorAll('.grafico-control[data-prediccion]').forEach(btn => {
        btn.classList.remove('activo');
      });
      this.classList.add('activo');
      actualizarGraficoPrediccion();
    });
  });
  
  // Inicializar gráficos y datos
  inicializarGraficoTendencia();
  inicializarGraficoDistribucion();
  actualizarEstadisticas();
  actualizarTablaVentas();
  actualizarGraficoPrediccion();
});
  </script>
</body>
</html>