// Sistema de gestión de roles - Funcionalidades profesionales
class RolesManager {
  constructor() {
    this.initializeNotifications();
    this.initializeModals();
    this.initializeValidations();
    this.initializeEventListeners();
  }

  // Sistema de notificaciones elegante
  initializeNotifications() {
    this.notificationContainer = document.getElementById('notification-container');
    if (!this.notificationContainer) {
      this.notificationContainer = document.createElement('div');
      this.notificationContainer.id = 'notification-container';
      this.notificationContainer.className = 'position-fixed top-0 end-0 p-3';
      this.notificationContainer.style.cssText = 'z-index: 9999;';
      document.body.appendChild(this.notificationContainer);
    }
  }

  showNotification(message, type = 'info', duration = 5000) {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show shadow-lg`;
    notification.style.cssText = `
      min-width: 300px;
      max-width: 400px;
      margin-bottom: 10px;
      border: none;
      border-radius: 10px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      animation: slideInRight 0.3s ease-out;
    `;

    const iconMap = {
      success: 'bx-check-circle',
      error: 'bx-error-circle',
      warning: 'bx-warning',
      info: 'bx-info-circle'
    };

    notification.innerHTML = `
      <div class="d-flex align-items-center">
        <i class="bx ${iconMap[type]} fs-4 me-2"></i>
        <div class="flex-grow-1">
          <div class="fw-semibold">${this.getNotificationTitle(type)}</div>
          <div class="small">${message}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    `;

    this.notificationContainer.appendChild(notification);

    // Auto-remove después del tiempo especificado
    setTimeout(() => {
      this.hideNotification(notification);
    }, duration);

    // Event listener para cerrar manualmente
    notification.querySelector('.btn-close').addEventListener('click', () => {
      this.hideNotification(notification);
    });
  }

  hideNotification(notification) {
    notification.style.animation = 'slideOutRight 0.3s ease-in';
    setTimeout(() => {
      if (notification.parentNode) {
        notification.parentNode.removeChild(notification);
      }
    }, 300);
  }

  getNotificationTitle(type) {
    const titles = {
      success: '¡Éxito!',
      error: 'Error',
      warning: 'Advertencia',
      info: 'Información'
    };
    return titles[type] || 'Notificación';
  }

  // Inicializar modales
  initializeModals() {
    // Modales de información de roles
    const infoButtons = document.querySelectorAll('[data-bs-toggle="modal"][data-bs-target*="modalInfoRol"]');
    infoButtons.forEach(button => {
      button.addEventListener('click', (e) => {
        e.preventDefault();
        const target = button.getAttribute('data-bs-target');
        const modal = document.querySelector(target);
        if (modal) {
          const bsModal = new bootstrap.Modal(modal);
          bsModal.show();
        }
      });
    });

    // Modales de eliminación de roles
    const deleteButtons = document.querySelectorAll('[data-bs-toggle="modal"][data-bs-target*="modalEliminarRol"]');
    deleteButtons.forEach(button => {
      button.addEventListener('click', (e) => {
        e.preventDefault();
        const target = button.getAttribute('data-bs-target');
        const modal = document.querySelector(target);
        if (modal) {
          const bsModal = new bootstrap.Modal(modal);
          bsModal.show();
        }
      });
    });
  }

  // Inicializar validaciones
  initializeValidations() {
    // Validación de formularios de eliminación
    const deleteForms = document.querySelectorAll('form[action*="roles"][method="POST"]');
    deleteForms.forEach(form => {
      const passwordInput = form.querySelector('input[name="password"]');
      const observacionSelect = form.querySelector('select[name="observacion"]');
      const submitButton = form.querySelector('button[type="submit"]');
      const confirmCheckbox = form.querySelector('input[name="confirm_delete"]');

      if (passwordInput && observacionSelect && submitButton) {
        const validateForm = () => {
          const passwordValid = passwordInput.value.trim().length > 0;
          const observacionValid = observacionSelect.value.trim().length > 0;
          const confirmValid = confirmCheckbox ? confirmCheckbox.checked : true;

          if (passwordValid && observacionValid && confirmValid) {
            submitButton.disabled = false;
            submitButton.classList.remove('btn-secondary');
            submitButton.classList.add('btn-danger');
          } else {
            submitButton.disabled = true;
            submitButton.classList.remove('btn-danger');
            submitButton.classList.add('btn-secondary');
          }
        };

        passwordInput.addEventListener('input', validateForm);
        observacionSelect.addEventListener('change', validateForm);
        if (confirmCheckbox) {
          confirmCheckbox.addEventListener('change', validateForm);
        }

        // Validación inicial
        validateForm();
      }
    });

    // Toggle de contraseña
    const togglePasswordButtons = document.querySelectorAll('.toggle-password');
    togglePasswordButtons.forEach(button => {
      button.addEventListener('click', (e) => {
        e.preventDefault();
        const input = button.parentNode.querySelector('input[type="password"], input[type="text"]');
        const icon = button.querySelector('i');
        
        if (input.type === 'password') {
          input.type = 'text';
          icon.classList.remove('bx-hide');
          icon.classList.add('bx-show');
        } else {
          input.type = 'password';
          icon.classList.remove('bx-show');
          icon.classList.add('bx-hide');
        }
      });
    });
  }

  // Inicializar event listeners
  initializeEventListeners() {
    // Confirmaciones para acciones críticas
    const criticalActions = document.querySelectorAll('form[action*="destroy"]');
    criticalActions.forEach(form => {
      form.addEventListener('submit', (e) => {
        const submitButton = form.querySelector('button[type="submit"]');
        if (submitButton && !submitButton.disabled) {
          if (!confirm('¿Está seguro que desea eliminar este rol? Esta acción no se puede deshacer.')) {
            e.preventDefault();
          }
        }
      });
    });

    // Tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
      return new bootstrap.Popover(popoverTriggerEl);
    });
  }

  // Métodos de utilidad
  showSuccess(message) {
    this.showNotification(message, 'success');
  }

  showError(message) {
    this.showNotification(message, 'error');
  }

  showWarning(message) {
    this.showNotification(message, 'warning');
  }

  showInfo(message) {
    this.showNotification(message, 'info');
  }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
  window.rolesManager = new RolesManager();

  // Mostrar notificaciones de sesión si existen


  Object.entries(sessionMessages).forEach(([type, message]) => {
    if (message && message.trim()) {
      window.rolesManager.showNotification(message, type);
    }
  });

  // Mostrar errores de validación
  const validationErrors = document.querySelectorAll('.invalid-feedback');
  validationErrors.forEach(error => {
    if (error.textContent.trim()) {
      window.rolesManager.showError(error.textContent.trim());
    }
  });
});

// Exportar para uso global
window.RolesManager = RolesManager; 