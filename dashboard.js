// Funcionalidad del menú lateral
document.addEventListener("DOMContentLoaded", () => {
  const sidebar = document.getElementById("sidebar")
  const sidebarToggle = document.getElementById("sidebarToggle")
  const mainContent = document.getElementById("mainContent")

  // Toggle del menú lateral
  sidebarToggle.addEventListener("click", () => {
    sidebar.classList.toggle("collapsed")
    mainContent.classList.toggle("expanded")
  })

  // Auto-colapsar en pantallas pequeñas
  function checkScreenSize() {
    if (window.innerWidth <= 768) {
      sidebar.classList.add("collapsed")
      mainContent.classList.add("expanded")
    } else {
      sidebar.classList.remove("collapsed")
      mainContent.classList.remove("expanded")
    }
  }

  window.addEventListener("resize", checkScreenSize)
  checkScreenSize()

  // Cargar datos iniciales
  cargarEquipos()
  cargarNotificaciones()
  cargarHistorial()
})

// Función para mostrar secciones
function showSection(sectionName) {
  // Ocultar todas las secciones
  const sections = document.querySelectorAll(".section")
  sections.forEach((section) => {
    section.classList.remove("active")
  })

  // Mostrar la sección seleccionada
  const targetSection = document.getElementById(sectionName)
  if (targetSection) {
    targetSection.classList.add("active")
    targetSection.classList.add("animate__animated", "animate__fadeIn")
  }

  // Actualizar menú activo
  const menuItems = document.querySelectorAll(".menu-item")
  menuItems.forEach((item) => {
    item.classList.remove("active")
  })

  const activeMenuItem = document.querySelector(`[onclick="showSection('${sectionName}')"]`)
  if (activeMenuItem) {
    activeMenuItem.classList.add("active")
  }

  // Actualizar título de la página
  const pageTitle = document.getElementById("pageTitle")
  const titles = {
    catalogo: "Catálogo de Equipos",
    notificaciones: "Notificaciones",
    historial: "Historial de Cambios",
    prestamos: "Zona de Préstamo", // Agregando título faltante
    perfil: "Perfil de Usuario", // Agregando título faltante
  }
  pageTitle.textContent = titles[sectionName] || "Dashboard"
}

// Función para cargar datos de equipos desde la base de datos
function cargarEquipos() {
  console.log("[v0] Iniciando carga de equipos...")
  const equiposGrid = document.getElementById("equiposGrid")

  // Mostrar loading
  equiposGrid.innerHTML = '<div class="loading">Cargando equipos...</div>'

  // Obtener equipos desde la base de datos
  fetch("obtener_equipos.php")
    .then((response) => {
      console.log("[v0] Respuesta recibida:", response.status)
      return response.json()
    })
    .then((data) => {
      console.log("[v0] Datos recibidos:", data)
      if (data.success) {
        const equipos = data.equipos
        console.log("[v0] Número de equipos:", equipos.length)

        equiposGrid.innerHTML = equipos
          .map(
            (equipo) => `
              <div class="equipo-card-simple animate__animated animate__fadeInUp">
                  <div class="equipo-header-simple">
                      <h3 class="equipo-title-simple">${equipo.nombre}</h3>
                      <span class="estado-badge-simple estado-${equipo.estado}-simple">
                          ${getEstadoTexto(equipo.estado)}
                      </span>
                  </div>
                  <div class="equipo-info-simple">
                      <div class="equipo-detail-simple">
                          <i class="fas fa-tag"></i>
                          <span>${equipo.categoria}</span>
                      </div>
                      <div class="equipo-detail-simple">
                          <i class="fas fa-barcode"></i>
                          <span>Serie: ${equipo.numero_serie}</span>
                      </div>
                      <div class="equipo-detail-simple">
                          <i class="fas fa-calendar"></i>
                          <span>Creado: ${equipo.fecha_creacion}</span>
                      </div>
                      <div class="equipo-detail-simple">
                          <i class="fas fa-edit"></i>
                          <span>Modificado: ${equipo.fecha_modificacion}</span>
                      </div>
                      <div class="equipo-detail-simple">
                          <i class="fas fa-info-circle"></i>
                          <span>Estado: 
                              <span class="estado-badge-inline estado-${equipo.estado}-inline">
                                  ${getEstadoTexto(equipo.estado)}
                              </span>
                          </span>
                      </div>
                  </div>
                  <div class="equipo-actions-simple">
                      <button class="btn-edit-simple" onclick="editarEquipo(${equipo.id})" title="Editar">
                          <i class="fas fa-edit"></i>
                      </button>
                      <button class="btn-delete-simple" onclick="eliminarEquipo(${equipo.id}, '${equipo.estado}')" title="Eliminar">
                          <i class="fas fa-trash"></i>
                      </button>
                  </div>
              </div>
          `,
          )
          .join("")
      } else {
        console.log("[v0] Error en respuesta:", data.message)
        equiposGrid.innerHTML = '<div class="error">Error al cargar equipos: ' + data.message + "</div>"
      }
    })
    .catch((error) => {
      console.error("[v0] Error en fetch:", error)
      equiposGrid.innerHTML = '<div class="error">Error al cargar equipos: ' + error.message + "</div>"
    })
}

