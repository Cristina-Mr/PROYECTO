<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_nombre']) || !isset($_SESSION['usuario_email'])) {
    // Si no hay usuario en sesión, redirigir a la página de login
    header("Location: sesion.html");
    exit();
}

// Obtener datos del usuario desde la sesión
$nombre = $_SESSION['usuario_nombre'];
$email = $_SESSION['usuario_email'];
// Si tienes un avatar guardado en sesión, úsalo, si no, usa un avatar por defecto
$avatar = isset($_SESSION['usuario_avatar']) ? $_SESSION['usuario_avatar'] : '../imagenes/avatar-default.png';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Perfil</title>
    <link rel="stylesheet" href="../estilos/menuTop.css" />
    <link rel="stylesheet" href="../estilos/inicio.css" />
    <link rel="stylesheet" href="../estilos/footer.css" />
    <link rel="stylesheet" href="../estilos/perfil.css" />
</head>
<body>
    <div class="container">
        <header class="menu">
            <div class="logo">
                <a href="inicio.html"><img src="../imagenes/logo.png" alt="Logo" /></a>
            </div>
            <nav>
                <ul>
                    <li class="search-bar">
                        <form action="#" method="get">
                            <input type="text" name="search" placeholder="Buscar..." aria-label="Buscar" />
                        </form>
                    </li>
                    <li><a href="inicio.html">Home</a></li>
                    <li><a href="logout.php">Salir</a></li>
                </ul>
            </nav>
        </header>

        <section class="perfil">
            <div class="perfil-header">
                <div class="avatar">
                    <img src="../imagenes/avatar_default.png" alt="Avatar" id="user-avatar">
                </div>
                <div class="user-info">
                    <h1 id="user-name"><?php echo htmlspecialchars($nombre); ?></h1>
                    <p id="user-email"><?php echo htmlspecialchars($email); ?></p>
                    <button id="edit-profile">Editar perfil</button>
                </div>
            </div>

            <div class="tabs">
                <button class="tab-button active" data-tab="published-courses">Cursos Publicados</button>
                <button class="tab-button" data-tab="subscribed-courses">Cursos Suscritos</button>
                <button class="tab-button" data-tab="reviews">Mis Reseñas</button>
            </div>

            <div class="tab-content active" id="published-courses">
                <h2>Mis Cursos Publicados</h2>
                <div class="courses-grid" id="published-courses-list">
                    <!-- Aquí puedes poner código PHP para mostrar cursos publicados -->
                </div>
            </div>

            <div class="tab-content" id="subscribed-courses">
                <h2>Cursos en los que estoy suscrito</h2>
                <div class="courses-grid" id="subscribed-courses-list">
                    <!-- Aquí puedes poner código PHP para mostrar cursos suscritos -->
                </div>
            </div>

            <div class="tab-content" id="reviews">
                <h2>Mis Reseñas</h2>
                <div class="reviews-list" id="user-reviews">
                    <!-- Aquí puedes poner código PHP para mostrar reseñas -->
                </div>
            </div>
        </section>
    </div>

    <footer class="pie">
        <div class="footer-content">
            <div class="footer-logo">
                <img id="minilogo" src="../imagenes/logo.png" alt="Logo" />
                <span class="copyright-text">© 2025 ProClass</span>
            </div>
            <div class="footer-divider"></div>
            <div class="footer-links">
                <a href="PoliticaPrivacidad.html">Política de privacidad</a>
                <a href="preguntas.html">Preguntas Frecuentes</a>
                <a href="contacto.html">Contacto</a>
            </div>
        </div>
    </footer>

    <script>
        // Cambiar pestañas
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', function () {
                document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

                this.classList.add('active');
                const tabId = this.getAttribute('data-tab');
                document.getElementById(tabId).classList.add('active');
            });
        });
    </script>
</body>
</html>
