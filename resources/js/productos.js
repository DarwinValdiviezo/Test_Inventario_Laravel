// Sistema de gestión de productos - Funcionalidades profesionales
class ProductManager {
  constructor() {
    this.initializeNotifications();
    this.initializeModals();
    this.initializeValidations();
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
    notification.style.cssText = `min-width: 300px; max-width: 400px; margin-bottom: 10px; border: none; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); animation: slideInRight 0.3s ease-out;`;
    const iconMap = { success: 'bi-check-circle', error: 'bi-x-circle', warning: 'bi-exclamation-triangle', info: 'bi-info-circle' };
    notification.innerHTML = `
      <div class="d-flex align-items-center">
        <i class="bi ${iconMap[type]} fs-4 me-2"></i>
        <div class="flex-grow-1">
          <div class="fw-semibold">${this.getNotificationTitle(type)}</div>
          <div class="small">${message}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    `;
    this.notificationContainer.appendChild(notification);
    setTimeout(() => { this.hideNotification(notification); }, duration);
    notification.querySelector('.btn-close').addEventListener('click', () => { this.hideNotification(notification); });
  }

  hideNotification(notification) {
    notification.style.animation = 'slideOutRight 0.3s ease-in';
    setTimeout(() => { if (notification.parentNode) { notification.parentNode.removeChild(notification); } }, 300);
  }

  getNotificationTitle(type) {
    const titles = { success: '¡Éxito!', error: 'Error', warning: 'Advertencia', info: 'Información' };
    return titles[type] || 'Notificación';
  }

  // Inicializar modales (Bootstrap)
  initializeModals() {
    // Tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });
  }

  // Inicializar validaciones en los modales
  initializeValidations() {
    // Validación de formularios de eliminación y restauración
    const modalForms = document.querySelectorAll('.modal form');
    modalForms.forEach(form => {
      const passwordInput = form.querySelector('input[name="password"]');
      const observacionSelect = form.querySelector('select[name="observacion"]');
      const submitButton = form.querySelector('button[type="submit"]');
      if (passwordInput && observacionSelect && submitButton) {
        const validateForm = () => {
          const passwordValid = passwordInput.value.trim().length > 0;
          const observacionValid = observacionSelect.value.trim().length > 0;
          if (passwordValid && observacionValid) {
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
        validateForm();
      }
    });
  }

  showSuccess(message) { this.showNotification(message, 'success'); }
  showError(message) { this.showNotification(message, 'error'); }
  showWarning(message) { this.showNotification(message, 'warning'); }
  showInfo(message) { this.showNotification(message, 'info'); }
}

document.addEventListener('DOMContentLoaded', function() {
  window.productManager = new ProductManager();
}); 