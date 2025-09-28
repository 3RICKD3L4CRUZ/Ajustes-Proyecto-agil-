<?php
session_start();

// Verificar si está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

include 'config/database.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Equipos</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
</head>
<body class="dashboard-body">
    <!-- Menú Lateral -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo-container">
                <i class="fas fa-laptop"></i>
                <span class="logo-text">Sistema de Equipos</span>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <div class="sidebar-menu">
            <!-- Agregando los 3 apartados nuevos al menú -->
            <a href="#" class="menu-item active" onclick="showSection('catalogo')">
                <i class="fas fa-desktop"></i>
                <span class="menu-text">Catálogo</span>
            </a>
            <a href="#" class="menu-item" onclick="showSection('prestamos')">
                <i class="fas fa-handshake"></i>
                <span class="menu-text">Zona de Préstamo</span>
            </a>
            <a href="#" class="menu-item" onclick="showSection('perfil')">
                <i class="fas fa-user-cog"></i>
                <span class="menu-text">Perfil</span>
            </a>
            <a href="#" class="menu-item" onclick="showSection('notificaciones')">
                <i class="fas fa-bell"></i>
                <span class="menu-text">Notificaciones</span>
            </a>
        </div>

        <div class="sidebar-footer">
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span class="user-name"><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></span>
            </div>
            <a href="logout.php" class="menu-item logout">
                <i class="fas fa-sign-out-alt"></i>
                <span class="menu-text">Cerrar Sesión</span>
            </a>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <main class="main-content" id="mainContent">
        <!-- Header con degradado turquesa-púrpura como en la imagen -->
        <header class="header-gradient">
            <div class="header-content">
                <h1 id="pageTitle">Catálogo de Equipos</h1>
                <div class="header-actions">
                    <button class="header-btn" onclick="showSection('notificaciones')" title="Notificaciones">
                        <i class="fas fa-bell"></i>
                        <span class="notification-dot" id="notificationDot"></span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Sección Catálogo de Equipos -->
        <section id="catalogo" class="section active wow animate__animated animate__fadeIn">
            <div class="catalogo-container">
                <!-- Filtros -->
                <div class="filters-section">
                    <div class="filters-row">
                        <div class="filter-item">
                            <input type="text" id="filtroNombre" placeholder="Buscar equipo..." onkeyup="filtrarEquipos()">
                        </div>
                        <div class="filter-item">
                            <select id="filtroCategoria" onchange="filtrarEquipos()">
                                <option value="">Todas las categorías</option>
                                <option value="Computadora">Computadora</option>
                                <option value="Proyector">Proyector</option>
                                <option value="Impresora">Impresora</option>
                                <option value="Tablet">Tablet</option>
                            </select>
                        </div>
                        <div class="filter-item">
                            <select id="filtroEstado" onchange="filtrarEquipos()">
                                <option value="">Todos los estados</option>
                                <option value="disponible">Disponible</option>
                                <option value="prestado">Prestado</option>
                                <option value="mantenimiento">En Mantenimiento</option>
                                <option value="baja">Dado de Baja</option>
                            </select>
                        </div>
                        <div class="filter-item">
                            <button class="btn-add" onclick="openModal('equipoModal')">
                                <i class="fas fa-plus"></i> Agregar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Grid de equipos con diseño simple como en la imagen -->
                <div class="equipos-grid-simple" id="equiposGrid">
                    <!-- Los equipos se cargarán dinámicamente -->
                </div>
            </div>
        </section>

        <!-- Sección Historial -->
        <section id="historial" class="section">
            <div class="container-centered">
                <article class="historial-container" id="historialContainer">
                    <!-- El historial se cargará dinámicamente -->
                </article>
            </div>
        </section>

        <!-- Sección Zona de Préstamo -->
        <section id="prestamos" class="section">
            <div class="container-centered">
                <div class="section-header-centered">
                    <button class="btn btn-primary" onclick="openModal('prestamoModal')">
                        <i class="fas fa-plus"></i> Nuevo Préstamo
                    </button>
                </div>

                <article class="prestamos-container" id="prestamosContainer">
                    <!-- Los préstamos se cargarán dinámicamente -->
                </article>
            </div>
        </section>

        <!-- Sección Perfil -->
        <section id="perfil" class="section">
            <div class="container-centered">
                <article class="perfil-card">
                    <div class="perfil-header">
                        <i class="fas fa-user-circle perfil-avatar"></i>
                        <div class="perfil-info">
                            <h2><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></h2>
                            <p><?php echo htmlspecialchars($_SESSION['usuario_email']); ?></p>
                        </div>
                    </div>
                    
                    <form id="perfilForm" class="perfil-form">
                        <div class="form-group">
                            <label for="perfilNombre">Nombre:</label>
                            <input type="text" id="perfilNombre" value="<?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="perfilEmail">Email:</label>
                            <input type="email" id="perfilEmail" value="<?php echo htmlspecialchars($_SESSION['usuario_email']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="perfilPassword">Nueva Contraseña:</label>
                            <input type="password" id="perfilPassword" placeholder="Dejar vacío para mantener actual">
                        </div>
                        <div class="form-group">
                            <label for="perfilPasswordConfirm">Confirmar Contraseña:</label>
                            <input type="password" id="perfilPasswordConfirm" placeholder="Confirmar nueva contraseña">
                        </div>
                        <div class="perfil-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </article>
            </div>
        </section>

        <!-- Sección Notificaciones -->
        <section id="notificaciones" class="section">
            <div class="container-centered">
                <div class="section-header-centered">
                    <button class="btn btn-secondary" onclick="marcarTodasLeidas()">
                        <i class="fas fa-check-double"></i> Marcar todas como leídas
                    </button>
                    <button class="btn btn-danger" onclick="eliminarTodasNotificaciones()">
                        <i class="fas fa-trash"></i> Eliminar todas
                    </button>
                </div>

                <article class="notifications-container" id="notificationsContainer">
                    <!-- Las notificaciones se cargarán dinámicamente -->
                </article>
            </div>
        </section>
    </main>

    <!-- Modales -->
    <!-- Modal Agregar/Editar Equipo -->
    <div id="equipoModal" class="modal-edit">
        <div class="modal-edit-content">
            <div class="modal-edit-header">
                <h3 id="modalTitle"><i class="fas fa-edit"></i> Editar Equipo</h3>
                <button class="modal-edit-close" onclick="closeModal('equipoModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-edit-body">
                <form id="equipoForm" class="modal-edit-form" onsubmit="guardarEquipo(event)">
                    <input type="hidden" id="equipoId">
                    
                    <div class="form-group">
                        <label for="nombre">Nombre del Equipo:</label>
                        <input type="text" id="nombre" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="categoria">Categoría:</label>
                        <select id="categoria" required>
                            <option value="">Seleccionar categoría</option>
                            <option value="Computadora">Computadora</option>
                            <option value="Proyector">Proyector</option>
                            <option value="Impresora">Impresora</option>
                            <option value="Tablet">Tablet</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="numeroSerie">Número de Serie:</label>
                        <input type="text" id="numeroSerie" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="estado">Estado:</label>
                        <select id="estado" required>
                            <option value="disponible">Disponible</option>
                            <option value="prestado">Prestado</option>
                            <option value="mantenimiento">En Mantenimiento</option>
                            <option value="baja">Dado de Baja</option>
                        </select>
                    </div>
                    
                    <div class="modal-edit-footer">
                        <button type="button" class="btn-modal-cancel" onclick="closeModal('equipoModal')">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn-modal-save">
                            <i class="fas fa-check"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para préstamos -->
    <div id="prestamoModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Nuevo Préstamo</h2>
                <span class="close" onclick="closeModal('prestamoModal')">&times;</span>
            </div>
            <form id="prestamoForm" onsubmit="guardarPrestamo(event)">
                <div class="form-group">
                    <label for="prestamoEquipo">Equipo:</label>
                    <select id="prestamoEquipo" required>
                        <option value="">Seleccionar equipo</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="prestamoUsuario">Usuario:</label>
                    <input type="text" id="prestamoUsuario" required>
                </div>
                <div class="form-group">
                    <label for="prestamoFechaInicio">Fecha de Inicio:</label>
                    <input type="date" id="prestamoFechaInicio" required>
                </div>
                <div class="form-group">
                    <label for="prestamoFechaFin">Fecha de Fin:</label>
                    <input type="date" id="prestamoFechaFin" required>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('prestamoModal')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Inicializar WOW.js
        new WOW().init();
    </script>
    <script src="dashboard.js"></script>
</body>
</html>
