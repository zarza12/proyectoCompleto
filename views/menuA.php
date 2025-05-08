<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$nombre = $_SESSION['nombre'] ?? 'Invitado';
$avatar = substr($nombre, 0, 1);
// No cierres el PHP, simplemente empieza el HTML
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Administrador</title>
    <style>
        :root {
            --color-primary: #4A235A; /* Color zarzamora oscuro */
            --color-secondary: #7D3C98; /* Color zarzamora medio */
            --color-accent: #A569BD; /* Color zarzamora claro */
            --color-hover: #D2B4DE; /* Color zarzamora muy claro */
            --color-text: #FFFFFF;
            --color-text-secondary: #F8F9F9;
            --font-primary: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            --shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: var(--font-primary);
            background-color: #f5f5f5;
            height: 100vh;
            margin: 0;
            padding: 0;
            overflow-x: hidden; /* Evita desplazamiento horizontal */
        }
        
        /* Barra superior nueva */
        .barra-superior {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--color-primary), var(--color-accent), var(--color-secondary));
            z-index: 2000;
        }
        
        /* Sección de usuario en la parte superior derecha */
        .info-usuario {
            position: fixed;
            top: 15px;
            right: 70px;
            display: flex;
            align-items: center;
            background-color: var(--color-primary);
            padding: 6px 15px;
            border-radius: 20px;
            color: var(--color-text);
            font-size: 14px;
            box-shadow: var(--shadow);
            z-index: 1200;
            transition: all 0.3s ease;
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
        
        /* Toggle para menú móvil */
        #menuToggle {
            display: none;
            position: fixed;
            top: 15px;
            right: 15px;
            z-index: 1100;
            background-color: var(--color-secondary);
            color: var(--color-text);
            border: none;
            border-radius: 50%;
            width: 45px;
            height: 45px;
            font-size: 22px;
            cursor: pointer;
            box-shadow: var(--shadow);
        }
        
        /* Menú principal con animación de expansión */
        #menuPrincipal {
            width: 70px; /* Ancho del menú colapsado */
            height: 100vh;
            background: linear-gradient(145deg, var(--color-primary), var(--color-secondary));
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            position: fixed;
            left: 0;
            top: 0;
            transition: all 0.3s ease;
            z-index: 1000;
        }
        
        #menuPrincipal:hover {
            width: 270px; /* Ancho del menú expandido */
        }
        
        #encabezado {
            padding: 20px 0;
            background-color: rgba(0, 0, 0, 0.1);
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: center;
            overflow: hidden;
            margin-top: 4px; /* Espacio para la barra superior */
        }
        
        #encabezado h1 {
            color: var(--color-text);
            font-size: 0; /* Inicialmente oculto */
            font-weight: 600;
            letter-spacing: 1px;
            white-space: nowrap;
            transition: font-size 0.3s ease, margin 0.3s ease;
        }
        
        #menuPrincipal:hover #encabezado h1 {
            font-size: 22px;
            margin: 0 auto;
        }
        
        #logoIcon {
            font-size: 26px;
            color: var(--color-text);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        #menuPrincipal:hover #logoIcon {
            display: none;
        }
        
        #nombreUsuario {
            color: var(--color-hover);
            font-size: 0; /* Inicialmente oculto */
            font-weight: 400;
            display: block;
            padding: 5px;
            border-radius: 20px;
            background-color: rgba(255, 255, 255, 0.1);
            margin-top: 10px;
            white-space: nowrap;
            transition: font-size 0.3s ease;
            overflow: hidden;
        }
        
        #menuPrincipal:hover #nombreUsuario {
            font-size: 16px;
        }
        
        #navegacion {
            overflow-y: auto;
            flex-grow: 1;
            padding: 10px 0;
        }
        
        #navegacion ul {
            list-style: none;
        }
        
        /* Nueva clase para categorías desplegables */
        .categoria-container {
            margin: 10px 0;
        }
        
        .categoria {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 15px;
            color: var(--color-hover);
            font-size: 0; /* Inicialmente oculto */
            text-transform: uppercase;
            letter-spacing: 1px;
            white-space: nowrap;
            transition: all 0.3s ease;
            overflow: hidden;
            cursor: pointer;
            border-radius: 8px;
            margin: 0 5px;
        }
        
        #menuPrincipal:hover .categoria {
            font-size: 14px;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .categoria:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        .categoria-icon {
            font-size: 20px;
            color: var(--color-text);
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 30px;
            text-align: center;
        }
        
        .categoria-title {
            transition: font-size 0.3s ease;
            font-size: 0;
            overflow: hidden;
        }
        
        #menuPrincipal:hover .categoria-title {
            font-size: 14px;
        }
        
        .categoria-arrow {
            font-size: 0;
            transition: font-size 0.3s ease, transform 0.3s ease;
        }
        
        #menuPrincipal:hover .categoria-arrow {
            font-size: 14px;
        }
        
        .categoria.active .categoria-arrow {
            transform: rotate(90deg);
        }
        
        /* Contenedor para las subcategorías */
        .subcategorias {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease;
        }
        
        .subcategorias.show {
            max-height: 1000px; /* Alto suficiente para que quepa el contenido */
        }
        
        .elementoMenu {
            margin: 5px 0;
            border-radius: 8px;
            transition: all 0.3s ease;
            padding: 0 10px;
            margin-left: 10px;
        }
        
        #menuPrincipal:hover .elementoMenu {
            padding: 0 15px;
            margin-left: 20px;
        }
        
        .elementoMenu:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(3px);
            border-left: 4px solid var(--color-hover);
        }
        
        .elementoMenu a {
            display: flex;
            align-items: center;
            padding: 12px 5px;
            text-decoration: none;
            color: var(--color-text-secondary);
            font-size: 15px;
            white-space: nowrap;
        }
        
        #menuPrincipal:hover .elementoMenu a {
            padding: 12px 10px;
        }
        
        .icono {
            font-size: 20px;
            min-width: 30px;
            text-align: center;
            display: inline-flex;
            justify-content: center;
            align-items: center;
        }
        
        .menuTexto {
            font-size: 0; /* Inicialmente oculto */
            transition: font-size 0.3s ease;
            overflow: hidden;
        }
        
        #menuPrincipal:hover .menuTexto {
            font-size: 15px;
            margin-left: 5px;
        }
        
        #piePagina {
            padding: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            background-color: rgba(0, 0, 0, 0.15);
        }
        
        #piePagina a {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: var(--color-text-secondary);
            padding: 8px;
            border-radius: 8px;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        
        #piePagina a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .logoutText {
            font-size: 0; /* Inicialmente oculto */
            transition: font-size 0.3s ease;
            overflow: hidden;
        }
        
        #menuPrincipal:hover .logoutText {
            font-size: 15px;
            margin-left: 10px;
        }
        
        /* Custom scrollbar */
        #navegacion::-webkit-scrollbar {
            width: 4px;
        }
        
        #navegacion::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
        }
        
        #navegacion::-webkit-scrollbar-thumb {
            background: var(--color-accent);
            border-radius: 10px;
        }
        
        /* Animaciones */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateX(-10px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        .elementoMenu {
            animation: fadeIn 0.3s ease forwards;
            opacity: 0;
        }
        
        .elementoMenu:nth-child(1) { animation-delay: 0.1s; }
        .elementoMenu:nth-child(2) { animation-delay: 0.2s; }
        .elementoMenu:nth-child(3) { animation-delay: 0.3s; }
        .elementoMenu:nth-child(4) { animation-delay: 0.4s; }
        
        /* Área de contenido principal */
        #contenidoPrincipal {
            margin-left: 70px; /* Mismo ancho que el menú colapsado */
            padding: 20px;
            transition: margin-left 0.3s ease;
            padding-top: 70px; /* Espacio para incluir la barra superior */
        }
        
        #menuPrincipal:hover ~ #contenidoPrincipal {
            margin-left: 270px; /* Mismo ancho que el menú expandido */
        }
        
        /* Título de sección */
        .titulo-seccion {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .titulo-seccion h2 {
            color: var(--color-primary);
            font-size: 24px;
            margin-right: 15px;
        }
        
        /* Responsive para móviles */
        @media (max-width: 768px) {
            #menuToggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .info-usuario {
                right: 70px;
            }
            
            #menuPrincipal {
                width: 0; /* Oculto por defecto en móviles */
                left: -300px; /* Fuera de la pantalla */
            }
            
            #menuPrincipal.activo {
                width: 100%; /* Ancho completo en móviles */
                left: 0; /* Visible */
            }
            
            #menuPrincipal:hover {
                width: 100%; /* Ancho completo en móviles */
            }
            
            #encabezado h1 {
                font-size: 22px; /* Siempre visible en móviles cuando el menú está activo */
                margin: 0 auto;
            }
            
            #logoIcon {
                display: none; /* Ocultar en móviles */
            }
            
            #nombreUsuario {
                font-size: 16px; /* Siempre visible en móviles cuando el menú está activo */
            }
            
            .categoria-title {
                font-size: 14px; /* Siempre visible en móviles cuando el menú está activo */
            }
            
            .categoria-arrow {
                font-size: 14px; /* Siempre visible en móviles cuando el menú está activo */
            }
            
            .menuTexto {
                font-size: 15px; /* Siempre visible en móviles cuando el menú está activo */
                margin-left: 5px;
            }
            
            .logoutText {
                font-size: 15px; /* Siempre visible en móviles cuando el menú está activo */
                margin-left: 10px;
            }
            
            #contenidoPrincipal {
                margin-left: 0; /* Sin margen en móviles */
                padding: 20px;
                padding-top: 70px; /* Espacio para el botón de menú */
            }
            
            #menuPrincipal:hover ~ #contenidoPrincipal {
                margin-left: 0; /* Sin margen en móviles */
            }
            
            /* Estilos específicos para menú móvil activo */
            body.menu-activo {
                overflow: hidden; /* Evita scroll cuando el menú está abierto */
            }
            
            /* Fondo oscuro cuando el menú está abierto */
            .overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 990;
            }
            
            .overlay.activo {
                display: block;
            }
            
            .info-usuario {
                top: 15px;
                right: 70px;
            }
        }
        @media (hover: none) and (pointer: coarse) {
  #menuPrincipal {
    transition: none;
    width: 70px;        /* siempre colapsado */
  }
  /* .activo se añade por JS al tocar el botón de menú */
  #menuPrincipal.activo {
    width: 270px;       /* instantáneo, sin animación */
  }

  /* Además anulamos todas las transiciones y sombras costosas */
  #menuPrincipal *,
  #menuPrincipal {
    transition: none !important;
    box-shadow: none !important;
  }
}
    </style>
