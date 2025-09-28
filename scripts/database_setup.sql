-- Crear base de datos
CREATE DATABASE IF NOT EXISTS sistema_equipos;
USE sistema_equipos;

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso DATETIME NULL,
    activo TINYINT(1) DEFAULT 1
);

-- Tabla de tokens de recuperación
CREATE TABLE tokens_recuperacion (
    usuario_id INT PRIMARY KEY,
    token VARCHAR(64) NOT NULL,
    expira DATETIME NOT NULL,
    usado TINYINT(1) DEFAULT 0,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabla de equipos
CREATE TABLE equipos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    numero_serie VARCHAR(100) UNIQUE NOT NULL,
    estado ENUM('disponible', 'prestado', 'mantenimiento', 'baja') DEFAULT 'disponible',
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    usuario_registro INT,
    FOREIGN KEY (usuario_registro) REFERENCES usuarios(id)
);

-- Tabla de historial de cambios
CREATE TABLE historial_equipos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipo_id INT NOT NULL,
    usuario_id INT NOT NULL,
    accion VARCHAR(50) NOT NULL,
    estado_anterior VARCHAR(50),
    estado_nuevo VARCHAR(50),
    observaciones TEXT,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (equipo_id) REFERENCES equipos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabla de notificaciones
CREATE TABLE notificaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    titulo VARCHAR(100) NOT NULL,
    mensaje TEXT NOT NULL,
    tipo ENUM('info', 'success', 'warning', 'error') DEFAULT 'info',
    leida TINYINT(1) DEFAULT 0,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Insertar usuario administrador por defecto
INSERT INTO usuarios (nombre, email, password) VALUES 
('Administrador', 'admin@sistema.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
-- Contraseña: password

-- Insertando equipos exactos como aparecen en las imágenes
INSERT INTO equipos (nombre, categoria, numero_serie, estado, fecha_registro, fecha_modificacion, usuario_registro) VALUES 
('Laptop Dell Inspiron 15', 'Computadora', 'DL001234', 'disponible', '2024-01-14 10:00:00', '2024-01-14 10:00:00', 1),
('Proyector Epson PowerLite', 'Proyector', 'EP005678', 'prestado', '2024-01-09 09:00:00', '2024-01-19 14:30:00', 1),
('Impresora HP LaserJet', 'Impresora', 'HP009876', 'mantenimiento', '2024-01-04 11:00:00', '2024-01-24 16:45:00', 1),
('Tablet iPad Air', 'Tablet', 'IP004321', 'disponible', '2024-01-19 08:00:00', '2024-01-19 08:00:00', 1);

-- Insertando historial correspondiente a los equipos de las imágenes
INSERT INTO historial_equipos (equipo_id, usuario_id, accion, estado_anterior, estado_nuevo, observaciones, fecha) VALUES 
(1, 1, 'Creado', NULL, 'disponible', 'Equipo registrado en el sistema', '2024-01-14 10:00:00'),
(2, 1, 'Creado', NULL, 'disponible', 'Equipo registrado en el sistema', '2024-01-09 09:00:00'),
(2, 1, 'Estado cambiado', 'disponible', 'prestado', 'Equipo prestado para presentación', '2024-01-19 14:30:00'),
(3, 1, 'Creado', NULL, 'disponible', 'Equipo registrado en el sistema', '2024-01-04 11:00:00'),
(3, 1, 'Estado cambiado', 'disponible', 'mantenimiento', 'Equipo enviado a mantenimiento preventivo', '2024-01-24 16:45:00'),
(4, 1, 'Creado', NULL, 'disponible', 'Equipo registrado en el sistema', '2024-01-19 08:00:00');

-- Insertar notificaciones de ejemplo
INSERT INTO notificaciones (usuario_id, titulo, mensaje, tipo, leida) VALUES 
(1, 'Equipo devuelto', 'El equipo "Laptop Dell Inspiron 15" ha sido devuelto', 'success', 0),
(1, 'Mantenimiento programado', 'El proyector Epson PowerLite requiere mantenimiento', 'warning', 0),
(1, 'Nuevo equipo registrado', 'Se ha registrado un nuevo equipo: Tablet iPad Air', 'info', 1),
(1, 'Equipo en mantenimiento', 'La impresora HP LaserJet está en mantenimiento', 'warning', 0),
(1, 'Sistema actualizado', 'El sistema de gestión ha sido actualizado exitosamente', 'success', 1);
