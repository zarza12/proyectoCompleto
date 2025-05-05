<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once '../../controllers/daoEntregas.php';
include_once  '../../models/Entregas.php';

if (isset($_POST['registrarEntrega']) && $_POST['registrarEntrega'] === 'registrarEntrega') {

    $fechaRegistrarEntrega = $_POST['fechaRegistrarEntrega2'];
    $cantidadProductos    = $_POST['cantidadProductos'];
    $calidadProducto      = $_POST['calidadProducto'];
    $nombreTransportista  = $_POST['nombreTransportista'];
    $emailComprador       = $_POST['emailComprador']??numfmt_get_locale;
    $nombreEmpresa        = $_POST['nombreEmpresa'];

    
    
    

    $entrega = new Entregas(
        null, // idEntregas (null porque es autoincrementable)
        $fechaRegistrarEntrega,
        $calidadProducto,
        $cantidadProductos,
        $nombreEmpresa,
        $emailComprador,
        $nombreTransportista
    );
    
    $daoEntregas = new daoEntregas();
    $registo = $daoEntregas->registrarEntrega($entrega);
    

    if ($registo) {
        echo "
        <script>
            alert('Registro exitoso');
            window.location.href = 'registrarEntregas.php';
        </script>";
    } else {
        mostrarMensaje("Error al registrar.");
       
    }
   

}


?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Entregas</title>
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
        }

        /* Contenedor principal ajustado para el menú */
        .contenido-principal {
            width: calc(100% - var(--menu-width-closed));
            margin-left: var(--menu-width-closed);
            transition: margin-left 0.3s ease, width 0.3s ease;
            padding: 20px;
            box-sizing: border-box;
        }
        
        /* Ajuste cuando el menú está abierto */
        #menuPrincipal:hover ~ .contenido-principal {
            width: calc(100% - var(--menu-width-open));
            margin-left: var(--menu-width-open);
        }
        
        /* Estilos para el contenedor principal */
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
            margin-bottom: 20px;
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
        
        /* CORREGIDO: Estilos para campos con íconos */
        .campo-contenedor {
            position: relative;
            display: flex;
            align-items: center;
        }

        .campo-icono {
            position: absolute;
            left: 10px;
            color: var(--color-primario);
            font-size: 16px;
            pointer-events: none; /* Asegura que no interfiera con clics */
            z-index: 1;
        }
        
        .campo-entrada, 
        .campo-select {
            width: 100%;
            padding: 12px 15px 12px 35px; /* Padding izquierdo para acomodar el ícono */
            border: 1px solid var(--color-borde);
            border-radius: 6px;
            font-size: 15px;
            color: var(--color-texto);
            background-color: #FAFAFA;
            transition: var(--transicion);
        }
        
        .campo-entrada:focus,
        .campo-select:focus {
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
        
        /* Estilos para elementos requeridos */
        .required {
            color: var(--color-error);
            margin-left: 5px;
        }
        
        /* Responsividad */
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
        }
    </style>
</head>
<body>
<?php include '../../views/menuA.php'; ?>

