<?php
// Conexión a la base de datos
$host = 'localhost';
$db = 'tfg';
$user = 'root';
$pass = ''; // Cambia si tienes contraseña

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al conectar a la base de datos']);
    exit;
}

// Parámetros desde la URL
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$porPagina = isset($_GET['porPagina']) ? (int)$_GET['porPagina'] : 4;
$offset = ($pagina - 1) * $porPagina;

$categoria = $_GET['categoria'] ?? '';
$search = $_GET['search'] ?? '';
$precio = $_GET['precio'] ?? '';
$ubicacion = $_GET['ubicacion'] ?? '';

// Construir consulta
$sql = "SELECT c.ID_curso, c.Titulo, c.Precio, cat.Nombre AS tema, c.Imagen_curso
        FROM Cursos c
        JOIN Categorias cat ON c.ID_cat = cat.ID_cat
        JOIN Publicaciones p ON c.ID_curso = p.ID_curso
        JOIN Ubicaciones u ON p.ID_ubicacion = u.ID_ubi
        WHERE 1 = 1";

$parametros = [];

// Filtros dinámicos
if ($categoria !== '') {
    $sql .= " AND cat.Nombre = :categoria";
    $parametros[':categoria'] = $categoria;
}

if ($search !== '') {
    $sql .= " AND c.Titulo LIKE :search";
    $parametros[':search'] = "%$search%";
}

if ($precio !== '') {
    if ($precio === '200+') {
        $sql .= " AND c.Precio > 200";
    } else {
        [$min, $max] = explode('-', $precio);
        $sql .= " AND c.Precio BETWEEN :minPrecio AND :maxPrecio";
        $parametros[':minPrecio'] = $min;
        $parametros[':maxPrecio'] = $max;
    }
}

if ($ubicacion !== '') {
    $sql .= " AND (u.Ciudad LIKE :ubicacion OR u.Pais LIKE :ubicacion)";
    $parametros[':ubicacion'] = "%$ubicacion%";
}

// Total de cursos (para paginación)
$sqlTotal = str_replace("SELECT c.ID_curso, c.Titulo, c.Precio, cat.Nombre AS tema, c.Imagen_curso", "SELECT COUNT(*) as total", $sql);
$stmtTotal = $pdo->prepare($sqlTotal);
$stmtTotal->execute($parametros);
$totalCursos = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];

// Añadir paginación
$sql .= " LIMIT :offset, :porPagina";
$stmt = $pdo->prepare($sql);

// Bind de parámetros dinámicos
foreach ($parametros as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
$stmt->bindValue(':porPagina', (int)$porPagina, PDO::PARAM_INT);

$stmt->execute();
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Devolver JSON
echo json_encode([
    'cursos' => $cursos,
    'total' => $totalCursos
]);
?>