// Función para cargar datos de notificaciones
function cargarNotificaciones() {
  const notificationsContainer = document.getElementById("notificationsContainer")

  // Datos de ejemplo
  const notificaciones = [
    {
      id: 1,
      titulo: "Equipo devuelto",
      mensaje: 'El equipo "Laptop Dell Inspiron" ha sido devuelto',
      tipo: "success",
      fecha: "2024-01-15 10:30",
      leida: false,
    },
    {
      id: 2,
      titulo: "Mantenimiento programado",
      mensaje: "El proyector Epson requiere mantenimiento",
      tipo: "warning",
      fecha: "2024-01-15 09:15",
      leida: false,
    },
  ]

  notificationsContainer.innerHTML = notificaciones
    .map(
      (notif) => `
        <div class="notification-card ${notif.leida ? "read" : "unread"} animate__animated animate__fadeInLeft">
            <div class="notification-icon ${notif.tipo}">
                <i class="fas ${getNotificationIcon(notif.tipo)}"></i>
            </div>
            <div class="notification-content">
                <h4>${notif.titulo}</h4>
                <p>${notif.mensaje}</p>
                <small>${notif.fecha}</small>
            </div>
            <div class="notification-actions">
                <button class="notification-btn" onclick="marcarLeida(${notif.id})" title="Marcar como leída">
                    <i class="fas fa-check"></i>
                </button>
                <button class="notification-btn delete" onclick="eliminarNotificacion(${notif.id})" title="Eliminar">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `,
    )
    .join("")

  // Actualizar contador de notificaciones
  const unreadCount = notificaciones.filter((n) => !n.leida).length
  const badge = document.getElementById("notificationBadge")
  const dot = document.getElementById("notificationDot")

  if (unreadCount > 0) {
    badge.textContent = unreadCount
    badge.style.display = "block"
    dot.style.display = "block"
  } else {
    badge.style.display = "none"
    dot.style.display = "none"
  }
}

// Función para cargar datos de historial
function cargarHistorial() {
  const historialContainer = document.getElementById("historialContainer")

  // Datos de ejemplo
  const historial = [
    {
      id: 1,
      equipo: "Laptop Dell Inspiron",
      accion: "Creado",
      usuario: "Admin",
      fecha: "2024-01-15 08:00",
    },
    {
      id: 2,
      equipo: "Proyector Epson",
      accion: "Estado cambiado a Prestado",
      usuario: "Admin",
      fecha: "2024-01-15 09:30",
    },
  ]

  historialContainer.innerHTML = historial
    .map(
      (item) => `
        <div class="historial-item animate__animated animate__fadeInRight">
            <div class="historial-icon">
                <i class="fas fa-history"></i>
            </div>
            <div class="historial-content">
                <h4>${item.equipo}</h4>
                <p>${item.accion}</p>
                <small>Por ${item.usuario} - ${item.fecha}</small>
            </div>
        </div>
    `,
    )
    .join("")
}

