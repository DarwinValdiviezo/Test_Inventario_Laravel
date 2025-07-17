// Lógica para el modal de eliminación de usuario (show y lista)
window.togglePasswordVisibility = function(fieldId) {
  const field = document.getElementById(fieldId);
  if (!field) return;
  const button = field.nextElementSibling;
  const icon = button.querySelector('i');
  if (field.type === 'password') {
    field.type = 'text';
    icon.className = 'bx bx-hide';
    button.title = 'Ocultar contraseña';
  } else {
    field.type = 'password';
    icon.className = 'bx bx-show';
    button.title = 'Mostrar contraseña';
  }
};

function validateDeleteModal(userId) {
  const password = document.getElementById(`password${userId}`);
  const tipoObs = document.getElementById(`tipo_observacion${userId}`);
  const confirm = document.getElementById(`confirmacion${userId}`);
  const btn = document.getElementById(`btnEliminar${userId}`);
  if (!password || !tipoObs || !confirm || !btn) return;
  const valid = password.value.length >= 6 && tipoObs.value !== '' && confirm.checked;
  btn.disabled = !valid;
  if (valid) {
    btn.classList.remove('btn-secondary');
    btn.classList.add('btn-danger');
  } else {
    btn.classList.remove('btn-danger');
    btn.classList.add('btn-secondary');
  }
}

function setupDeleteModals() {
  document.querySelectorAll('[id^="formEliminarUsuario"]').forEach(form => {
    const userId = form.id.replace('formEliminarUsuario', '');
    const password = document.getElementById(`password${userId}`);
    const tipoObs = document.getElementById(`tipo_observacion${userId}`);
    const confirm = document.getElementById(`confirmacion${userId}`);
    [password, tipoObs, confirm].forEach(el => {
      if (el) {
        el.addEventListener('input', () => validateDeleteModal(userId));
        el.addEventListener('change', () => validateDeleteModal(userId));
      }
    });
    // Validación inicial
    validateDeleteModal(userId);
  });
}

// Sistema de gestión de usuarios
class UserManagementSystem {
  constructor() {
    this.init();
  }

  init() {
    this.initializeEventListeners();
    this.setupFilterAutoSubmit();
    this.setupRealTimeSearch();
    this.setupCriticalActionConfirmations();
    this.setupTooltips();
    this.setupModals();
    this.initializeAnimations();
    setupDeleteModals(); // Agregar esta línea
  }

