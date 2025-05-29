<?php
include_once '../../controllers/daoTratamiento.php';
include_once  '../../models/Tratamiento.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_POST['registrarTratamiento']) && $_POST['registrarTratamiento'] === 'registrarTratamiento') {
    //echo "aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa";

    $fechaRegistroTratamiento = $_POST['fechaRegistroTratamiento2'];
    $sectorTratamiento        = $_POST['sectorTratamiento'];
    $frecuenciaTratamiento    = $_POST['frecuenciaTratamiento'];
    $observacionesTratamiento = $_POST['observacionesTratamiento'];
    
    // Capturar arrays de fumigantes
    $fumigante_nombres     = $_POST['fumigante_nombre'];
    $fumigante_cantidades  = $_POST['fumigante_cantidad'];
    $fumigante_unidades    = $_POST['fumigante_unidad'];
    
    // Procesar los fumigantes (ejemplo)
    /*$totalFumigantes = count($fumigante_nombres);
    for ($i = 0; $i < $totalFumigantes; $i++) {
        $nombreFumigante    = $fumigante_nombres[$i];
        $cantidadFumigante  = $fumigante_cantidades[$i];
        $unidadFumigante    = $fumigante_unidades[$i];
        
        // Aquí puedes procesar cada fumigante
        // Por ejemplo, insertarlo en la base de datos
    }*/
    
    

    $tratamiento = new Tratamiento(
        null , 
        $fechaRegistroTratamiento,
        $sectorTratamiento,
        $frecuenciaTratamiento,
        $observacionesTratamiento,
        $fumigante_nombres,
        $fumigante_cantidades,
        $fumigante_unidades

    );  
    $daoTratamiento = new daoTratamiento();
    $registo = $daoTratamiento->registrarTratamiento($tratamiento);
    

    if ($registo) {
        echo "
        <script>
            alert('Registro exitoso');
            window.location.href = 'registrarTratamiento.php';
        </script>";
    } else {
        mostrarMensaje("Error al  registro.");
       
    }
   

}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Tratamiento</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Los estilos se mantienen igual... */
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
        
        .fumigante-header {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 0.5fr;
            gap: 10px;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--color-primario);
        }
        
        .fumigante-item {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 0.5fr;
            gap: 10px;
            margin-bottom: 10px;
            align-items: center;
        }
        
        .btn-add {
            background-color: var(--color-acento);
            color: var(--color-blanco);
            border: none;
            border-radius: 4px;
            padding: 8px 15px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            font-weight: 500;
            margin-top: 10px;
            transition: var(--transicion);
        }
        
        .btn-add:hover {
            background-color: var(--color-secundario);
        }
        
        .remove-btn {
            background-color: var(--color-error);
            color: var(--color-blanco);
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            cursor: pointer;
            transition: var(--transicion);
        }
        
        .remove-btn:hover {
            transform: scale(1.1);
        }
        
        .required {
            color: var(--color-error);
            margin-left: 4px;
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
        
        @media (max-width: 768px) {
            .contenedor-principal {
                margin: 10px auto;
            }
            
            .contenedor-formulario {
                padding: 20px;
            }
            
            .acciones-formulario {
                flex-direction: column;
            }
            
            .boton {
                width: 100%;
                justify-content: center;
            }
            
            .fumigante-item {
                grid-template-columns: 1fr;
                gap: 8px;
            }
            
            .fumigante-header {
                display: none;
            }
        }
    </style>
</head>
<body>
<?php include '../../views/menuA.php'; ?>

    <div class="contenedor-principal">
        <div class="cabecera">
            <div>
                <h1 class="titulo-pagina">Registro de Tratamiento</h1>
                <p class="subtitulo">Ingrese los datos del nuevo tratamiento</p>
            </div>
            <button class="boton-atras" title="Volver atrás">
                <i class="fas fa-arrow-left"></i>
            </button>
        </div>
        
        <div class="contenedor-formulario">
            <div class="tarjeta-info">
                <i class="fas fa-info-circle"></i>
                Complete todos los campos requeridos para registrar una nueva receta de tratamiento.
            </div>
            
            <form id="formularioTratamiento" method="POST" action="registrarTratamiento.php">
                <div class="seccion-formulario">
                    <h3 class="titulo-seccion">Información General</h3>
                    
                    <div class="grupo-formulario">
                        <label class="etiqueta" for="fechaRegistroTratamiento">Fecha de Registro</label>
                        <div class="campo-con-icono">
                            <i class="fas fa-calendar-alt icono-campo"></i>
                            <input type="date" id="fechaRegistroTratamiento2" style="display: none;" name="fechaRegistroTratamiento2" class="campo-entrada" readonly >
                            <input type="date" id="fechaRegistroTratamiento" name="fechaRegistroTratamiento" class="campo-entrada" readonly disabled>
                        </div>
                    </div>
                    
                    <div class="grupo-formulario">
                        <label class="etiqueta" for="sectorTratamiento">Seleccione el Sector <span style="color: red;">*</span></label>
                        <div class="campo-con-icono">
                            <i class="fas fa-map-marker-alt icono-campo"></i>
                            <select id="sectorTratamiento" name="sectorTratamiento" class="campo-entrada campo-select" required>
                                <option value="">Seleccione un sector...</option>
                                <!-- Las opciones se cargarán dinámicamente desde JavaScript -->
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="seccion-formulario">
                    <h3 class="titulo-seccion">Fumigantes y Cantidades</h3>
                    
                    <div class="grupo-formulario">
                        <div class="fumigante-header">
                            <div>Nombre del Fumigante</div>
                            <div>Cantidad</div>
                            <div>Unidad</div>
                            <div></div>
                        </div>
                        <div id="contenedorFumigantes">
                            <div class="fumigante-item">
                                <input type="text" name="fumigante_nombre[]" class="campo-entrada inputFumigante" placeholder="Nombre del fumigante" required>
                                <input type="number" name="fumigante_cantidad[]" class="campo-entrada inputCantidad" placeholder="Cantidad" required step="0.01">
                                <select name="fumigante_unidad[]" class="campo-entrada campo-select selectUnidad" required>
                                    <option value="L/Ha">L/Ha</option>
                                    <option value="Kg/Ha">Kg/Ha</option>
                                    <option value="mL/Ha">mL/Ha</option>
                                    <option value="g/Ha">g/Ha</option>
                                </select>
                                <button type="button" class="remove-btn" onclick="this.parentElement.remove()">×</button>
                            </div>
                        </div>
                        <button type="button" class="btn-add" onclick="agregarFumigante()">
                            <i class="fas fa-plus"></i> Agregar Fumigante
                        </button>
                    </div>
                </div>
                
                <div class="seccion-formulario">
                    <h3 class="titulo-seccion">Detalles del Tratamiento</h3>
                    
                    <div class="grupo-formulario">
                        <label class="etiqueta" for="frecuenciaTratamiento">Frecuencia del Tratamiento <span style="color: red;">*</span></label>
                        <div class="campo-con-icono">
                            <i class="fas fa-clock icono-campo"></i>
                            <select id="frecuenciaTratamiento" name="frecuenciaTratamiento" class="campo-entrada campo-select" required>
                                <option value="">Seleccione la frecuencia...</option>
                                <option value="diario">Diario</option>
                                <option value="semanal">Semanal</option>
                                <option value="quincenal">Quincenal</option>
                                <option value="mensual">Mensual</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="seccion-formulario">
                    <h3 class="titulo-seccion">Observaciones</h3>
                    
                    <div class="grupo-formulario">
                        <label class="etiqueta" for="observacionesTratamiento">Notas adicionales</label>
                        <textarea id="observacionesTratamiento" name="observacionesTratamiento" class="campo-entrada" rows="3" placeholder="Ingrese cualquier observación relevante..."></textarea>
                    </div>
                </div>
                
                <div class="acciones-formulario">
                    <button type="button" class="boton boton-secundario">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="boton boton-primario" value="registrarTratamiento" id="registrarTratamiento" name="registrarTratamiento">
                        <i class="fas fa-save"></i> Registrar Tratamiento
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Definir el array de sectores
        const sectores = [
            { value: 'sector_a', label: 'Sector A' },
            { value: 'sector_b', label: 'Sector B' },
            { value: 'sector_c', label: 'Sector C' },
            { value: 'sector_d', label: 'Sector D' }
        ];
        
        // Función para cargar los sectores en el select
        function cargarSectores() {
            const selectSector = document.getElementById('sectorTratamiento');
            
            // Mantener la opción por defecto
            const opcionPorDefecto = selectSector.options[0];
            
            // Limpiar las opciones actuales excepto la primera
            selectSector.innerHTML = '';
            selectSector.appendChild(opcionPorDefecto);
            
            // Agregar las opciones desde el array
            sectores.forEach(sector => {
                const opcion = document.createElement('option');
                opcion.value = sector.value;
                opcion.textContent = sector.label;
                selectSector.appendChild(opcion);
            });
        }
        
        // Establecer la fecha actual en el campo de fecha y cargar los sectores
        document.addEventListener('DOMContentLoaded', function() {
            const fechaActual = new Date().toISOString().split('T')[0];
            document.getElementById('fechaRegistroTratamiento').value = fechaActual;
            document.getElementById('fechaRegistroTratamiento2').value = fechaActual;
            // Cargar sectores al iniciar
            cargarSectores();
        });
        
        // Función para agregar un nuevo fumigante
        function agregarFumigante() {
            const contenedor = document.getElementById('contenedorFumigantes');
            
            const nuevaFila = document.createElement('div');
            nuevaFila.className = 'fumigante-item';
            
            nuevaFila.innerHTML = `
                <input type="text" name="fumigante_nombre[]" class="campo-entrada inputFumigante" placeholder="Nombre del fumigante" required>
                <input type="number" name="fumigante_cantidad[]" class="campo-entrada inputCantidad" placeholder="Cantidad" required step="0.01">
                <select name="fumigante_unidad[]" class="campo-entrada campo-select selectUnidad" required>
                    <option value="L/Ha">L/Ha</option>
                    <option value="Kg/Ha">Kg/Ha</option>
                    <option value="mL/Ha">mL/Ha</option>
                    <option value="g/Ha">g/Ha</option>
                </select>
                <button type="button" class="remove-btn" onclick="this.parentElement.remove()">×</button>
            `;
            
            contenedor.appendChild(nuevaFila);
        }
        
        // Función para registrar el tratamiento
        function registrarTratamiento() {
            const sector = document.getElementById('sectorTratamiento').value;
            const frecuencia = document.getElementById('frecuenciaTratamiento').value;
            
            // Obtener todos los fumigantes
            const filasFumigantes = document.querySelectorAll('.fumigante-item');
            const fumigantes = [];
            
            filasFumigantes.forEach(fila => {
                const nombre = fila.querySelector('.inputFumigante').value;
                const cantidad = fila.querySelector('.inputCantidad').value;
                const unidad = fila.querySelector('.selectUnidad').value;
                
                if (nombre && cantidad) {
                    fumigantes.push({
                        nombre,
                        cantidad,
                        unidad
                    });
                }
            });
            
            // Validar campos requeridos
            if (!sector) {
                alert('Por favor seleccione un sector');
                return;
            }
            
            if (!frecuencia) {
                alert('Por favor seleccione una frecuencia de tratamiento');
                return;
            }
            
            if (fumigantes.length === 0) {
                alert('Debe ingresar al menos un fumigante');
                return;
            }
            
            // Aquí iría la lógica para enviar los datos al servidor
            alert('Tratamiento registrado correctamente');
            
            // Reiniciar formulario después de enviar
            document.getElementById('formularioTratamiento').reset();
            
            // Reiniciar el contenedor de fumigantes
            const contenedor = document.getElementById('contenedorFumigantes');
            contenedor.innerHTML = `
                <div class="fumigante-item">
                    <input type="text" class="campo-entrada inputFumigante" placeholder="Nombre del fumigante" required>
                    <input type="number" class="campo-entrada inputCantidad" placeholder="Cantidad" required step="0.01">
                    <select class="campo-entrada campo-select selectUnidad" required>
                        <option value="L/Ha">L/Ha</option>
                        <option value="Kg/Ha">Kg/Ha</option>
                        <option value="mL/Ha">mL/Ha</option>
                        <option value="g/Ha">g/Ha</option>
                    </select>
                    <button type="button" class="remove-btn" onclick="this.parentElement.remove()">×</button>
                </div>
            `;
            
            // Restablecer la fecha
            document.getElementById('fechaRegistroTratamiento').value = new Date().toISOString().split('T')[0];
            
            // Volver a cargar las opciones de sectores
            cargarSectores();
        }
    </script>
</body>
</html>