function getNotificationIcon(tipo) {
  const icons = {
    success: "fa-check-circle",
    warning: "fa-exclamation-triangle",
    error: "fa-times-circle",
    info: "fa-info-circle",
  }
  return icons[tipo] || "fa-info-circle"
}

function marcarLeida(id) {
  // Aquí implementarías la lógica para marcar como leída
  console.log("Marcar notificación como leída:", id)
  cargarNotificaciones()
}

function marcarTodasLeidas() {
  // Aquí implementarías la lógica para marcar todas como leídas
  console.log("Marcar todas las notificaciones como leídas")
  cargarNotificaciones()
}

// Funciones para el manejo de notificaciones
function eliminarNotificacion(id) {
  if (confirm("¿Estás seguro de que deseas eliminar esta notificación?")) {
    // Aquí implementarías la lógica para eliminar la notificación específica
    console.log("Eliminar notificación:", id)
    cargarNotificaciones()
  }
}

function eliminarTodasNotificaciones() {
  if (confirm("¿Estás seguro de que deseas eliminar todas las notificaciones?")) {
    // Aquí implementarías la lógica para eliminar todas las notificaciones
    console.log("Eliminar todas las notificaciones")
    cargarNotificaciones()
  }
}

// Función para filtrar equipos por nombre, categoría y estado
function filtrarEquipos() {
  const filtroNombre = document.getElementById("filtroNombre").value.toLowerCase()
  const filtroCategoria = document.getElementById("filtroCategoria").value
  const filtroEstado = document.getElementById("filtroEstado").value

  console.log("[v0] Aplicando filtros:", { filtroNombre, filtroCategoria, filtroEstado })

  // Obtener todas las tarjetas de equipos
  const equipoCards = document.querySelectorAll(".equipo-card-simple")

  equipoCards.forEach((card) => {
    const nombre = card.querySelector(".equipo-title-simple").textContent.toLowerCase()
    const categoria = card.querySelector(".equipo-detail-simple span").textContent
    const estadoBadge = card.querySelector(".estado-badge-simple")
    const estado = estadoBadge ? estadoBadge.className.match(/estado-(\w+)-simple/)?.[1] : ""

    // Aplicar filtros
    const coincideNombre = !filtroNombre || nombre.includes(filtroNombre)
    const coincideCategoria = !filtroCategoria || categoria === filtroCategoria
    const coincideEstado = !filtroEstado || estado === filtroEstado

    // Mostrar u ocultar la tarjeta
    if (coincideNombre && coincideCategoria && coincideEstado) {
      card.style.display = "block"
      card.classList.add("animate__animated", "animate__fadeIn")
    } else {
      card.style.display = "none"
    }
  })
}

// Funciones para el manejo de equipos
function openModal(modalId) {
  const modal = document.getElementById(modalId)

  if (modalId === "equipoModal") {
    const equipoId = document.getElementById("equipoId").value
    if (!equipoId) {
      // Modo agregar - limpiar formulario y cambiar título
      document.getElementById("equipoForm").reset()
      document.getElementById("equipoId").value = ""
      document.getElementById("modalTitle").innerHTML = '<i class="fas fa-plus"></i> Agregar Equipo'
    }
  }

  if (modal.classList.contains("modal-edit")) {
    modal.classList.add("active")
  } else {
    modal.style.display = "flex"
  }
  modal.classList.add("animate__animated", "animate__fadeIn")

  // Prevenir scroll del body cuando el modal está abierto
  document.body.style.overflow = "hidden"
}

function closeModal(modalId) {
  const modal = document.getElementById(modalId)
  if (modal.classList.contains("modal-edit")) {
    modal.classList.remove("active")
  } else {
    modal.style.display = "none"
  }

  // Restaurar scroll del body
  document.body.style.overflow = "auto"
}

