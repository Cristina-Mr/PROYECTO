<?php
require 'db.php';

$nombre = 'Marcos López';
$email = 'marcos.lopez@gmail.com';
$password = 14112001;

$sql = "INSERT INTO Usuarios (Nombre, Email, Contraseña) VALUES (?, ?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("sss", $nombre, $email, $password);
$stmt->execute();
$stmt->close();
?>

