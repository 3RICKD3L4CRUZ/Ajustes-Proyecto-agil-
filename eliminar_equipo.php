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

// Obtener ID del equipo
$equipoId = $_POST['equipoId'] ?? '';

// Validar que se proporcione el ID
if (empty($equipoId)) {
    echo json_encode(['success' => false, 'message' => 'ID del equipo es requerido']);
    exit;
}

try {
    // Verificar que el equipo esté dado de baja antes de eliminar
    $sqlVerificar = "SELECT estado, nombre FROM equipos WHERE id = :id AND eliminado = FALSE";
    $stmtVerificar = $pdo->prepare($sqlVerificar);
    $stmtVerificar->execute([':id' => $equipoId]);
    $equipo = $stmtVerificar->fetch(PDO::FETCH_ASSOC);
    
    if (!$equipo) {
        echo json_encode(['success' => false, 'message' => 'Equipo no encontrado o ya eliminado']);
        exit;
    }
    
    if ($equipo['estado'] !== 'baja') {
        echo json_encode(['success' => false, 'message' => 'Solo se pueden eliminar equipos que estén dados de baja']);
        exit;
    }
    
    // Marcar el equipo como eliminado (soft delete) en lugar de eliminarlo físicamente
    $sql = "UPDATE equipos SET eliminado = TRUE, fecha_modificacion = CURRENT_TIMESTAMP WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([':id' => $equipoId]);
    
    if ($result && $stmt->rowCount() > 0) {
        // Registrar en el historial
        $sqlHistorial = "INSERT INTO historial (equipo_id, accion, usuario_id, fecha, detalles) 
                        VALUES (:equipo_id, :accion, :usuario_id, CURRENT_TIMESTAMP, :detalles)";
        $stmtHistorial = $pdo->prepare($sqlHistorial);
        $stmtHistorial->execute([
            ':equipo_id' => $equipoId,
            ':accion' => 'Equipo eliminado',
            ':usuario_id' => $_SESSION['usuario_id'],
            ':detalles' => "Equipo eliminado: " . $equipo['nombre']
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Equipo eliminado correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se pudo eliminar el equipo']);
    }
    
} catch (PDOException $e) {
    error_log("Error al eliminar equipo: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?>
