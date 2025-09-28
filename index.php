<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Catálogo de Equipos</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <h1 class="logo animate__animated animate__fadeInDown">
                <i class="fas fa-laptop"></i> Sistema de Equipos
            </h1>
            <nav class="nav">
                <button class="nav-btn active" onclick="showSection('catalogo')">
                    <i class="fas fa-list"></i> Catálogo
                </button>
                <button class="nav-btn" onclick="showSection('notificaciones')">
                    <i class="fas fa-bell"></i> Notificaciones
                </button>
                <button class="nav-btn" onclick="showSection('historial')">
                    <i class="fas fa-history"></i> Historial
                </button>
            </nav>
        </div>
    </header>

    <main class="main">
        <!-- Sección Catálogo de Equipos -->
        <section id="catalogo" class="section active animate__animated animate__fadeIn">
            <div class="container">
                <div class="section-header">
                    <h2><i class="fas fa-desktop"></i> Catálogo de Equipos</h2>
                    <button class="btn btn-primary" onclick="openModal('equipoModal')">
                        <i class="fas fa-plus"></i> Agregar Equipo
                    </button>
                </div>

                <!-- Filtros -->
                <article class="filters-card">
                    <h3><i class="fas fa-filter"></i> Filtros</h3>
                    <div class="filters">
                        <div class="filter-group">
                            <label for="filtroNombre">Nombre:</label>
                            <input type="text" id="filtroNombre" placeholder="Buscar por nombre..." onkeyup="filtrarEquipos()">
                        </div>
                        <div class="filter-group">
                            <label for="filtroCategoria">Categoría:</label>
                            <select id="filtroCategoria" onchange="filtrarEquipos()">
                                <option value="">Todas</option>
                                <option value="Computadora">Computadora</option>
                                <option value="Proyector">Proyector</option>
                                <option value="Impresora">Impresora</option>
                                <option value="Tablet">Tablet</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="filtroEstado">Estado:</label>
                            <select id="filtroEstado" onchange="filtrarEquipos()">
                                <option value="">Todos</option>
                                <option value="disponible">Disponible</option>
                                <option value="prestado">Prestado</option>
                                <option value="mantenimiento">En Mantenimiento</option>
                                <option value="baja">Dado de Baja</option>
                            </select>
                        </div>
                    </div>
                </article>

                <!-- Lista de Equipos -->
                <article class="equipos-grid" id="equiposGrid">
                    <!-- Los equipos se cargarán dinámicamente -->
                </article>
            </div>
        </section>

        <!-- Sección Notificaciones -->
        <section id="notificaciones" class="section">
            <div class="container">
                <div class="section-header">
                    <h2><i class="fas fa-bell"></i> Centro de Notificaciones</h2>
                    <button class="btn btn-secondary" onclick="marcarTodasLeidas()">
                        <i class="fas fa-check-double"></i> Marcar todas como leídas
                    </button>
                </div>

                <article class="notifications-container" id="notificationsContainer">
                    <!-- Las notificaciones se cargarán dinámicamente -->
                </article>
            </div>
        </section>

        <!-- Sección Historial -->
        <section id="historial" class="section">
            <div class="container">
                <div class="section-header">
                    <h2><i class="fas fa-history"></i> Historial de Cambios</h2>
                </div>

                <article class="historial-container" id="historialContainer">
                    <!-- El historial se cargará dinámicamente -->
                </article>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Sistema de Catálogo de Equipos. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- Modal para Agregar/Editar Equipo -->
    <div id="equipoModal" class="modal">
        <div class="modal-content animate__animated animate__zoomIn">
            <div class="modal-header">
                <h3 id="modalTitle"><i class="fas fa-plus"></i> Agregar Equipo</h3>
                <button class="close-btn" onclick="closeModal('equipoModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="equipoForm" class="modal-body">
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" onclick="closeModal('equipoModal')">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Confirmación -->
    <div id="confirmModal" class="modal">
        <div class="modal-content animate__animated animate__zoomIn">
            <div class="modal-header">
                <h3><i class="fas fa-exclamation-triangle"></i> Confirmar Acción</h3>
            </div>
            <div class="modal-body">
                <p id="confirmMessage"></p>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('confirmModal')">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmBtn">
                        <i class="fas fa-check"></i> Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
