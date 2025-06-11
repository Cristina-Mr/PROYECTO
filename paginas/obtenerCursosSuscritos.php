<?php
require '../db.php';
header('Content-Type: application/json');

$usuarioId = $_GET['usuario_id'];
$sql = "SELECT c.ID, c.Titulo, c.Descripcion, c.Imagen 
        FROM Cursos c
        JOIN Suscripciones s ON c.ID = s.CursoID
        WHERE s.UsuarioID = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $usuarioId);
$stmt->execute();
$result = $stmt->get_result();

$cursos = [];
while ($curso = $result->fetch_assoc()) {
    $cursos[] = $curso;
}

echo json_encode($cursos);

$stmt->close();
$conexion->close();
?>