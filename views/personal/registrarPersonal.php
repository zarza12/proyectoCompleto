<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once  '../../controllers/daoPersonas.php';
include_once  '../../models/Personas.php';


function generarCuatroDigitos() {
    $numero=0;
    $infinito=true;
    while($infinito){
        $numero = rand(1000, 9999);
        $daoPersona = new daoPersonas();
        $existeID = $daoPersona->existePersonaID($numero);
        if ($existeID) {
            $infinito=true;
        } else {
            $infinito=false;
           
        }
    }
    return $numero;
}

$IDpersona=generarCuatroDigitos();


if (isset($_POST['registrarPersonal']) && $_POST['registrarPersonal'] === 'registrarPersonal') {
    // Recibir datos del formulario en PHP
    $nombreCompleto = $_POST['nombreCompleto'];
    $fechaNacimiento = $_POST['fechaNacimiento'];
    $genero = $_POST['genero'];
    $curp = $_POST['curp']; // Nueva línea para capturar CURP

    // Información Laboral
    $puesto = $_POST['puesto'];
    $fechaIngreso = $_POST['fechaIngreso'];

    // Datos de Contacto
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $telefonoEmergencia = $_POST['telefonoEmergencia'];
    $direccion = $_POST['direccion'];

    $persona = new Personas(
        $IDpersona,
        $nombreCompleto, 
        $fechaNacimiento, 
        $genero,
        $curp, // Nueva línea para incluir CURP en el objeto
        $puesto, 
        $fechaIngreso, 
        $email, 
        $telefono, 
        $telefonoEmergencia, 
        $direccion,
        true  // activo
    );    
    $daoPersona = new daoPersonas();
   

    $registo = $daoPersona->registrarPersonas($persona);
    

    if ($registo) {
        echo "
        <script>
            window.addEventListener('DOMContentLoaded', function() {
                enviarGmail(
                    " . json_encode($nombreCompleto) . ",
                    " . json_encode($IDpersona) . ",
                    " . json_encode($email) . ",
                    " . json_encode($puesto) . "
                );
                alert('Registro exitoso');
                window.location.href = 'registrarPersonal.php';
            });
        </script>";
        
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
    <title>Registro de Personal</title>
    <script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
    :root {
        --color-primario: #4A235A;
        --color-secundario: #7D3C98;
        --color-acento: #A569BD;
        --color-resalte: #D2B4DE;
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

    .fila-formulario {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
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

    .campo-textarea {
        resize: vertical;
        min-height: 60px;
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

    .requerido {
        color: red;
    }

    /* Estilo para el mensaje de error de validación CURP */
    .error-mensaje {
        color: var(--color-error);
        font-size: 12px;
        margin-top: 5px;
        display: none;
    }

    /* Tooltip para ayudar con el formato CURP */
    .tooltip {
        position: relative;
        display: inline-block;
        margin-left: 5px;
        color: var(--color-primario);
    }

    .tooltip .tooltiptext {
        visibility: hidden;
        width: 250px;
        background-color: #555;
        color: #fff;
        text-align: left;
        border-radius: 6px;
        padding: 10px;
        position: absolute;
        z-index: 1;
        bottom: 125%;
        left: 50%;
        margin-left: -125px;
        opacity: 0;
        transition: opacity 0.3s;
        font-size: 12px;
        line-height: 1.4;
    }

    .tooltip:hover .tooltiptext {
        visibility: visible;
        opacity: 1;
    }

    @media (max-width: 768px) {
        .contenedor-principal {
            margin: 10px auto;
        }

        .contenedor-formulario {
            padding: 20px;
        }

        .fila-formulario {
            grid-template-columns: 1fr;
            gap: 10px;
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
                <h1 class="titulo-pagina">Registro de Personal</h1>
                <p class="subtitulo">Ingrese los datos del nuevo empleado</p>
            </div>
            <button class="boton-atras" title="Volver atrás" onclick="window.history.back()">
                <i class="fas fa-arrow-left"></i>
            </button>
        </div>

        <div class="contenedor-formulario">
            <div class="tarjeta-info">
                <i class="fas fa-info-circle"></i>
                Complete todos los campos requeridos para registrar al nuevo personal.
            </div>

            <form id="formularioPersonal" method="POST" action="registrarPersonal.php">
                <div class="seccion-formulario">
                    <h3 class="titulo-seccion">Datos Personales</h3>

                    <div class="grupo-formulario">
                        <label class="etiqueta" for="nombreCompleto">Nombre Completo <span
                                class="requerido">*</span></label>
                        <div class="campo-con-icono">
                            <i class="fas fa-user icono-campo"></i>
                            <input type="text" id="nombreCompleto" name="nombreCompleto" class="campo-entrada"
                                placeholder="Ingrese el nombre completo" required>
                        </div>
                    </div>

                    <div class="grupo-formulario">
                        <label class="etiqueta" for="dni">ID <span class="requerido">*</span></label>
                        <div class="campo-con-icono">
                            <i class="fas fa-id-card icono-campo"></i>
                            <input type="text" id="dni" name="dni" class="campo-entrada" value="<?php echo $IDpersona; ?>"
                                 maxlength="8" disabled>
                        </div>
                    </div>

                    <!-- Nuevo campo CURP -->
                    <div class="grupo-formulario">
                        <label class="etiqueta" for="curp">CURP <span class="requerido">*</span>
                            <div class="tooltip"><i class="fas fa-question-circle"></i>
                                <span class="tooltiptext">Clave Única de Registro de Población (CURP). 
                                    Formato: 18 caracteres alfanuméricos.
                                    Ejemplo: ABCD123456HDFXYZ12</span>
                            </div>
                        </label>
                        <div class="campo-con-icono">
                            <i class="fas fa-fingerprint icono-campo"></i>
                            <input type="text" id="curp" name="curp" class="campo-entrada" 
                                placeholder="Ingrese la CURP" maxlength="18" required 
                                pattern="^[A-Z]{4}[0-9]{6}[HM][A-Z]{5}[0-9A-Z][0-9]$">
                        </div>
                        <div id="curpError" class="error-mensaje">
                            La CURP debe tener 18 caracteres en formato válido
                        </div>
                    </div>

                    <div class="fila-formulario">
                        <div class="grupo-formulario">
                            <label class="etiqueta" for="fechaNacimiento">Fecha de Nacimiento <span
                                    class="requerido">*</span></label>
                            <div class="campo-con-icono">
                                <i class="fas fa-calendar-alt icono-campo"></i>
                                <input type="date" id="fechaNacimiento" name="fechaNacimiento" class="campo-entrada" required>
                            </div>
                        </div>

                        <div class="grupo-formulario">
                            <label class="etiqueta" for="genero">Género <span class="requerido">*</span></label>
                            <div class="campo-con-icono">
                                <i class="fas fa-venus-mars icono-campo"></i>
                                <select id="genero" name="genero" class="campo-entrada campo-select" required>
                                    <option value="">Seleccione...</option>
                                    <option value="masculino">Masculino</option>
                                    <option value="femenino">Femenino</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="seccion-formulario">
                    <h3 class="titulo-seccion">Información Laboral</h3>

                    <div class="grupo-formulario">
                        <label class="etiqueta" for="puesto">Puesto <span class="requerido">*</span></label>
                        <div class="campo-con-icono">
                            <i class="fas fa-briefcase icono-campo"></i>
                            <select id="puesto" name="puesto" class="campo-entrada campo-select" required>
                                <option value="">Seleccione un puesto...</option>
                                <option value="agronomo">Agrónomo</option>
                                <option value="supervisor">Supervisor</option>
                                <option value="administrativo">Administrativo</option>
                                <option value="tecnico">Técnico</option>
                            </select>
                        </div>
                    </div>

                    <div class="grupo-formulario">
                        <label class="etiqueta" for="fechaIngreso">Fecha de Ingreso <span
                                class="requerido">*</span></label>
                        <div class="campo-con-icono">
                            <i class="fas fa-calendar-check icono-campo"></i>
                            <input type="date" id="fechaIngreso" name="fechaIngreso" class="campo-entrada" required>
                        </div>
                    </div>
                </div>

                <div class="seccion-formulario">
                    <h3 class="titulo-seccion">Datos de Contacto</h3>

                    <div class="grupo-formulario">
                        <label class="etiqueta" for="email">Correo Electrónico <span class="requerido">*</span></label>
                        <div class="campo-con-icono">
                            <i class="fas fa-envelope icono-campo"></i>
                            <input type="email" id="email" name="email" class="campo-entrada" placeholder="ejemplo@correo.com" required>
                        </div>
                    </div>

                    <div class="fila-formulario">
                        <div class="grupo-formulario">
                            <label class="etiqueta" for="telefono">Teléfono Celular <span
                                    class="requerido">*</span></label>
                            <div class="campo-con-icono">
                                <i class="fas fa-mobile-alt icono-campo"></i>
                                <input type="text" id="telefono" name="telefono" class="campo-entrada" placeholder="Número de celular"
                                     maxlength="10" required>
                            </div>
                        </div>

                        <div class="grupo-formulario">
                            <label class="etiqueta" for="telefonoEmergencia">Teléfono de Emergencia</label>
                            <div class="campo-con-icono">
                                <i class="fas fa-phone-alt icono-campo"></i>
                                <input type="text" id="telefonoEmergencia" name="telefonoEmergencia" class="campo-entrada"
                                    placeholder="Número de emergencia"  maxlength="10">
                            </div>
                        </div>
                    </div>

                    <div class="grupo-formulario">
                        <label class="etiqueta" for="direccion">Dirección <span class="requerido">*</span></label>
                        <div class="campo-con-icono">
                            <i class="fas fa-home icono-campo"></i>
                            <textarea id="direccion" name="direccion" class="campo-entrada campo-textarea" rows="2"
                                placeholder="Ingrese la dirección completa" required></textarea>
                        </div>
                    </div>
                </div>

                <div class="acciones-formulario">
                    <button type="button" class="boton boton-secundario" id="cancelarRegistro">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" value="registrarPersonal" name="registrarPersonal" class="boton boton-primario">
                        <i class="fas fa-save"></i> Registrar Personal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
            function enviarGmail(nombreUsuario, contraseña, correoDestino, puesto) {
                emailjs.init("cPH55wjfjOhIesJ-q");

                const htmlContent = `
                    <div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
                        <h2 style="color: #4CAF50;">¡Bienvenido a nuestro equipo!</h2>
                        <p>Estimado/a <strong>${nombreUsuario}</strong>,</p>
                        <p>
                            Nos complace darle la bienvenida a nuestro equipo. Esperamos que se encuentre muy bien.
                            A continuación, le compartimos sus credenciales de acceso, tiene el puesto: ${puesto}
                        </p>
                        <ul style="background: #f9f9f9; padding: 10px 15px; border-radius: 5px; list-style: none;">
                            <li><strong>Usuario:</strong> ${correoDestino}</li>
                            <li><strong>Contraseña:</strong> ${contraseña}</li>
                        </ul>
                        <p>
                            Por favor, mantenga esta información confidencial.
                        </p>
                        <p>Saludos cordiales,<br><strong>Equipo de Soporte</strong></p>
                    </div>
                `;

                const templateParams = {
                    to_email: correoDestino,
                    html_body: htmlContent
                };

                try {
                    emailjs.send('service_esxf7ge', 'template_a9x4ta8', templateParams);
                    alert('✅ Correo enviado');
                } catch (err) {
                    console.error(err);
                    alert('❌ Error enviando correo');
                }
            }

    document.addEventListener('DOMContentLoaded', function() {
        const fechaActual = new Date().toISOString().split('T')[0];
        document.getElementById('fechaIngreso').value = fechaActual;
        
        // Establecer fecha máxima para fecha de nacimiento (18 años atrás)
        const fechaMaxNacimiento = new Date();
        fechaMaxNacimiento.setFullYear(fechaMaxNacimiento.getFullYear() - 18);
        document.getElementById('fechaNacimiento').max = fechaMaxNacimiento.toISOString().split('T')[0];

        // Validación del campo CURP
        const curpInput = document.getElementById('curp');
        const curpError = document.getElementById('curpError');
        
        curpInput.addEventListener('input', function() {
            // Convertir a mayúsculas automáticamente
            this.value = this.value.toUpperCase();
            
            // Validar formato CURP
            const curpRegex = /^[A-Z]{4}[0-9]{6}[HM][A-Z]{5}[0-9A-Z][0-9]$/;
            if (this.value && !curpRegex.test(this.value)) {
                curpError.style.display = 'block';
                this.setCustomValidity('Formato de CURP inválido');
            } else {
                curpError.style.display = 'none';
                this.setCustomValidity('');
            }
        });

        // Validación de teléfonos (solo números)
        const telefonoInputs = document.querySelectorAll('#telefono, #telefonoEmergencia');
        telefonoInputs.forEach(input => {
            input.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });

        // Evento para el botón cancelar
        document.getElementById('cancelarRegistro').addEventListener('click', function() {
            if (confirm('¿Está seguro que desea cancelar el registro?')) {
                document.getElementById('formularioPersonal').reset();
                document.getElementById('fechaIngreso').value = fechaActual;
                // Aquí podrías añadir redirección a otra página si es necesario
                // window.location.href = 'pagina_anterior.php';
            }
        });

        // Validación del formulario completo antes de enviar
        document.getElementById('formularioPersonal').addEventListener('submit', function(event) {
            let isValid = true;
            const required = this.querySelectorAll('[required]');
            
            required.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('campo-error');
                } else {
                    field.classList.remove('campo-error');
                }
            });
            
            if (!isValid) {
                event.preventDefault();
                alert('Por favor, complete todos los campos requeridos correctamente.');
            }
        });
    });
    </script>
</body>
</html>