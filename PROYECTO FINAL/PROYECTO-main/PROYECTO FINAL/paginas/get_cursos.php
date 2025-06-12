<?php
header('Content-Type: application/json');

// Configuración de la conexión
$conexion = new mysqli("localhost", "root", "", "tfg");
if ($conexion->connect_error) {
    die(json_encode(['error' => "Conexión fallida: " . $conexion->connect_error]));
}

// Obtener parámetros de filtrado
$categoria = isset($_GET['categoria']) ? $conexion->real_escape_string($_GET['categoria']) : '';
$precio = isset($_GET['precio']) ? $_GET['precio'] : '';
$ubicacion = isset($_GET['ubicacion']) ? $conexion->real_escape_string($_GET['ubicacion']) : '';
$orden = isset($_GET['orden']) ? $_GET['orden'] : 'fecha_reciente';
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$porPagina = isset($_GET['porPagina']) ? (int)$_GET['porPagina'] : 6;

// Construir consulta SQL base
$sql = "SELECT * FROM cursos WHERE 1=1";
$where = [];

// Aplicar filtros
if (!empty($categoria)) {
    $where[] = "tema = '$categoria'";
}

if (!empty($precio)) {
    list($min, $max) = explode('-', $precio);
    if ($max === '+') {
        $where[] = "precio >= $min";
    } else {
        $where[] = "precio BETWEEN $min AND $max";
    }
}

if (!empty($ubicacion)) {
    $where[] = "ubicacion LIKE '%$ubicacion%'";
}

// Añadir condiciones WHERE si existen
if (!empty($where)) {
    $sql .= " AND " . implode(' AND ', $where);
}

// Aplicar ordenación
switch ($orden) {
    case 'precio_asc':
        $sql .= " ORDER BY precio ASC";
        break;
    case 'precio_desc':
        $sql .= " ORDER BY precio DESC";
        break;
    case 'valoracion':
        // Asumiendo que hay un campo de valoración
        $sql .= " ORDER BY valoracion DESC";
        break;
    default:
        $sql .= " ORDER BY fecha_inicio DESC";
}

// Contar total de resultados (para paginación)
$resultTotal = $conexion->query(str_replace('SELECT *', 'SELECT COUNT(*) as total', $sql));
$totalCursos = $resultTotal->fetch_assoc()['total'];

// Aplicar paginación
$offset = ($pagina - 1) * $porPagina;
$sql .= " LIMIT $offset, $porPagina";

// Ejecutar consulta final
$result = $conexion->query($sql);

if (!$result) {
    echo json_encode(['error' => "Error en la consulta: " . $conexion->error]);
    exit;
}

$cursos = [];
while ($row = $result->fetch_assoc()) {
    $cursos[] = $row;
}

echo json_encode([
    'cursos' => $cursos,
    'total' => $totalCursos
]);

$conexion->close();
?>