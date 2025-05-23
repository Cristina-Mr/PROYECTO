<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    die("Debes iniciar sesión para publicar un curso.");
}

$conexion = new mysqli("localhost", "root", "", "proclass");
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $conexion->real_escape_string($_POST['titulo']);
    $tema = $conexion->real_escape_string($_POST['tema']);
    $descripcion = $conexion->real_escape_string($_POST['descripcion']);
    $ubicacion = $conexion->real_escape_string($_POST['ubicacion']);
    $fecha = $_POST['fecha'];
    $duracion = (int) $_POST['duracion'];
    $precio = (float) $_POST['precio'];
    $id_usuario = $_SESSION['usuario_id'];

    // Manejo de la imagen
    $nombreImagen = null;
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $rutaTemp = $_FILES['imagen']['tmp_name'];
        $nombreOriginal = basename($_FILES['imagen']['name']);
        $ext = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
        $extPermitidas = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($ext, $extPermitidas)) {
            $nombreImagen = uniqid('curso_', true) . '.' . $ext;
            $destino = __DIR__ . '/uploads/' . $nombreImagen;
            if (!is_dir(__DIR__ . '/uploads')) {
                mkdir(__DIR__ . '/uploads', 0755, true);
            }
            if (!move_uploaded_file($rutaTemp, $destino)) {
                die("Error al subir la imagen.");
            }
        } else {
            die("Tipo de imagen no permitido.");
        }
    }

    // Verificar o insertar categoría
    $stmt = $conexion->prepare("SELECT ID_cat FROM Categorias WHERE Nombre = ?");
    $stmt->bind_param("s", $tema);
    $stmt->execute();
    $stmt->bind_result($id_cat);
    if (!$stmt->fetch()) {
        $stmt->close();
        $stmt = $conexion->prepare("INSERT INTO Categorias (Nombre) VALUES (?)");
        $stmt->bind_param("s", $tema);
        $stmt->execute();
        $id_cat = $stmt->insert_id;
    }
    $stmt->close();

    // Verificar o insertar ubicación
    $stmt = $conexion->prepare("SELECT ID_ubi FROM Ubicaciones WHERE Nombre = ?");
    $stmt->bind_param("s", $ubicacion);
    $stmt->execute();
    $stmt->bind_result($id_ubi);
    if (!$stmt->fetch()) {
        $stmt->close();
        $stmt = $conexion->prepare("INSERT INTO Ubicaciones (Nombre) VALUES (?)");
        $stmt->bind_param("s", $ubicacion);
        $stmt->execute();
        $id_ubi = $stmt->insert_id;
    }
    $stmt->close();

    // Insertar curso
    $stmt = $conexion->prepare("INSERT INTO Cursos (Titulo, Descripción, ID_cat, Precio, Fecha_inicio, Duracion, Imagen, ID_ubi)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssidsisi", $titulo, $descripcion, $id_cat, $precio, $fecha, $duracion, $nombreImagen, $id_ubi);
    if (!$stmt->execute()) {
        die("Error al insertar curso: " . $stmt->error);
    }
    $id_curso = $stmt->insert_id;
    $stmt->close();

    // Insertar publicación
    $stmt = $conexion->prepare("INSERT INTO Publicaciones (ID_curso, ID_usu) VALUES (?, ?)");
    $stmt->bind_param("ii", $id_curso, $id_usuario);
    $stmt->execute();
    $stmt->close();

    echo "✅ Curso publicado correctamente.";
}

$conexion->close();
?>