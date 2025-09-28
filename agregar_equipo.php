<?php
session_start();
header('Content-Type: application/json');

// Verificar si está logueado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

try {
    include 'config/database.php';
    
    // Obtener datos del formulario
    $nombre = $_POST['nombre'] ?? '';
    $categoria = $_POST['categoria'] ?? '';
    $numeroSerie = $_POST['numeroSerie'] ?? '';
    $estado = $_POST['estado'] ?? '';
    $usuario_id = $_SESSION['usuario_id'];
    
    // Validar datos requeridos
    if (empty($nombre) || empty($categoria) || empty($numeroSerie) || empty($estado)) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos son requeridos']);
        exit();
    }
    
    // Verificar que el número de serie no exista
    $stmt = $pdo->prepare("SELECT id FROM equipos WHERE numero_serie = ?");
    $stmt->execute([$numeroSerie]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'El número de serie ya existe']);
        exit();
    }
    
    // Insertar nuevo equipo
    $stmt = $pdo->prepare("
        INSERT INTO equipos (nombre, categoria, numero_serie, estado, usuario_id, fecha_creacion, fecha_modificacion) 
        VALUES (?, ?, ?, ?, ?, NOW(), NOW())
    ");
    
    $result = $stmt->execute([$nombre, $categoria, $numeroSerie, $estado, $usuario_id]);
    
    if ($result) {
        $equipoId = $pdo->lastInsertId();
        
        // Registrar en historial
        $stmt = $pdo->prepare("
            INSERT INTO historial_equipos (equipo_id, accion, usuario_id, fecha) 
            VALUES (?, 'Equipo creado', ?, NOW())
        ");
        $stmt->execute([$equipoId, $usuario_id]);
        
        echo json_encode(['success' => true, 'message' => 'Equipo agregado correctamente', 'equipoId' => $equipoId]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al agregar el equipo']);
    }
    
} catch (PDOException $e) {
    error_log("Error en agregar_equipo.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()]);
} catch (Exception $e) {
    error_log("Error general en agregar_equipo.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
}
?>
