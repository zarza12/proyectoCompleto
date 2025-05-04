<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once  '../../controllers/daoProduccion.php';
include_once  '../../models/Produccion.php';

if (isset($_POST['registrarProduccion']) && $_POST['registrarProduccion'] === 'registrarProduccion') {
    // Recibir datos del formulario en PHP
    $sectorProduccion = $_POST['sectorProduccion'];
    $fechaProduccion = $_POST['fechaProduccion'];
    $calidadExportacion = $_POST['calidadExportacion']; 
    $calidadNacional = $_POST['calidadNacional'] ?? 0;
    $calidadDesecho = $_POST['calidadDesecho']??0;
    $totalCajas = $_POST['totalCajas']?? 0;
    

    $produccion = new Produccion(
        null, 
        $sectorProduccion,
        $fechaProduccion,
        $calidadExportacion,
        $calidadNacional,
        $calidadDesecho,
        $totalCajas
    );  
    $daoProduccion = new daoProduccion();
   

    $registo = $daoProduccion->registrarProduccion($produccion);
    

    if ($registo) {
        mostrarMensaje("Registro exitoso.");
    } else {
        mostrarMensaje("Error al insertar el registro.");
       
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Producción</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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
            padding: 20px;
        }
        
        .contenedor-principal {
            max-width: 800px;
            margin: 30px auto;
            background-color: var(--color-blanco);
            border-radius: var(--borde-radio);
            box-shadow: var(--sombra-media);
            overflow: hidden;
        }
        
        .cabecera {
            background: linear-gradient(135deg, var(--color-primario), var(--color-secundario));
            color: var(--color-blanco);
            padding: 25px 30px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .titulo-pagina {
            font-size: 24px;
            margin: 0;
            font-weight: 600;
        }
        
        .subtitulo {
            font-size: 16px;
            margin-top: 5px;
            opacity: 0.9;
        }
        
        .boton-atras {
            background-color: rgba(255, 255, 255, 0.2);
            color: var(--color-blanco);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transicion);
        }
        
        .boton-atras:hover {
            background-color: rgba(255, 255, 255, 0.3);
            transform: scale(1.05);
        }
        
        .contenedor-formulario {
            padding: 30px;
        }
        
        .seccion-formulario {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--color-borde);
        }
        
        .seccion-formulario:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .titulo-seccion {
            font-size: 18px;
            color: var(--color-primario);
            margin-bottom: 15px;
            padding-left: 10px;
            border-left: 4px solid var(--color-acento);
        }
        
        .grupo-formulario {
            margin-bottom: 20px;
        }
        
        .etiqueta {
            display: block;
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--color-texto);
        }
        
        .campo-entrada {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--color-borde);
            border-radius: 6px;
            font-size: 15px;
            color: var(--color-texto);
            background-color: #FAFAFA;
            transition: var(--transicion);
        }
        
        .campo-entrada:focus {
            outline: none;
            border-color: var(--color-acento);
            box-shadow: 0 0 0 3px rgba(165, 105, 189, 0.2);
            background-color: var(--color-blanco);
        }
        
        .campo-entrada:disabled {
            background-color: #F1F1F1;
            cursor: not-allowed;
        }
        
        .campo-select {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23333' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 15px;
            padding-right: 40px;
        }
        
        .seccion-calidad {
            background-color: #F9F4FC; /* Fondo suave color zarzamora */
            border-radius: 8px;
            padding: 20px;
            margin-top: 10px;
        }
        
        .cuadricula-calidad {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        .item-calidad {
            background-color: var(--color-blanco);
            padding: 15px;
            border-radius: 8px;
            box-shadow: var(--sombra-suave);
            transition: var(--transicion);
        }
        
        .item-calidad:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .item-calidad .etiqueta {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--color-primario);
        }
        
        .icono-calidad {
            font-size: 18px;
        }
        
        .campo-numero {
            text-align: center;
            font-weight: 500;
        }
        
        .campo-numero::-webkit-inner-spin-button,
        .campo-numero::-webkit-outer-spin-button {
            opacity: 1;
            height: 25px;
        }
        
        .resumen-cajas {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-top: 15px;
            padding: 10px 15px;
            background-color: var(--color-resalte);
            border-radius: 6px;
            font-weight: 500;
        }
        
        .acciones-formulario {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 20px;
        }
        
        .boton {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transicion);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .boton-primario {
            background: linear-gradient(to right, var(--color-secundario), var(--color-primario));
            color: var(--color-blanco);
        }
        
        .boton-primario:hover {
            background: linear-gradient(to right, var(--color-primario), var(--color-secundario));
            transform: translateY(-2px);
            box-shadow: var(--sombra-media);
        }
        
        .boton-secundario {
            background-color: #F1F1F1;
            color: var(--color-texto);
        }
        
        .boton-secundario:hover {
            background-color: #E5E5E5;
        }
        
        .campo-con-icono {
            position: relative;
        }
        
        .icono-campo {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: 15px;
            color: var(--color-primario);
            font-size: 16px;
        }
        
        .campo-con-icono .campo-entrada {
            padding-left: 40px;
        }
        
        .tarjeta-info {
            background-color: rgba(165, 105, 189, 0.1);
            border-left: 4px solid var(--color-acento);
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 0 8px 8px 0;
        }
        
        .tarjeta-info i {
            color: var(--color-primario);
            margin-right: 8px;
        }
        
        @media (max-width: 768px) {
            
            .contenedor-principal {
                margin: 10px auto;
            }
            
            .contenedor-formulario {
                padding: 20px;
            }
            
            .cuadricula-calidad {
                grid-template-columns: 1fr;
            }
            
            .acciones-formulario {
                flex-direction: column;
            }
            
            .boton {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
<?php include '../../views/menuA.php'; ?>

    <div class="contenedor-principal">
        <div class="cabecera">
            <div>
                <h1 class="titulo-pagina">Registro de Producción</h1>
                <p class="subtitulo">Ingrese los datos de la nueva producción</p>
            </div>
            <button class="boton-atras" title="Volver atrás">
                <i class="fas fa-arrow-left"></i>
            </button>
        </div>
        
        <div class="contenedor-formulario">
            <div class="tarjeta-info">
                <i class="fas fa-info-circle"></i>
                Complete todos los campos requeridos para registrar la producción diaria.
            </div>
            
            <form id="formularioProduccion" method="POST" action="/dashboard/ProyectoMari/proyectoCompleto/views/produccion/registrarProduccion.php">
                <div class="seccion-formulario">
                    <h3 class="titulo-seccion">Información General</h3>
                    
                    <div class="grupo-formulario">
                        <label class="etiqueta" for="sectorProduccion">Sector de Producción <span style="color: red;">*</span></label>
                        <div class="campo-con-icono">
                            <i class="fas fa-map-marker-alt icono-campo"></i>
                            <select id="sectorProduccion" name="sectorProduccion" class="campo-entrada campo-select" required>
                                <option value="">Seleccione un sector</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="grupo-formulario">
                        <label class="etiqueta" for="fechaProduccion">Fecha de Producción <span style="color: red;">*</span></label>
                        <div class="campo-con-icono">
                            <i class="fas fa-calendar-alt icono-campo"></i>
                            <input type="date" id="fechaProduccion" name="fechaProduccion" class="campo-entrada" value="2025-03-16">
                        </div>
                    </div>

                </div>
                
                <div class="seccion-formulario">
                    <h3 class="titulo-seccion">Detalle de Calidad del Lote</h3>
                    
                    <div class="seccion-calidad">
                        <p>Ingrese la cantidad de cajas según su calidad:</p>
                        
                        <div class="cuadricula-calidad">
                            <div class="item-calidad">
                                <label class="etiqueta" for="calidadExportacion">
                                    <i class="fas fa-globe icono-calidad"></i> Exportación
                                </label>
                                <input type="number" id="calidadExportacion" name="calidadExportacion" min="0" class="campo-entrada campo-numero" placeholder="Cajas" oninput="actualizarSumaCajas()">
                            </div>
                            
                            <div class="item-calidad">
                                <label class="etiqueta" for="calidadNacional">
                                    <i class="fas fa-flag icono-calidad"></i> Consumo Nacional
                                </label>
                                <input type="number" id="calidadNacional" name="calidadNacional" min="0" class="campo-entrada campo-numero" placeholder="Cajas" oninput="actualizarSumaCajas()">
                            </div>
                            
                            <div class="item-calidad">
                                <label class="etiqueta" for="calidadDesecho">
                                    <i class="fas fa-trash icono-calidad"></i> Desecho
                                </label>
                                <input type="number" id="calidadDesecho" name="calidadDesecho" min="0" class="campo-entrada campo-numero" placeholder="Cajas" oninput="actualizarSumaCajas()">
                            </div>
                        </div>
                        
                        <div class="resumen-cajas" id="resumenCajas">
                            Total de cajas: <span id="totalCajas">0</span>
                        </div>
                    </div>
                </div>
                
                <!-- Campo oculto para enviar el total de cajas -->
                <input type="hidden" id="totalCajasHidden" name="totalCajas" value="0">
                
                <div class="acciones-formulario">
                    <button type="button" class="boton boton-secundario">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="boton boton-primario" id="registrarProduccion" value="registrarProduccion" name="registrarProduccion">
                        <i class="fas fa-save"></i> Registrar Producción
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
   // 1. Definir sectores en un array
const sectores = [
    { value: 'sector_a', label: 'Sector A' },
    { value: 'sector_b', label: 'Sector B' },
    { value: 'sector_c', label: 'Sector C' },
    { value: 'sector_d', label: 'Sector D' }
];

// 2. Función para poblar el <select>
function populateSectores() {
    const select = document.getElementById('sectorProduccion');
    // primero limpiamos cualquier opción vieja (incluida la "Seleccione...")
    select.innerHTML = '<option value="">Seleccione un sector</option>';
    sectores.forEach(sec => {
        const option = document.createElement('option');
        option.value = sec.value;
        option.textContent = sec.label;
        select.appendChild(option);
    });
}

// 3. Función para calcular la suma de cajas
function actualizarSumaCajas() {
    const exp = parseInt(document.getElementById('calidadExportacion').value) || 0;
    const nac = parseInt(document.getElementById('calidadNacional').value) || 0;
    const des = parseInt(document.getElementById('calidadDesecho').value) || 0;
    const total = exp + nac + des;
    document.getElementById('totalCajas').textContent = total;
    document.getElementById('totalCajasHidden').value = total;
    document.getElementById('resumenCajas').style.backgroundColor =
        total > 0 ? '#D2B4DE' : '#F9F4FC';
}

// NUEVA FUNCIÓN: Obtener fecha actual en formato ISO local (sin conversión UTC)
function getLocalISODate() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

// 4. Listener que se dispara cuando el DOM ya está cargado
document.addEventListener('DOMContentLoaded', () => {
    // Usamos la nueva función para establecer la fecha local correcta
    document.getElementById('fechaProduccion').value = getLocalISODate();

    // llama a poblar sectores
    populateSectores();
});
</script>

</body>
</html>