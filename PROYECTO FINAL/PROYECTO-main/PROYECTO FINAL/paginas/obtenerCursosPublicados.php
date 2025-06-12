<?php
// ../api/obtenerCursosPublicados.php

require '../db.php';
session_start();
header('Content-Type: application/json');

// Verifica si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['error' => 'No hay sesión activa']);
    exit;
}

$usuarioId = $_SESSION['usuario_id'];

// Consulta para obtener cursos publicados por el usuario
$sql = "SELECT ID, Titulo, Descripcion, Precio, Imagen FROM Cursos WHERE ID_autor = ?";
$stmt = $conexion->prepare($sql);

if (!$stmt) {
    echo json_encode(['error' => 'Error al preparar la consulta: ' . $conexion->error]);
    exit;
}

$stmt->bind_param("i", $usuarioId);
$stmt->execute();
$resultado = $stmt->get_result();

$cursos = [];
while ($curso = $resultado->fetch_assoc()) {
    $cursos[] = $curso;
}

// Devuelve los cursos en formato JSON
echo json_encode($cursos);

$stmt->close();
$conexion->close();