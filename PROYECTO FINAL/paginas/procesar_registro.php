<?php
require '../db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['usuario'];
    $email = $_POST['email'];
    $clave = $_POST['clave'];
    $confirmar_clave = $_POST['confirmar-clave'];

    if ($clave !== $confirmar_clave) {
        echo "❌ Las contraseñas no coinciden.";
        exit;
    }

    $clave_encriptada = password_hash($clave, PASSWORD_DEFAULT);

    $sql = "INSERT INTO Usuarios (Nombre, Email, Contraseña) VALUES (?, ?, ?)";
    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        echo "❌ Error preparando la consulta: " . $conexion->error;
        exit;
    }

    $stmt->bind_param("sss", $nombre, $email, $clave_encriptada);

    if ($stmt->execute()) {
        echo "✅ Usuario registrado correctamente.";

        // Redirigir al inicio
        header("Location: inicio.html");
        
    } else {
        echo "❌ Error al registrar: " . $stmt->error;
    }

    $stmt->close();
    $conexion->close();
} else {
    echo "❌ Método no permitido.";
}
?>


