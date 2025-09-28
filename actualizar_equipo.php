<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Obtener datos del POST
$equipoId = $_POST['equipoId'] ?? '';
$nombre = $_POST['nombre'] ?? '';
$categoria = $_POST['categoria'] ?? '';
$numeroSerie = $_POST['numeroSerie'] ?? '';
$estado = $_POST['estado'] ?? '';

// Validar datos requeridos
if (empty($equipoId) || empty($nombre) || empty($categoria) || empty($numeroSerie) || empty($estado)) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son requeridos']);
    exit;
}

// Validar estados permitidos
$estadosPermitidos = ['disponible', 'prestado', 'mantenimiento', 'baja'];
if (!in_array($estado, $estadosPermitidos)) {
    echo json_encode(['success' => false, 'message' => 'Estado no válido']);
    exit;
}

try {
    // Actualizar el equipo solo si no ha sido eliminado
    $sql = "UPDATE equipos SET 
            nombre = :nombre, 
            categoria = :categoria, 
            numero_serie = :numero_serie, 
            estado = :estado, 
            fecha_modificacion = CURRENT_TIMESTAMP 
            WHERE id = :id AND eliminado = FALSE";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        ':nombre' => $nombre,
        ':categoria' => $categoria,
        ':numero_serie' => $numeroSerie,
        ':estado' => $estado,
        ':id' => $equipoId
    ]);
    
    if ($result && $stmt->rowCount() > 0) {
        // Registrar en el historial
        $sqlHistorial = "INSERT INTO historial (equipo_id, accion, usuario_id, fecha, detalles) 
                        VALUES (:equipo_id, :accion, :usuario_id, CURRENT_TIMESTAMP, :detalles)";
        $stmtHistorial = $pdo->prepare($sqlHistorial);
        $stmtHistorial->execute([
            ':equipo_id' => $equipoId,
            ':accion' => 'Equipo actualizado',
            ':usuario_id' => $_SESSION['usuario_id'],
            ':detalles' => "Estado cambiado a " . ucfirst($estado) . " - Equipo: " . $nombre
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Equipo actualizado correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se pudo actualizar el equipo o no se encontró']);
    }
    
} catch (PDOException $e) {
    error_log("Error al actualizar equipo: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?>
