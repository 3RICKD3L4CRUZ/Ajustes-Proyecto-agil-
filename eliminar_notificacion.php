<?php
session_start();
include 'config/database.php';

// Verificar si está logueado
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['id'])) {
        $notificacion_id = $input['id'];
        $usuario_id = $_SESSION['usuario_id'];
        
        try {
            // Eliminar notificación específica
            $stmt = $pdo->prepare("DELETE FROM notificaciones WHERE id = ? AND usuario_id = ?");
            $stmt->execute([$notificacion_id, $usuario_id]);
            
            echo json_encode(['success' => true, 'message' => 'Notificación eliminada correctamente']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al eliminar la notificación: ' . $e->getMessage()]);
        }
    } elseif (isset($input['eliminar_todas']) && $input['eliminar_todas'] === true) {
        $usuario_id = $_SESSION['usuario_id'];
        
        try {
            // Eliminar todas las notificaciones del usuario
            $stmt = $pdo->prepare("DELETE FROM notificaciones WHERE usuario_id = ?");
            $stmt->execute([$usuario_id]);
            
            echo json_encode(['success' => true, 'message' => 'Todas las notificaciones han sido eliminadas']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al eliminar las notificaciones: ' . $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Datos inválidos']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
}
?>