</head>
<body>
    <!-- Barra superior con el degradado -->
    <div class="barra-superior"></div>
    
    <!-- Información del usuario en la parte superior derecha -->
    <div class="info-usuario">
        <div class="avatar-usuario"><?php echo $avatar ?></div>
        <span id="usuario-nombre"><?php echo $nombre ?></span>
    </div>
    
    <!-- Botón de toggle para móviles -->
    <button id="menuToggle" aria-label="Abrir menú">
        ☰
    </button>
    
    <!-- Overlay para fondo oscuro en móviles -->
    <div class="overlay" id="overlay"></div>
    
    <div id="menuPrincipal">
        <div id="encabezado">
            <div id="logoIcon">👑</div>
            <div>
                <h1>Panel Admin</h1>
                <label id="nombreUsuario"><?php echo $nombre ?></label>
            </div>
        </div>
        
        <nav id="navegacion">
            <ul>
            <li class="elementoMenu">
                            <a href="/views/sectores.php">
                                <label class="icono">✏️</label>
                                <label class="menuTexto">Registrar Sectores</label>
                            </a>
                        </li>
                <!-- Categoría Producción -->
                <div class="categoria-container">
                    <div class="categoria" onclick="toggleSubcategoria(this, 'produccion-menu')">
                        <div class="categoria-icon">🌱</div>
                        <div class="categoria-title">Producción</div>
                        <div class="categoria-arrow">▶</div>
                    </div>
                    <div class="subcategorias" id="produccion-menu">
                        <li class="elementoMenu">
                            <a href="/views/produccion/registrarProduccion.php">
                                    
                                <label class="icono">➕</label>
                                <label class="menuTexto">Registrar Producción</label>
                            </a>
                        </li>
                        <li class="elementoMenu">
                            <a href="/views/produccion/modificarProduccion.php">
                                <label class="icono">✏️</label>
                                <label class="menuTexto">Modificar Producción</label>
                            </a>
                        </li>
                        <li class="elementoMenu">
                            <a href="/views/produccion/eliminarProduccion.php">
                                <label class="icono">🗑️</label>
                                <label class="menuTexto">Eliminar Producción</label>
                            </a>
                        </li>
                        <li class="elementoMenu">
                            <a href="/views/produccion/historialProduccion.php" >
                                <label class="icono">📋</label>
                                <label class="menuTexto">Historial de Producción</label>
                            </a>
                        </li>
                    </div>
                </div>

                <li class="elementoMenu">
                            <a href="/views/inventario.php">
                                <label class="icono">📋</label>
                                <label class="menuTexto">Inventario</label>
                            </a>
                </li>

                <!-- Categoría Entregas -->
                <div class="categoria-container">
                    <div class="categoria" onclick="toggleSubcategoria(this, 'entregas-menu')">
                        <div class="categoria-icon">🚚</div>
                        <div class="categoria-title">Entregas</div>
                        <div class="categoria-arrow">▶</div>
                    </div>
                    <div class="subcategorias" id="entregas-menu">
                        <li class="elementoMenu">
                            <a href="/views/entregas/registrarEntregas.php">
                                <label class="icono">➕</label>
                                <label class="menuTexto">Registrar Entrega</label>
                            </a>
                        </li>
                        <li class="elementoMenu">
                            <a href="/views/entregas/modificarEntregas.php">
                                <label class="icono">✏️</label>
                                <label class="menuTexto">Modificar Entrega</label>
                            </a>
                        </li>
                        <li class="elementoMenu">
                            <a href="/views/entregas/eliminarEntregas.php">
                                <label class="icono">🗑️</label>
                                <label class="menuTexto">Eliminar Entrega</label>
                            </a>
                        </li>
                        <li class="elementoMenu">
                            <a href="/views/entregas/historialEntregas.php">
                                <label class="icono">📊</label>
                                <label class="menuTexto">Historial de Entregas</label>
                            </a>
                        </li>
                    </div>
                </div>

                <!-- Categoría Tratamientos -->
                <div class="categoria-container">
                    <div class="categoria" onclick="toggleSubcategoria(this, 'tratamientos-menu')">
                        <div class="categoria-icon">📝</div>
                        <div class="categoria-title">Tratamientos</div>
                        <div class="categoria-arrow">▶</div>
                    </div>
                    <div class="subcategorias" id="tratamientos-menu">
                        <li class="elementoMenu">
                            <a href="/views/tratamientos/registrarTratamiento.php">
                                <label class="icono">➕</label>
                                <label class="menuTexto">Registrar Receta</label>
                            </a>
                        </li>
                        <li class="elementoMenu">
                            <a href="/views/tratamientos/modificarTratamiento.php">
                                <label class="icono">✏️</label>
                                <label class="menuTexto">Modificar Receta</label>
                            </a>
                        </li>
                        <li class="elementoMenu">
                            <a href="/views/tratamientos/historialTratamiento.php">
                                <label class="icono">📚</label>
                                <label class="menuTexto">Historial</label>
                            </a>
                        </li>
                    </div>
                </div>

                <!-- Categoría Personal -->
                <div class="categoria-container">
                    <div class="categoria" onclick="toggleSubcategoria(this, 'personal-menu')">
                        <div class="categoria-icon">👥</div>
                        <div class="categoria-title">Personal</div>
                        <div class="categoria-arrow">▶</div>
                    </div>
                    <div class="subcategorias" id="personal-menu">
                        <li class="elementoMenu">
                            <a href="/views/personal/registrarPersonal.php">
                                <label class="icono">➕</label>
                                <label class="menuTexto">Registrar Personal</label>
                            </a>
                        </li>
                        <li class="elementoMenu">
                            <a href="/views/personal/modificarPersonal.php">
                                <label class="icono">✏️</label>
                                <label class="menuTexto">Modificar Personal</label>
                            </a>
                        </li>
                        <li class="elementoMenu">
                            <a href="/views/personal/listarPersonal.php" >
                                <label class="icono">📋</label>
                                <label class="menuTexto">Listar Personal</label>
                            </a>
                        </li>
                        <li class="elementoMenu">
                            <a href="/views/personal/eliminarPersonal.php">
                                <label class="icono">🗑️</label>
                                <label class="menuTexto">Eliminar Personal</label>
                            </a>
                        </li>
                    </div>
                </div>

                <li class="elementoMenu">
                            <a href="/views/ventasRealisadas.php">
                                <label class="icono">📋</label>
                                <label class="menuTexto">Ventas Realizadas</label>
                            </a>
                </li>

            </ul>
        </nav>

        <div id="piePagina">
            <a href="/views/sessionEliminar.php">
                <label class="icono">🚪</label>
                <label class="logoutText">Cerrar Sesión</label>
            </a>
        </div>
    </div>
    
   
    
    <script>

        // Función para mostrar/ocultar las subcategorías
        function toggleSubcategoria(elemento, id) {
            // Verifica si el elemento clickeado ya está activo
            const isActive = elemento.classList.contains('active');
            
            // Cierra todas las subcategorías abiertas
            const todasCategorias = document.querySelectorAll('.categoria');
            const todasSubcategorias = document.querySelectorAll('.subcategorias');
            
            todasCategorias.forEach(cat => {
                cat.classList.remove('active');
            });
            
            todasSubcategorias.forEach(subcat => {
                subcat.classList.remove('show');
            });
            
            // Si la categoría clickeada no estaba activa, ábrela
            if (!isActive) {
                elemento.classList.add('active');
                document.getElementById(id).classList.add('show');
            }
            // Si ya estaba activa, permanecerá cerrada porque ya quitamos las clases
        }
    
        // Función para controlar el menú en dispositivos móviles
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menuToggle');
            const menuPrincipal = document.getElementById('menuPrincipal');
            const overlay = document.getElementById('overlay');
            
            // Abrir/cerrar menú al hacer clic en el botón
            menuToggle.addEventListener('click', function() {
                menuPrincipal.classList.toggle('activo');
                overlay.classList.toggle('activo');
                document.body.classList.toggle('menu-activo');
                
                // Cambiar el ícono del botón
                menuToggle.innerHTML = menuPrincipal.classList.contains('activo') ? '✕' : '☰';
            });
            
            // Cerrar el menú al hacer clic en el overlay
            overlay.addEventListener('click', function() {
                menuPrincipal.classList.remove('activo');
                overlay.classList.remove('activo');
                document.body.classList.remove('menu-activo');
                menuToggle.innerHTML = '☰';
            });
            
            // Cerrar el menú al hacer clic en una opción (en móviles)
            const menuItems = document.querySelectorAll('.elementoMenu a');
            menuItems.forEach(item => {
                item.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        menuPrincipal.classList.remove('activo');
                        overlay.classList.remove('activo');
                        document.body.classList.remove('menu-activo');
                        menuToggle.innerHTML = '☰';
                    }
                });
            });
            
            // Ajustar comportamiento en cambio de tamaño de ventana
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    menuPrincipal.classList.remove('activo');
                    overlay.classList.remove('activo');
                    document.body.classList.remove('menu-activo');
                    menuToggle.innerHTML = '☰';
                }
            });
        });
    </script>
</body>
</html>