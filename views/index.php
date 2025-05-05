<?php
include_once  '../controllers/daoPersonas.php';
include_once  '../models/Personas.php';

$daoPersonal = new daoPersonas();
$listaUsuarios = $daoPersonal->obtenerLogin(); // <- Este método debe devolver un array asociativo

// Crear un array para pasar a JavaScript
$usuariosLoginJS = [];
foreach ($listaUsuarios as $usuario) {
    $pues='';
    if($usuario['puesto']=='Agrónomo'){
        $pues='agronomo';
    }else{
        $pues='supervisor';
    }

    $usuariosLoginJS[] = [
        'nombre' => $usuario['nombre'],
        'correo' => $usuario['correo'],
        'clave' => $usuario['clave'],
        'puesto' => $pues
    ];
}

// Convertir a JSON para insertar en JS
$usuariosLoginJSON = json_encode($usuariosLoginJS);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema Administrativo</title>
    <script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
    <style>
        :root {
            --color-primario: #4A235A;
            --color-secundario: #7D3C98;
            --color-acento: #A569BD;
            --color-resalte: #D2B4DE;
            --color-fondo: #F3F2F7;
            --color-blanco: #FFFFFF;
            --color-error: #E74C3C;
            --color-exito: #2ECC71;
            --fuente-principal: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            --sombra: 0 10px 25px rgba(74, 35, 90, 0.3);
            --sombra-sm: 0 4px 8px rgba(0, 0, 0, 0.1);
            --borde-radio: 12px;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: var(--fuente-principal);
        }
        body {
            background: linear-gradient(135deg, var(--color-primario) 0%, var(--color-secundario) 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            overflow: hidden;
            position: relative;
        }
        .forma-fondo {
            position: absolute;
            background-color: rgba(255, 255, 255, 0.03);
            border-radius: 50%;
        }
        .forma-fondo-1 { width: 300px; height: 300px; top: -100px; left: -100px; }
        .forma-fondo-2 { width: 200px; height: 200px; bottom: -50px; right: 50px; }
        .forma-fondo-3 { width: 150px; height: 150px; bottom: 150px; left: 60%; }
        .contenedor {
            width: 100%;
            max-width: 420px;
            background-color: var(--color-blanco);
            border-radius: var(--borde-radio);
            box-shadow: var(--sombra);
            overflow: hidden;
            position: relative;
            z-index: 10;
        }
        .pestanas {
            display: flex;
            background-color: var(--color-primario);
        }
        .boton-pestana {
            flex: 1;
            background: none;
            border: none;
            padding: 18px 0;
            font-size: 15px;
            font-weight: 600;
            color: var(--color-resalte);
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .boton-pestana.activo {
            color: var(--color-blanco);
            background-color: rgba(255, 255, 255, 0.1);
        }
        .boton-pestana:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }
        .boton-pestana.activo::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: var(--color-acento);
        }
        .contenedor-formulario {
            padding: 30px;
        }
        .tab-formulario {
            display: none;
        }
        .tab-formulario.activo {
            display: block;
            animation: aparecer 0.5s ease forwards;
        }
        @keyframes aparecer {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .grupo-formulario {
            margin-bottom: 20px;
        }
        .etiqueta-formulario {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: #666;
            font-weight: 500;
        }
        .entrada-formulario {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
            background-color: #f9f9f9;
        }
        .entrada-formulario:focus {
            outline: none;
            border-color: var(--color-acento);
            box-shadow: 0 0 0 3px rgba(165, 105, 189, 0.2);
            background-color: white;
        }
        .entrada-formulario::placeholder {
            color: #aaa;
        }
        .boton {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        .boton-primario {
            background: linear-gradient(to right, var(--color-secundario), var(--color-primario));
            color: white;
        }
        .boton-primario:hover {
            background: linear-gradient(to right, var(--color-primario), var(--color-secundario));
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .opciones-formulario {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 15px 0 25px;
        }
        .recordarme {
            display: flex;
            align-items: center;
        }
        .recordarme input { margin-right: 8px; accent-color: var(--color-secundario); }
        .enlace-olvido {
            color: var(--color-secundario);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
        }
        .enlace-olvido:hover {
            color: var(--color-primario);
            text-decoration: underline;
        }
        .pie-formulario {
            text-align: center;
            margin-top: 25px;
            color: #777;
            font-size: 14px;
        }
        .pie-formulario a {
            color: var(--color-secundario);
            text-decoration: none;
            font-weight: 600;
        }
        .pie-formulario a:hover {
            text-decoration: underline;
        }
        .logo {
            text-align: center;
            margin-bottom: 25px;
        }
        .icono-logo {
            font-size: 42px;
            background: linear-gradient(to right, var(--color-primario), var(--color-acento));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }
        .texto-logo {
            font-size: 22px;
            font-weight: 700;
            color: var(--color-primario);
        }
        @media (max-width: 480px) {
            .contenedor { border-radius: 8px; }
            .contenedor-formulario { padding: 20px; }
            .icono-logo { font-size: 36px; }
            .texto-logo { font-size: 18px; }
        }
    </style>
</head>
<body>
    <div class="forma-fondo forma-fondo-1"></div>
    <div class="forma-fondo forma-fondo-2"></div>
    <div class="forma-fondo forma-fondo-3"></div>

    <div class="contenedor">
        <div class="pestanas">
            <button class="boton-pestana activo" data-tab="login">Iniciar Sesión</button>
            <button class="boton-pestana" data-tab="recuperar">Recuperar</button>
        </div>

        <div class="contenedor-formulario">
            <div class="tab-formulario activo" id="login-tab">
                <div class="logo">
                    <div class="icono-logo">👑</div>
                    <div class="texto-logo">Sistema Administrativo</div>
                </div>
                <form id="formulario-login">
                    <div class="grupo-formulario">
                        <label for="login-usuario" class="etiqueta-formulario">Correo</label>
                        <input type="email" id="login-usuario" class="entrada-formulario" placeholder="correo@ejemplo.com" required>
                    </div>
                    <div class="grupo-formulario">
                        <label for="login-clave" class="etiqueta-formulario">Contraseña</label>
                        <input type="password" id="login-clave" class="entrada-formulario" placeholder="Ingresa tu contraseña" required>
                    </div>
                    <div class="opciones-formulario">
                        <div class="recordarme">
                            <input type="checkbox" id="recordar">
                            <label for="recordar">Recordarme</label>
                        </div>
                        <a href="#" class="enlace-olvido" onclick="cambiarPestana('recuperar')">¿Olvidaste tu contraseña?</a>
                    </div>
                    <button type="submit" class="boton boton-primario">Iniciar Sesión</button>
                </form>
            </div>

            <div class="tab-formulario" id="recuperar-tab">
                <div class="logo">
                    <div class="icono-logo">🔑</div>
                    <div class="texto-logo">Recuperar Contraseña</div>
                </div>
                <form id="formulario-recuperar">
                    <div class="grupo-formulario">
                        <label for="recuperar-correo" class="etiqueta-formulario">Correo Electrónico</label>
                        <input type="email" id="recuperar-correo" class="entrada-formulario" placeholder="Ingresa tu correo electrónico" required>
                    </div>
                    <p style="margin-bottom: 20px; font-size: 14px; color: #666;">
                        Se enviará tu contraseña al correo electrónico que proporcionaste.
                    </p>
                    <button type="submit" class="boton boton-primario">Enviar la Recuperación</button>
                </form>
                <div class="pie-formulario">
                    ¿Recordaste tu contraseña? <a href="#" onclick="cambiarPestana('login')">Volver al inicio de sesión</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const usuarios = <?php echo $usuariosLoginJSON ?>

        function cambiarPestana(idPestana) {
            document.querySelectorAll('.tab-formulario').forEach(tab => tab.classList.remove('activo'));
            document.querySelectorAll('.boton-pestana').forEach(button => button.classList.remove('activo'));

            document.getElementById(idPestana + '-tab').classList.add('activo');
            document.querySelector(`.boton-pestana[data-tab="${idPestana}"]`).classList.add('activo');
        }

        document.querySelectorAll('.boton-pestana').forEach(boton => {
            boton.addEventListener('click', () => {
                const idPestana = boton.getAttribute('data-tab');
                cambiarPestana(idPestana);
            });
        });

        document.getElementById('formulario-login').addEventListener('submit', (e) => {
            e.preventDefault();
            const correo = document.getElementById('login-usuario').value.trim();
            const clave = document.getElementById('login-clave').value.trim();

            const usuario = usuarios.find(u => u.correo === correo && u.clave === clave);

            if (usuario) {
                if (usuario.puesto === 'agronomo') {
                    fetch('session.php', {
                    method: 'POST', // 1️⃣ Enviamos una solicitud POST
                    headers: {
                            'Content-Type': 'application/json' // 2️⃣ Indicamos que el cuerpo será JSON
                    },
                        body: JSON.stringify({ nombre: usuario.nombre }) // 3️⃣ Enviamos el nombre como JSON
                    })
                    .then(res => res.text()) // 4️⃣ Cuando el servidor responde, capturamos su respuesta
                    .then(() => {
                        window.location.href = 'menuAgronomo.php'; // 5️⃣ Redirigimos al usuario después de guardar en sesión
                    });
                   
                } else if (usuario.puesto === 'supervisor') {
                    fetch('session.php', {
                    method: 'POST', // 1️⃣ Enviamos una solicitud POST
                    headers: {
                            'Content-Type': 'application/json' // 2️⃣ Indicamos que el cuerpo será JSON
                    },
                        body: JSON.stringify({ nombre: usuario.nombre }) // 3️⃣ Enviamos el nombre como JSON
                    })
                    .then(res => res.text()) // 4️⃣ Cuando el servidor responde, capturamos su respuesta
                    .then(() => {
                         window.location.href = 'menuA.php'; // 5️⃣ Redirigimos al usuario después de guardar en sesión
                    });

                }
            } else {
                alert('Correo o contraseña incorrectos');
            }
        });

         document.getElementById('formulario-recuperar')
                .addEventListener('submit', async e => {
                    e.preventDefault(); // 1️⃣ evita que recargue la página

                    const correo = document.getElementById('recuperar-correo').value.trim();
                    const usuario = usuarios.find(u => u.correo === correo);
                    if (!usuario) {
                    return alert('Correo no registrado');
                    }

                    emailjs.init("cPH55wjfjOhIesJ-q");
                    const htmlContent = `
                    <h2>Recuperación de contraseña</h2>
                    <p>Hola ${usuario.nombre},</p>
                    <p>Tu contraseña es: <strong>${usuario.clave}</strong></p>
                    <p>Saludos cordiales,<br>Equipo de Soporte</p>
                    `;

                    const templateParams = {
                    to_email: correo,        // 2️⃣ usa la variable correcta
                    html_body: htmlContent
                    };

                    try {
                     emailjs.send('service_esxf7ge','template_a9x4ta8', templateParams);
                    alert('✅ Correo enviado');
                    } catch (err) {
                    console.error(err);
                    alert('❌ Error enviando correo');
                    return;
                    }

                    // 3️⃣ finalmente cambiamos de pestaña
                    cambiarPestana('login');
        });

        
    </script>
</body>
</html>