  initializeEventListeners() {
    // Event listeners para filtros
    const filterInputs = document.querySelectorAll('select[name="rol"], select[name="cantidad"]');
    filterInputs.forEach(input => {
      input.addEventListener('change', function() {
        this.closest('form').submit();
      });
    });

    // Event listeners para búsqueda
    const searchInput = document.querySelector('input[name="busqueda"]');
    if (searchInput) {
      let searchTimeout;
      searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
          this.closest('form').submit();
        }, 500);
      });
    }
  }

  setupFilterAutoSubmit() {
    const filterInputs = document.querySelectorAll('select[name="rol"], select[name="cantidad"]');
    filterInputs.forEach(input => {
      input.addEventListener('change', function() {
        this.closest('form').submit();
      });
    });
  }

  setupRealTimeSearch() {
    const searchInput = document.querySelector('input[name="busqueda"]');
    if (searchInput) {
      let searchTimeout;
      searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
          this.closest('form').submit();
        }, 500);
      });
    }
  }



  setupTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });
  }

  setupModals() {
    // Configurar modales para acciones de usuario
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
      modal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const modalTitle = this.querySelector('.modal-title');
        const modalBody = this.querySelector('.modal-body');
        
        // Personalizar contenido del modal según la acción
        if (button.classList.contains('btn-warning')) {
          modalTitle.innerHTML = '<i class="bx bx-user-x text-warning me-2"></i> Desactivar Usuario';
        } else if (button.classList.contains('btn-success')) {
          modalTitle.innerHTML = '<i class="bx bx-user-check text-success me-2"></i> Activar Usuario';
        } else if (button.classList.contains('btn-danger')) {
          modalTitle.innerHTML = '<i class="bx bx-trash text-danger me-2"></i> Eliminar Usuario';
        }
      });
    });
  }

  initializeAnimations() {
    // Animaciones para las filas de la tabla
    const tableRows = document.querySelectorAll('tbody tr');
    tableRows.forEach((row, index) => {
      row.style.opacity = '0';
      row.style.transform = 'translateY(20px)';
      
      setTimeout(() => {
        row.style.transition = 'all 0.3s ease-out';
        row.style.opacity = '1';
        row.style.transform = 'translateY(0)';
      }, index * 50);
    });
  }

  updateCounters() {
    const totalUsers = document.querySelectorAll('tbody tr').length;
    const activeUsers = document.querySelectorAll('tbody tr .badge.bg-success').length;
    const inactiveUsers = document.querySelectorAll('tbody tr .badge.bg-secondary').length;
    const deletedUsers = document.querySelectorAll('tbody tr .badge.bg-dark').length;

    // Actualizar contadores si existen
    const totalCounter = document.getElementById('total-users');
    if (totalCounter) totalCounter.textContent = totalUsers;
    
    const activeCounter = document.getElementById('active-users');
    if (activeCounter) activeCounter.textContent = activeUsers;
    
    const inactiveCounter = document.getElementById('inactive-users');
    if (inactiveCounter) inactiveCounter.textContent = inactiveUsers;
    
    const deletedCounter = document.getElementById('deleted-users');
    if (deletedCounter) deletedCounter.textContent = deletedUsers;
  }

  filterTable(searchTerm) {
    const rows = document.querySelectorAll('tbody tr');
    let foundCount = 0;

    rows.forEach(row => {
      const text = row.textContent.toLowerCase();
      if (text.includes(searchTerm.toLowerCase())) {
        row.style.display = '';
        row.classList.add('highlight-row');
        foundCount++;
      } else {
        row.style.display = 'none';
      }
    });

    return foundCount;
  }

  sortTable(column, type = 'text') {
    const table = document.querySelector('table');
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));

    rows.sort((a, b) => {
      const aValue = a.querySelector(`td:nth-child(${column})`).textContent.trim();
      const bValue = b.querySelector(`td:nth-child(${column})`).textContent.trim();

      if (type === 'number') {
        return parseFloat(aValue) - parseFloat(bValue);
      } else {
        return aValue.localeCompare(bValue);
      }
    });

    // Reordenar las filas
    rows.forEach(row => tbody.appendChild(row));
  }

  exportData(format = 'csv') {
    const table = document.querySelector('table');
    const rows = Array.from(table.querySelectorAll('tbody tr'));
    
    let csvContent = '';
    
    // Obtener headers
    const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.trim());
    csvContent += headers.join(',') + '\n';
    
    // Obtener datos
    rows.forEach(row => {
      const cells = Array.from(row.querySelectorAll('td')).map(td => {
        let content = td.textContent.trim();
        // Escapar comillas
        content = content.replace(/"/g, '""');
        return `"${content}"`;
      });
      csvContent += cells.join(',') + '\n';
    });
    
    // Crear y descargar archivo
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `usuarios_${new Date().toISOString().split('T')[0]}.csv`;
    a.click();
    window.URL.revokeObjectURL(url);
  }

  showStatistics() {
    const stats = {
      total: document.querySelectorAll('tbody tr').length,
      activos: document.querySelectorAll('tbody tr .badge.bg-success').length,
      inactivos: document.querySelectorAll('tbody tr .badge.bg-secondary').length,
      eliminados: document.querySelectorAll('tbody tr .badge.bg-dark').length
    };

    // Mostrar estadísticas en una notificación
    const statsMessage = `
      Total: ${stats.total}
      Activos: ${stats.activos}
      Inactivos: ${stats.inactivos}
      Eliminados: ${stats.eliminados}
    `;
    
    // Eliminar todas las funciones y clases de notificaciones visuales JS
  }

  // Método para buscar usuarios en tiempo real
  searchUsers(query) {
    const rows = document.querySelectorAll('tbody tr');
    let foundCount = 0;

    rows.forEach(row => {
      const text = row.textContent.toLowerCase();
      if (text.includes(query.toLowerCase())) {
        row.style.display = '';
        row.classList.add('highlight-row');
        foundCount++;
      } else {
        row.style.display = 'none';
      }
    });

    // Mostrar resultado de búsqueda
    if (query) {
      // Eliminar todas las funciones y clases de notificaciones visuales JS
    }
  }

  // Método para cambiar estado de usuario
  toggleUserStatus(userId, action) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/users/${userId}/${action}`;
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    form.appendChild(csrfToken);
    document.body.appendChild(form);
    form.submit();
  }

  // Método para mostrar confirmación personalizada
  showConfirmation(message, callback) {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Confirmación</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <p>${message}</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary confirm-btn">Confirmar</button>
          </div>
        </div>
      </div>
    `;

    document.body.appendChild(modal);
    const modalInstance = new bootstrap.Modal(modal);
    modalInstance.show();

    modal.querySelector('.confirm-btn').addEventListener('click', () => {
      callback();
      modalInstance.hide();
      document.body.removeChild(modal);
    });

    modal.addEventListener('hidden.bs.modal', () => {
      document.body.removeChild(modal);
    });
  }
}

// Eliminar todas las funciones y clases de notificaciones visuales JS
// Inicializar sistema cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
  window.userManagement = new UserManagementSystem();
  // Eliminar todas las funciones y clases de notificaciones visuales JS
});

// Exportar para uso global
window.UserManagementSystem = UserManagementSystem;
// Eliminar todas las funciones y clases de notificaciones visuales JS 