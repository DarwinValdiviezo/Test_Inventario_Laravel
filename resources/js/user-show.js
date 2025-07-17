/**
 * UserShowManager - Gestor de la vista de detalles de usuarios
 * Maneja todas las funcionalidades específicas de la vista show de usuarios
 */
class UserShowManager {
  constructor() {
    this.notificationContainer = document.getElementById('notification-container');
    this.notifications = [];
    this.init();
  }

  /**
   * Inicializar el gestor
   */
  init() {
    this.setupEventListeners();
    this.setupTooltips();
    this.setupAnimations();
    this.setupModals();
    console.log('UserShowManager inicializado');
  }

  /**
   * Configurar event listeners
   */
  setupEventListeners() {
    // Event listeners para formularios
    document.querySelectorAll('form').forEach(form => {
      form.addEventListener('submit', (e) => this.handleFormSubmit(e));
    });

    // Event listeners para botones de acción
    document.querySelectorAll('.btn').forEach(btn => {
      btn.addEventListener('click', (e) => this.handleButtonClick(e));
    });

    // Event listeners para modales
    document.querySelectorAll('[data-bs-toggle="modal"]').forEach(trigger => {
      trigger.addEventListener('click', (e) => this.handleModalTrigger(e));
    });

    // Event listeners para cerrar modales
    document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(closeBtn => {
      closeBtn.addEventListener('click', (e) => this.handleModalClose(e));
    });
  }

