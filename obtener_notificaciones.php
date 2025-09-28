<?php
session_start();
header('Content-Type: application/json');

// Verificar si está logueado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

try {
    include 'config/database.php';
    
    $usuario_id = $_SESSION['usuario_id'];
    
    // Obtener notificaciones del usuario
    $stmt = $pdo->prepare("
        SELECT id, titulo, mensaje, tipo, fecha_creacion, leida 
        FROM notificaciones 
        WHERE usuario_id = ? 
        ORDER BY fecha_creacion DESC
    ");
    $stmt->execute([$usuario_id]);
    $notificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'notificaciones' => $notificaciones]);
    
} catch (PDOException $e) {
    error_log("Error en obtener_notificaciones.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error de base de datos']);
} catch (Exception $e) {
    error_log("Error general en obtener_notificaciones.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
}
?>
