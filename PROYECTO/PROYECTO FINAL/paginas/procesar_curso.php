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
    $categoria = $conexion->real_escape_string($_POST['categoria']);
    $descripcion = $conexion->real_escape_string($_POST['descripcion']);
    $ubicacion = $conexion->real_escape_string($_POST['ubicacion']);
    $fecha = $_POST['fecha']; // se asume fecha válida
    $duracion = (int) $_POST['duracion'];
    $precio = (float) $_POST['precio'];
    $id_usuario = 1; // Esto deberías obtenerlo de la sesión del usuario logueado

    // Manejo de la imagen subida
    $nombreImagen = null;
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $rutaTemp = $_FILES['imagen']['tmp_name'];
        $nombreOriginal = basename($_FILES['imagen']['name']);
        $ext = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

        // Validar extensión permitida
        $extPermitidas = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($ext, $extPermitidas)) {
            $nombreImagen = uniqid('curso_', true) . '.' . $ext;
            $destino = __DIR__ . '/uploads/' . $nombreImagen;

            // Crear carpeta uploads si no existe
            if (!is_dir(__DIR__ . '/uploads')) {
                mkdir(__DIR__ . '/uploads', 0755, true);
            }

            if (!move_uploaded_file($rutaTemp, $destino)) {
                die("Error al subir la imagen.");
            }
        } else {
            die("Tipo de archivo no permitido para la imagen.");
        }
    }

    // Iniciar transacción para asegurar la integridad de los datos
    $conexion->begin_transaction();

    try {
        // 1. Verificar/insertar la categoría
        $sql_cat = "SELECT ID_cat FROM Categorias WHERE Nombre = ?";
        $stmt_cat = $conexion->prepare($sql_cat);
        $stmt_cat->bind_param("s", $categoria);
        $stmt_cat->execute();
        $result_cat = $stmt_cat->get_result();
        
        if ($result_cat->num_rows > 0) {
            $row_cat = $result_cat->fetch_assoc();
            $id_categoria = $row_cat['ID_cat'];
        } else {
            $sql_new_cat = "INSERT INTO Categorias (Nombre) VALUES (?)";
            $stmt_new_cat = $conexion->prepare($sql_new_cat);
            $stmt_new_cat->bind_param("s", $categoria);
            $stmt_new_cat->execute();
            $id_categoria = $conexion->insert_id;
            $stmt_new_cat->close();
        }
        $stmt_cat->close();

        // 2. Verificar/insertar la ubicación
        $sql_ubi = "SELECT ID_ubi FROM Ubicaciones WHERE Ciudad = ?";
        $stmt_ubi = $conexion->prepare($sql_ubi);
        $stmt_ubi->bind_param("s", $ubicacion);
        $stmt_ubi->execute();
        $result_ubi = $stmt_ubi->get_result();
        
        if ($result_ubi->num_rows > 0) {
            $row_ubi = $result_ubi->fetch_assoc();
            $id_ubicacion = $row_ubi['ID_ubi'];
        } else {
            $sql_new_ubi = "INSERT INTO Ubicaciones (Ciudad, Pais) VALUES (?, 'España')";
            $stmt_new_ubi = $conexion->prepare($sql_new_ubi);
            $stmt_new_ubi->bind_param("s", $ubicacion);
            $stmt_new_ubi->execute();
            $id_ubicacion = $conexion->insert_id;
            $stmt_new_ubi->close();
        }
        $stmt_ubi->close();

        // 3. Insertar el curso
        $sql_curso = "INSERT INTO Cursos (Titulo, Descripcion, ID_cat, Duracion_horas, Precio, Imagen_curso, Fecha_inicio) 
                     VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_curso = $conexion->prepare($sql_curso);
        $stmt_curso->bind_param("ssiidss", $titulo, $descripcion, $id_categoria, $duracion, $precio, $nombreImagen, $fecha);
        $stmt_curso->execute();
        $id_curso = $conexion->insert_id;
        $stmt_curso->close();

        // 4. Crear la publicación (relación curso-usuario-ubicación)
        $sql_pub = "INSERT INTO Publicaciones (ID_curso, ID_usu, ID_ubicacion) VALUES (?, ?, ?)";
        $stmt_pub = $conexion->prepare($sql_pub);
        $stmt_pub->bind_param("iii", $id_curso, $id_usuario, $id_ubicacion);
        $stmt_pub->execute();
        $stmt_pub->close();

        // Confirmar la transacción
        $conexion->commit();
        
        // Redirección o mensaje de éxito
        header("Location: curso_publicado.php?id=$id_curso");
        exit();
        
    } catch (Exception $e) {
        // Revertir en caso de error
        $conexion->rollback();
        die("Error al publicar el curso: " . $e->getMessage());
    }
}

$conexion->close();
?>