<div class="contenido-principal">
    <div class="contenedor-principal">
        <div class="cabecera">
            <div>
                <h1 class="titulo-pagina">Registro de Entrega</h1>
                <p class="subtitulo">Complete el formulario para registrar una nueva entrega</p>
            </div>
            <a href="index.php">
                <button class="boton-atras" title="Volver atrás">
                    <i class="fas fa-arrow-left"></i>
                </button>
            </a>
        </div>
        
        <div class="contenedor-formulario">
            <div class="tarjeta-info">
                <i class="fas fa-info-circle"></i>
                Complete todos los campos requeridos para registrar la entrega de productos.
            </div>
            
            <form id="formularioRegistrarEntrega"  method="POST" action="registrarEntregas.php">
                <div class="seccion-formulario">
                <h3 class="titulo-seccion">Información del Producto</h3>
        
                 <!-- Calidad del Producto -->
                <div class="grupo-formulario">
                    <label class="etiqueta" for="calidadProducto">Calidad del Producto<span class="required">*</span></label>
                <div class="campo-contenedor">
                <i class="fas fa-tag campo-icono"></i>
                <select id="calidadProducto" name="calidadProducto" class="campo-select" required>
                    <option value="">Seleccione la calidad</option>
                    <option value="exportacion">Exportación</option>
                    <option value="nacional">Consumo Nacional</option>
                    <option value="desecho">Desecho</option>
                </select>
                </div>
                </div>

                 <!-- Cantidad de Productos -->
                <div class="grupo-formulario">
                <label class="etiqueta" for="cantidadProductos">Cantidad de Productos<span class="required">*</span></label>
                <div class="campo-contenedor">
                <i class="fas fa-boxes campo-icono"></i>
                <input type="number" id="cantidadProductos" name="cantidadProductos" class="campo-entrada" min="1" required>
                </div>
                </div>
                </div>
    
                <div class="seccion-formulario">
                 <h3 class="titulo-seccion">Información del Destinatario</h3>
        
                <!-- Empresa Receptora -->
                <div class="grupo-formulario">
                <label class="etiqueta" for="nombreEmpresa">Empresa Receptora<span class="required">*</span></label>
                <div class="campo-contenedor">
                <i class="fas fa-building campo-icono"></i>
                <input type="text" id="nombreEmpresa" name="nombreEmpresa" class="campo-entrada" required>
                </div>
                </div>

                <!-- Opción para añadir email -->
                <div class="grupo-formulario">
                <label class="etiqueta" for="opcionEmail">¿Desea registrar un correo electrónico para recibir reportes?</label>
                <div class="campo-contenedor">
                <i class="fas fa-envelope campo-icono"></i>
                <select id="opcionEmail" name="opcionEmail" class="campo-select" onchange="mostarGmail()">
                    <option value="no">No</option>
                    <option value="si">Sí</option>
                </select>
                </div>
                </div>

                <!-- Email del Comprador (Opcional) -->
                <div class="grupo-formulario" id="campoEmail" style="display: none;">
                <label class="etiqueta" for="emailComprador">Email del Comprador (Opcional)</label>
                <div class="campo-contenedor">
                <i class="fas fa-at campo-icono"></i>
                <input type="email" id="emailComprador" name="emailComprador" class="campo-entrada" placeholder="ejemplo@correo.com">
                </div>
                </div>
                </div>
    
                <div class="seccion-formulario">
                <h3 class="titulo-seccion">Información de Entrega</h3>
        
                <!-- Nombre del Transportista -->
                <div class="grupo-formulario">
                <label class="etiqueta" for="nombreTransportista">Nombre del Transportista<span class="required">*</span></label>
                <div class="campo-contenedor">
                <i class="fas fa-truck campo-icono"></i>
                <input type="text" id="nombreTransportista" name="nombreTransportista" class="campo-entrada" required>
                </div>
                </div>

                <!-- Fecha de Entrega -->
                <div class="grupo-formulario">
                <label class="etiqueta" for="fechaRegistrarEntrega">Fecha de Entrega<span class="required">*</span></label>
                 <div class="campo-contenedor">
                <i class="fas fa-calendar-alt campo-icono"></i>
                <input type="date" id="fechaRegistrarEntrega2" name="fechaRegistrarEntrega2" class="campo-entrada" style="display: none;" required >
                <input type="date" id="fechaRegistrarEntrega" name="fechaRegistrarEntrega" class="campo-entrada" required disabled>
                </div>
                </div>
                </div>

                <!-- Botones de Acción -->
                <div class="acciones-formulario">
                <button type="button" class="boton boton-secundario" onclick="limpiarFormulario()">
                <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="submit" id="registrarEntrega" name="registrarEntrega" value="registrarEntrega" class="boton boton-primario">
                <i class="fas fa-save"></i> Registrar Entrega
                </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Configurar la fecha actual en el campo de fecha
    document.addEventListener('DOMContentLoaded', function() {
        const ahora = new Date();
        
        // Convertir la fecha a formato YYYY-MM-DD para el input date
        const año = ahora.getFullYear();
        const mes = String(ahora.getMonth() + 1).padStart(2, '0');
        const dia = String(ahora.getDate()).padStart(2, '0');
        
        const fechaHoy = `${año}-${mes}-${dia}`;
        
        // Asignar la fecha actual a ambos campos de fecha
        document.getElementById('fechaRegistrarEntrega').value = fechaHoy;
        document.getElementById('fechaRegistrarEntrega2').value = fechaHoy;
    });
    
    // Función para mostrar u ocultar el campo de email
    function mostarGmail() {
        const opcionEmail = document.getElementById('opcionEmail').value;
        const campoEmail = document.getElementById('campoEmail');
        
        if (opcionEmail === 'si') {
            campoEmail.style.display = 'block';
        } else {
            campoEmail.style.display = 'none';
            document.getElementById('emailComprador').value = '';
        }
    }
    
    // Función para limpiar el formulario
    function limpiarFormulario() {
        document.getElementById('formularioRegistrarEntrega').reset();
        document.getElementById('campoEmail').style.display = 'none';
        
        // Restablecer la fecha actual
        const fechaHoy = new Date().toISOString().split('T')[0];
        document.getElementById('fechaRegistrarEntrega').value = fechaHoy;
        
        // Quitar cualquier borde de error
        const inputs = document.querySelectorAll('.campo-entrada, .campo-select');
        inputs.forEach(input => {
            input.style.borderColor = 'var(--color-borde)';
        });
    }
    
    // Función para manejar el registro de entrega
    function registrarEntrega() {
        // Validar el formulario
        const form = document.getElementById('formularioRegistrarEntrega');
        const inputs = form.querySelectorAll('input[required], select[required]');
        let formValido = true;
        
        inputs.forEach(input => {
            if (!input.value) {
                input.style.borderColor = 'var(--color-error)';
                formValido = false;
            } else {
                input.style.borderColor = 'var(--color-borde)';
            }
        });
        
        if (!formValido) {
            alert('Por favor, complete todos los campos requeridos');
            return;
        }
        
        // Simulación de envío de datos
        alert('Entrega registrada correctamente');
        limpiarFormulario();
    }
</script>
</body>
</html>