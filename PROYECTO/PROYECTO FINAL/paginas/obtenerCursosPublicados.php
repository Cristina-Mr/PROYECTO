<?php
require '../db.php';
header('Content-Type: application/json');

$autorId = $_GET['autor_id'];
$sql = "SELECT ID, Titulo, Descripcion, Precio, Imagen FROM Cursos WHERE AutorID = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $autorId);
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