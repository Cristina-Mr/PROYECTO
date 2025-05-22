<?php
// Conexión a la base de datos 'proclass'
$conexion = new mysqli("localhost", "root", "", "proclass");

// Verificar conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Recoger datos del formulario
$usuario = $_POST['usuario'];
$email = $_POST['email'];
$clave = password_hash($_POST['clave'], PASSWORD_DEFAULT); // Encriptar la contraseña

// Insertar datos en la tabla 'usuarios'
$sql = "INSERT INTO usuarios (usuario, email, clave) VALUES (?, ?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("sss", $usuario, $email, $clave);

if ($stmt->execute()) {
    echo "✅ Usuario registrado correctamente.";
} else {
    echo "❌ Error al registrar: " . $stmt->error;
}

$stmt->close();
$conexion->close();
?>
