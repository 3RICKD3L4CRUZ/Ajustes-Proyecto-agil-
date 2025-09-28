<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

try {
    // Obtener todos los equipos que no han sido eliminados
    $sql = "SELECT id, nombre, categoria, numero_serie, estado, 
            DATE_FORMAT(fecha_creacion, '%d/%m/%Y') as fecha_creacion,
            DATE_FORMAT(fecha_modificacion, '%d/%m/%Y') as fecha_modificacion
            FROM equipos 
            WHERE eliminado = FALSE
            ORDER BY fecha_creacion DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $equipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'equipos' => $equipos]);
    
} catch (PDOException $e) {
    error_log("Error al obtener equipos: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?>