// Función para guardar equipo en la base de datos
function guardarEquipo(event) {
  event.preventDefault()
  console.log("[v0] Iniciando guardado de equipo...")

  const equipoId = document.getElementById("equipoId").value
  const nombre = document.getElementById("nombre").value
  const categoria = document.getElementById("categoria").value
  const numeroSerie = document.getElementById("numeroSerie").value
  const estado = document.getElementById("estado").value

  console.log("[v0] Datos del formulario:", { equipoId, nombre, categoria, numeroSerie, estado })

  // Validar campos requeridos
  if (!nombre || !categoria || !numeroSerie || !estado) {
    alert("Todos los campos son requeridos")
    return
  }

  // Crear URLSearchParams para enviar al servidor
  const formData = new URLSearchParams()
  if (equipoId) {
    formData.append("equipoId", equipoId)
  }
  formData.append("nombre", nombre)
  formData.append("categoria", categoria)
  formData.append("numeroSerie", numeroSerie)
  formData.append("estado", estado)

  // Determinar si es agregar o actualizar
  const isUpdate = equipoId && equipoId !== ""
  const url = isUpdate ? "actualizar_equipo.php" : "agregar_equipo.php"
  const successMessage = isUpdate ? "Equipo actualizado correctamente" : "Equipo agregado correctamente"

  console.log("[v0] Enviando a:", url, "Datos:", formData.toString())

  // Enviar datos al servidor
  fetch(url, {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: formData,
  })
    .then((response) => {
      console.log("[v0] Respuesta de guardado:", response.status)
      return response.json()
    })
    .then((data) => {
      console.log("[v0] Resultado de guardado:", data)
      if (data.success) {
        alert(successMessage)
        closeModal("equipoModal")
        cargarEquipos() // Recargar la lista de equipos

        // Limpiar formulario
        document.getElementById("equipoForm").reset()
        document.getElementById("equipoId").value = ""
      } else {
        alert("Error al guardar equipo: " + data.message)
      }
    })
    .catch((error) => {
      console.error("[v0] Error en guardado:", error)
      alert("Error al guardar equipo: " + error.message)
    })
}

function editarEquipo(id) {
  // Obtener datos del equipo desde la base de datos
  fetch("obtener_equipos.php")
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        const equipo = data.equipos.find((e) => e.id == id)
        if (equipo) {
          document.getElementById("equipoId").value = id
          document.getElementById("nombre").value = equipo.nombre
          document.getElementById("categoria").value = equipo.categoria
          document.getElementById("numeroSerie").value = equipo.numero_serie
          document.getElementById("estado").value = equipo.estado

          document.getElementById("modalTitle").innerHTML = '<i class="fas fa-edit"></i> Editar Equipo'
          openModal("equipoModal")
        }
      } else {
        alert("Error al cargar datos del equipo")
      }
    })
    .catch((error) => {
      console.error("Error:", error)
      alert("Error al cargar datos del equipo")
    })
}

function eliminarEquipo(id, estado) {
  if (estado !== "baja") {
    alert("No se puede eliminar este equipo. Solo se pueden eliminar equipos que estén dados de baja.")
    return
  }

  if (confirm("¿Estás seguro de que deseas eliminar este equipo?")) {
    console.log("[v0] Eliminando equipo:", id)

    fetch("eliminar_equipo.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `equipoId=${id}`,
    })
      .then((response) => response.json())
      .then((data) => {
        console.log("[v0] Resultado eliminación:", data)
        if (data.success) {
          alert("Equipo eliminado correctamente")
          cargarEquipos() // Recargar la lista
        } else {
          alert("Error al eliminar equipo: " + data.message)
        }
      })
      .catch((error) => {
        console.error("[v0] Error en eliminación:", error)
        alert("Error al eliminar equipo: " + error.message)
      })
  }
}

// Función para obtener texto del estado incluyendo "Dado de Baja"
function getEstadoTexto(estado) {
  const estados = {
    disponible: "DISPONIBLE",
    prestado: "PRESTADO",
    mantenimiento: "EN MANTENIMIENTO",
    baja: "DADO DE BAJA",
  }
  return estados[estado] || estado.toUpperCase()
}

// Cerrar modales al hacer clic fuera de ellos
window.onclick = (event) => {
  const modals = document.querySelectorAll(".modal, .modal-edit")
  modals.forEach((modal) => {
    if (event.target === modal) {
      if (modal.classList.contains("modal-edit")) {
        modal.classList.remove("active")
      } else {
        modal.style.display = "none"
      }
      // Restaurar scroll del body
      document.body.style.overflow = "auto"
    }
  })
}