  /**
   * Configurar tooltips
   */
  setupTooltips() {
    // Inicializar tooltips de Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl, {
        trigger: 'hover',
        placement: 'top',
        animation: true
      });
    });

    // Tooltips personalizados para elementos específicos
    this.setupCustomTooltips();
  }

  /**
   * Configurar tooltips personalizados
   */
  setupCustomTooltips() {
    // Tooltip para el avatar del usuario
    const userAvatar = document.querySelector('.avatar-initial');
    if (userAvatar) {
      userAvatar.addEventListener('mouseenter', (e) => {
        this.showCustomTooltip(e.target, 'Avatar del usuario', 'top');
      });
    }

    // Tooltips para badges de estado
    document.querySelectorAll('.badge').forEach(badge => {
      badge.addEventListener('mouseenter', (e) => {
        const status = e.target.textContent.trim();
        let tooltipText = '';
        
        switch(status) {
          case 'Activo':
            tooltipText = 'El usuario está activo y puede acceder al sistema';
            break;
          case 'Inactivo':
            tooltipText = 'El usuario está inactivo y no puede acceder al sistema';
            break;
          case 'Eliminado':
            tooltipText = 'El usuario fue eliminado del sistema';
            break;
          default:
            if (status.includes('Pendiente')) {
              tooltipText = 'El usuario está pendiente de eliminación';
            }
        }
        
        if (tooltipText) {
          this.showCustomTooltip(e.target, tooltipText, 'top');
        }
      });
    });
  }

  /**
   * Mostrar tooltip personalizado
   */
  showCustomTooltip(element, text, placement = 'top') {
    // Remover tooltip existente
    const existingTooltip = document.querySelector('.custom-tooltip');
    if (existingTooltip) {
      existingTooltip.remove();
    }

    // Crear tooltip
    const tooltip = document.createElement('div');
    tooltip.className = 'custom-tooltip';
    tooltip.textContent = text;
    tooltip.style.cssText = `
      position: absolute;
      background: rgba(0, 0, 0, 0.8);
      color: white;
      padding: 5px 10px;
      border-radius: 4px;
      font-size: 12px;
      z-index: 10000;
      pointer-events: none;
      white-space: nowrap;
      max-width: 200px;
      word-wrap: break-word;
    `;

    // Posicionar tooltip
    const rect = element.getBoundingClientRect();
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;

    switch(placement) {
      case 'top':
        tooltip.style.top = (rect.top + scrollTop - 30) + 'px';
        tooltip.style.left = (rect.left + scrollLeft + rect.width / 2 - tooltip.offsetWidth / 2) + 'px';
        break;
      case 'bottom':
        tooltip.style.top = (rect.bottom + scrollTop + 5) + 'px';
        tooltip.style.left = (rect.left + scrollLeft + rect.width / 2 - tooltip.offsetWidth / 2) + 'px';
        break;
      case 'left':
        tooltip.style.top = (rect.top + scrollTop + rect.height / 2 - tooltip.offsetHeight / 2) + 'px';
        tooltip.style.left = (rect.left + scrollLeft - tooltip.offsetWidth - 5) + 'px';
        break;
      case 'right':
        tooltip.style.top = (rect.top + scrollTop + rect.height / 2 - tooltip.offsetHeight / 2) + 'px';
        tooltip.style.left = (rect.right + scrollLeft + 5) + 'px';
        break;
    }

    document.body.appendChild(tooltip);

    // Remover tooltip al salir del elemento
    element.addEventListener('mouseleave', () => {
      if (tooltip.parentNode) {
        tooltip.remove();
      }
    }, { once: true });
  }

  /**
   * Configurar animaciones
   */
  setupAnimations() {
    // Animación de entrada para las tarjetas
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
      card.style.opacity = '0';
      card.style.transform = 'translateY(20px)';
      
      setTimeout(() => {
        card.style.transition = 'all 0.5s ease-out';
        card.style.opacity = '1';
        card.style.transform = 'translateY(0)';
      }, index * 100);
    });

    // Animación para los badges de estado
    const badges = document.querySelectorAll('.badge');
    badges.forEach(badge => {
      badge.addEventListener('mouseenter', () => {
        badge.style.transform = 'scale(1.1)';
        badge.style.transition = 'transform 0.2s ease';
      });

      badge.addEventListener('mouseleave', () => {
        badge.style.transform = 'scale(1)';
      });
    });

    // Animación para los botones de acción
    const actionButtons = document.querySelectorAll('.btn');
    actionButtons.forEach(btn => {
      btn.addEventListener('mouseenter', () => {
        btn.style.transform = 'translateY(-2px)';
        btn.style.boxShadow = '0 4px 8px rgba(0,0,0,0.2)';
        btn.style.transition = 'all 0.2s ease';
      });

      btn.addEventListener('mouseleave', () => {
        btn.style.transform = 'translateY(0)';
        btn.style.boxShadow = '';
      });
    });
  }

  /**
   * Configurar modales
   */
  setupModals() {
    // Event listeners para modales
    document.querySelectorAll('.modal').forEach(modal => {
      // Mostrar modal con animación
      modal.addEventListener('show.bs.modal', (e) => {
        this.handleModalShow(e);
      });

      // Ocultar modal con animación
      modal.addEventListener('hide.bs.modal', (e) => {
        this.handleModalHide(e);
      });

      // Validar formularios en modales
      const form = modal.querySelector('form');
      if (form) {
        form.addEventListener('submit', (e) => this.handleModalFormSubmit(e));
      }
    });
  }

  /**
   * Manejar apertura de modal
   */
  handleModalShow(event) {
    const modal = event.target;
    const modalContent = modal.querySelector('.modal-content');
    
    // Animación de entrada
    modalContent.style.transform = 'scale(0.7)';
    modalContent.style.opacity = '0';
    
    setTimeout(() => {
      modalContent.style.transition = 'all 0.3s ease-out';
      modalContent.style.transform = 'scale(1)';
      modalContent.style.opacity = '1';
    }, 50);

    // Configurar validación en tiempo real para formularios
    this.setupModalFormValidation(modal);
  }

  /**
   * Manejar cierre de modal
   */
  handleModalHide(event) {
    const modal = event.target;
    const modalContent = modal.querySelector('.modal-content');
    
    // Animación de salida
    modalContent.style.transition = 'all 0.2s ease-in';
    modalContent.style.transform = 'scale(0.7)';
    modalContent.style.opacity = '0';
  }

  /**
   * Configurar validación de formularios en modales
   */
  setupModalFormValidation(modal) {
    const form = modal.querySelector('form');
    if (!form) return;

    const inputs = form.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
      input.addEventListener('blur', () => {
        this.validateField(input);
      });

      input.addEventListener('input', () => {
        this.clearFieldError(input);
      });
    });

    // Configurar validación específica para formularios de eliminación
    if (form.id && form.id.includes('formEliminarUsuario')) {
      this.setupEliminarUsuarioValidation(form);
    }
  }

  /**
   * Configurar validación específica para formularios de eliminación
   */
  setupEliminarUsuarioValidation(form) {
    const userId = form.id.replace('formEliminarUsuario', '');
    const passwordField = document.getElementById(`password${userId}`);
    const tipoObservacionField = document.getElementById(`tipo_observacion${userId}`);
    const observacionField = document.getElementById(`observacion${userId}`);
    const confirmacionField = document.getElementById(`confirmacion${userId}`);
    const submitBtn = document.getElementById(`btnEliminar${userId}`);
    const contador = document.getElementById(`contador${userId}`);

    // Validación en tiempo real para el campo de observación
    if (observacionField) {
      observacionField.addEventListener('input', (e) => {
        const text = e.target.value;
        const length = text.length;
        
        // Actualizar contador
        if (contador) {
          contador.textContent = length;
          
          // Cambiar color según la longitud
          if (length < 20) {
            contador.style.color = '#dc3545';
          } else if (length > 400) {
            contador.style.color = '#fd7e14';
          } else {
            contador.style.color = '#198754';
          }
        }

        // Validar longitud mínima
        if (length < 20) {
          this.showFieldError(observacionField, 'La observación debe tener al menos 20 caracteres');
        } else {
          this.clearFieldError(observacionField);
        }

        this.validateEliminarForm(userId);
      });
    }

    // Validación para el tipo de observación
    if (tipoObservacionField) {
      tipoObservacionField.addEventListener('change', () => {
        if (!tipoObservacionField.value) {
          this.showFieldError(tipoObservacionField, 'Debe seleccionar un tipo de observación');
        } else {
          this.clearFieldError(tipoObservacionField);
        }
        this.validateEliminarForm(userId);
      });
    }

    // Validación para la contraseña
    if (passwordField) {
      passwordField.addEventListener('input', () => {
        if (passwordField.value.length < 6) {
          this.showFieldError(passwordField, 'La contraseña debe tener al menos 6 caracteres');
        } else {
          this.clearFieldError(passwordField);
        }
        this.validateEliminarForm(userId);
      });
    }

    // Validación para la confirmación
    if (confirmacionField) {
      confirmacionField.addEventListener('change', () => {
        this.validateEliminarForm(userId);
      });
    }

    // Validación inicial
    this.validateEliminarForm(userId);
  }

  /**
   * Validar formulario de eliminación completo
   */
  validateEliminarForm(userId) {
    const passwordField = document.getElementById(`password${userId}`);
    const tipoObservacionField = document.getElementById(`tipo_observacion${userId}`);
    const observacionField = document.getElementById(`observacion${userId}`);
    const confirmacionField = document.getElementById(`confirmacion${userId}`);
    const submitBtn = document.getElementById(`btnEliminar${userId}`);

    if (!passwordField || !tipoObservacionField || !observacionField || !confirmacionField || !submitBtn) {
      return false;
    }

    const isPasswordValid = passwordField.value.length >= 6;
    const isTipoValid = tipoObservacionField.value !== '';
    const isObservacionValid = observacionField.value.length >= 20;
    const isConfirmacionValid = confirmacionField.checked;

    const isValid = isPasswordValid && isTipoValid && isObservacionValid && isConfirmacionValid;

    // Habilitar/deshabilitar botón
    submitBtn.disabled = !isValid;
    
    // Cambiar estilo del botón
    if (isValid) {
      submitBtn.classList.remove('btn-secondary');
      submitBtn.classList.add('btn-danger');
    } else {
      submitBtn.classList.remove('btn-danger');
      submitBtn.classList.add('btn-secondary');
    }

    return isValid;
  }

  /**
   * Validar campo individual
   */
  validateField(field) {
    const value = field.value.trim();
    let isValid = true;
    let errorMessage = '';

    // Validaciones específicas según el tipo de campo
    if (field.name === 'password') {
      if (value.length < 6) {
        isValid = false;
        errorMessage = 'La contraseña debe tener al menos 6 caracteres';
      }
    } else if (field.name === 'observacion') {
      if (value.length < 20) {
        isValid = false;
        errorMessage = 'La observación debe tener al menos 20 caracteres';
      } else if (value.length > 500) {
        isValid = false;
        errorMessage = 'La observación no puede exceder 500 caracteres';
      }
    } else if (field.name === 'tipo_observacion') {
      if (!value) {
        isValid = false;
        errorMessage = 'Debe seleccionar un tipo de observación';
      }
    }

    if (!isValid) {
      this.showFieldError(field, errorMessage);
    } else {
      this.clearFieldError(field);
    }

    return isValid;
  }

  /**
   * Toggle de visibilidad de contraseña
   */
  togglePasswordVisibility(fieldId) {
    const field = document.getElementById(fieldId);
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
  }

  /**
   * Mostrar error en campo
   */
  showFieldError(field, message) {
    // Remover error previo
    this.clearFieldError(field);

    // Agregar clase de error
    field.classList.add('is-invalid');

    // Crear mensaje de error
    const errorDiv = document.createElement('div');
    errorDiv.className = 'invalid-feedback';
    errorDiv.textContent = message;
    errorDiv.style.display = 'block';

    // Insertar después del campo
    field.parentNode.appendChild(errorDiv);
  }

  /**
   * Limpiar error de campo
   */
  clearFieldError(field) {
    field.classList.remove('is-invalid');
    const errorDiv = field.parentNode.querySelector('.invalid-feedback');
    if (errorDiv) {
      errorDiv.remove();
    }
  }

  /**
   * Manejar envío de formularios
   */
  handleFormSubmit(event) {
    const form = event.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    
    // Validar formulario
    if (!this.validateForm(form)) {
      event.preventDefault();
      return false;
    }

    // Mostrar loading en botón
    if (submitBtn) {
      const originalText = submitBtn.innerHTML;
      submitBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i> Procesando...';
      submitBtn.disabled = true;

      // Restaurar botón después de un tiempo
      setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
      }, 5000);
    }

    return true;
  }

  /**
   * Validar formulario completo
   */
  validateForm(form) {
    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');

    requiredFields.forEach(field => {
      if (!this.validateField(field)) {
        isValid = false;
      }
    });

    return isValid;
  }

  /**
   * Manejar envío de formularios en modales
   */
  handleModalFormSubmit(event) {
    const form = event.target;
    const modal = form.closest('.modal');
    
    if (!this.validateForm(form)) {
      event.preventDefault();
      this.showNotification('Por favor, complete todos los campos requeridos correctamente', 'error');
      return false;
    }

    // Mostrar confirmación antes de enviar


    return true;
  }

  /**
   * Obtener acción del formulario
   */
  getFormAction(form) {
    return form.action.toLowerCase();
  }

  /**
   * Manejar clicks en botones
   */
  handleButtonClick(event) {
    const button = event.target.closest('.btn');
    if (!button) return;

    // Efecto de click
    button.style.transform = 'scale(0.95)';
    setTimeout(() => {
      button.style.transform = '';
    }, 150);

    // Acciones específicas según el tipo de botón
    if (button.classList.contains('btn-danger')) {
      this.handleDangerButtonClick(button, event);
    } else if (button.classList.contains('btn-success')) {
      this.handleSuccessButtonClick(button, event);
    }
  }

  /**
   * Manejar clicks en botones de peligro
   */


  /**
   * Manejar clicks en botones de éxito
   */
  handleSuccessButtonClick(button, event) {
    // Efecto visual adicional
    button.style.boxShadow = '0 0 20px rgba(25, 135, 84, 0.3)';
    setTimeout(() => {
      button.style.boxShadow = '';
    }, 500);
  }

  /**
   * Manejar triggers de modal
   */
  handleModalTrigger(event) {
    const trigger = event.target.closest('[data-bs-toggle="modal"]');
    if (!trigger) return;

    // Efecto visual
    trigger.style.transform = 'scale(0.95)';
    setTimeout(() => {
      trigger.style.transform = '';
    }, 150);
  }

  /**
   * Manejar cierre de modal
   */
  handleModalClose(event) {
    const closeBtn = event.target.closest('[data-bs-dismiss="modal"]');
    if (!closeBtn) return;

    // Efecto visual
    closeBtn.style.transform = 'scale(0.95)';
    setTimeout(() => {
      closeBtn.style.transform = '';
    }, 150);
  }

  /**
   * Mostrar notificación
   */
  showNotification(message, type = 'info', duration = 5000) {
    // Crear notificación
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show`;
    notification.style.cssText = `
      min-width: 300px;
      max-width: 400px;
      margin-bottom: 10px;
      border: none;
      border-radius: 10px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      animation: slideInRight 0.3s ease-out;
    `;

    // Contenido de la notificación
    notification.innerHTML = `
      <div class="d-flex align-items-center">
        <i class="bx ${this.getNotificationIcon(type)} fs-4 me-2"></i>
        <div class="flex-grow-1">
          <strong>${this.getNotificationTitle(type)}</strong>
          <br>
          <small>${message}</small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    `;

    // Agregar al contenedor
    this.notificationContainer.appendChild(notification);

    // Auto-dismiss
    setTimeout(() => {
      this.dismissNotification(notification);
    }, duration);

    // Event listener para cerrar manualmente
    const closeBtn = notification.querySelector('.btn-close');
    if (closeBtn) {
      closeBtn.addEventListener('click', () => {
        this.dismissNotification(notification);
      });
    }

    // Agregar a la lista de notificaciones
    this.notifications.push(notification);

    return notification;
  }

  /**
   * Obtener icono de notificación
   */
  getNotificationIcon(type) {
    const icons = {
      success: 'bx-check-circle',
      error: 'bx-x-circle',
      warning: 'bx-exclamation-triangle',
      info: 'bx-info-circle'
    };
    return icons[type] || icons.info;
  }

  /**
   * Obtener título de notificación
   */
  getNotificationTitle(type) {
    const titles = {
      success: 'Éxito',
      error: 'Error',
      warning: 'Advertencia',
      info: 'Información'
    };
    return titles[type] || titles.info;
  }

  /**
   * Cerrar notificación
   */
  dismissNotification(notification) {
    if (notification.parentNode) {
      notification.style.animation = 'slideOutRight 0.3s ease-in';
      setTimeout(() => {
        if (notification.parentNode) {
          notification.remove();
        }
      }, 300);
    }

    // Remover de la lista
    const index = this.notifications.indexOf(notification);
    if (index > -1) {
      this.notifications.splice(index, 1);
    }
  }

  /**
   * Cerrar todas las notificaciones
   */
  dismissAllNotifications() {
    this.notifications.forEach(notification => {
      this.dismissNotification(notification);
    });
  }

  /**
   * Actualizar información en tiempo real
   */
  updateUserInfo() {
    // Aquí se pueden agregar actualizaciones en tiempo real
    // Por ejemplo, actualizar el tiempo transcurrido desde la última actividad
    const lastUpdateElement = document.querySelector('[data-last-update]');
    if (lastUpdateElement) {
      const lastUpdate = new Date(lastUpdateElement.dataset.lastUpdate);
      const now = new Date();
      const diff = Math.floor((now - lastUpdate) / 1000 / 60); // minutos
      
      if (diff > 0) {
        lastUpdateElement.textContent = `${diff} minuto${diff > 1 ? 's' : ''} atrás`;
      }
    }
  }

  /**
   * Obtener instancia singleton
   */
  static getInstance() {
    if (!UserShowManager.instance) {
      UserShowManager.instance = new UserShowManager();
    }
    return UserShowManager.instance;
  }
}

// Exportar para uso global
window.UserShowManager = UserShowManager;

// Función global para toggle de contraseña
window.togglePasswordVisibility = function(fieldId) {
  const userShowManager = UserShowManager.getInstance();
  userShowManager.togglePasswordVisibility(fieldId);
}; 