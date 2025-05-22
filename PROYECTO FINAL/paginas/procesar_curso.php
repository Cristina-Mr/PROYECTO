<?php
// Configuración de la conexión
$conexion = new mysqli("localhost", "root", "", "proclass");
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Revisar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger y sanitizar datos
    $titulo = $conexion->real_escape_string($_POST['titulo']);
    $tema = $conexion->real_escape_string($_POST['tema']);
    $descripcion = $conexion->real_escape_string($_POST['descripcion']);
    $ubicacion = $conexion->real_escape_string($_POST['ubicacion']);
    $fecha = $_POST['fecha']; // se asume fecha válida
    $duracion = (int) $_POST['duracion'];
    $precio = (float) $_POST['precio'];

    // Manejo de la imagen subida
    $nombreImagen = null;
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $rutaTemp = $_FILES['imagen']['tmp_name'];
        $nombreOriginal = basename($_FILES['imagen']['name']);
        $ext = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

        // Validar extensión permitida (opcional)
        $extPermitidas = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($ext, $extPermitidas)) {
            $nombreImagen = uniqid('curso_', true) . '.' . $ext;
            $destino = __DIR__ . '/uploads/' . $nombreImagen;

            // Crear carpeta uploads si no existe
            if (!is_dir(__DIR__ . '/uploads')) {
                mkdir(__DIR__ . '/uploads', 0755, true);
            }

            if (!move_uploaded_file($rutaTemp, $destino)) {
                echo "Error al subir la imagen.";
                exit;
            }
        } else {
            echo "Tipo de archivo no permitido para la imagen.";
            exit;
        }
    }

    // Insertar datos en la tabla cursos
    $sql = "INSERT INTO cursos (titulo, tema, descripcion, ubicacion, fecha_inicio, duracion, precio, imagen) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sssssids", $titulo, $tema, $descripcion, $ubicacion, $fecha, $duracion, $precio, $nombreImagen);

    if ($stmt->execute()) {
        echo "✅ Curso publicado correctamente.";
    } else {
        echo "❌ Error al publicar el curso: " . $stmt->error;
    }

    $stmt->close();
}

$conexion->close();
?>
