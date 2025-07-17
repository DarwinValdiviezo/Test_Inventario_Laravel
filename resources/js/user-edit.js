/**
 * Sistema de Edición de Usuarios
 * Funcionalidades avanzadas para el formulario de edición de usuarios
 */

class UserEditManager {
  constructor() {
    this.form = document.getElementById('formEditarUsuario');
    if (this.form) {
      this.initializeComponents();
      this.initializeValidation();
      this.initializePasswordStrength();
      this.initializePasswordToggle();
      this.initializeFormReset();
      this.initializeSubmitHandler();
      this.initializeRealTimeValidation();
      this.initializeRoleInfo();
      this.initializeTermsValidation();
      this.initializeAdminRestrictions();
    }
  }

  initializeComponents() {
    // Inicializar tooltips
    if (typeof bootstrap !== 'undefined') {
      const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
      });
    }

    // Inicializar modales
    if (typeof bootstrap !== 'undefined') {
      const modalTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="modal"]'));
      modalTriggerList.map(function (modalTriggerEl) {
        return new bootstrap.Modal(modalTriggerEl);
      });
    }
  }

  initializeValidation() {
    if (!this.form) return;
    // Validación en tiempo real para todos los campos
    const inputs = this.form.querySelectorAll('input, select');
    inputs.forEach(input => {
      input.addEventListener('blur', () => this.validateField(input));
      input.addEventListener('input', () => this.clearFieldError(input));
    });

    // Validación especial para contraseñas (opcionales en edición)
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');
    
    if (passwordInput) {
      passwordInput.addEventListener('input', () => {
        this.validatePassword();
        this.updatePasswordStrength();
        this.validatePasswordConfirmation();
        this.togglePasswordStrengthVisibility();
      });
    }
    
    if (confirmInput) {
      confirmInput.addEventListener('input', () => {
        this.validatePasswordConfirmation();
      });
    }
  }

  initializeRealTimeValidation() {
    // Validación en tiempo real para email
    const emailInput = document.getElementById('email');
    if (emailInput) {
      let emailTimeout;
      emailInput.addEventListener('input', () => {
        clearTimeout(emailTimeout);
        emailTimeout = setTimeout(() => {
          this.validateEmailUniqueness(emailInput.value);
        }, 500);
      });
    }

    // Validación en tiempo real para nombre
    const nameInput = document.getElementById('name');
    if (nameInput) {
      nameInput.addEventListener('input', () => {
        this.validateNameFormat(nameInput.value);
      });
    }
  }

  validateField(field) {
    const value = field.value.trim();
    const validation = field.dataset.validation;
    
    if (!validation) return true;

    const rules = validation.split('|');
    let isValid = true;
    let errorMessage = '';

    for (const rule of rules) {
      const [ruleName, ruleValue] = rule.split(':');
      
      switch (ruleName) {
        case 'required':
          if (!value) {
            isValid = false;
            errorMessage = 'Este campo es obligatorio';
          }
          break;
          
        case 'optional':
          // Para campos opcionales, solo validar si hay valor
          break;
          
        case 'email':
          if (value && !this.isValidEmail(value)) {
            isValid = false;
            errorMessage = 'Ingrese un correo electrónico válido';
          }
          break;
          
        case 'min':
          if (value && value.length < parseInt(ruleValue)) {
            isValid = false;
            errorMessage = `Mínimo ${ruleValue} caracteres`;
          }
          break;
          
        case 'max':
          if (value && value.length > parseInt(ruleValue)) {
            isValid = false;
            errorMessage = `Máximo ${ruleValue} caracteres`;
          }
          break;
          
        case 'pattern':
          if (value && !this.isValidPattern(value, field.pattern)) {
            isValid = false;
            errorMessage = 'Formato no válido';
          }
          break;
          
        case 'match':
          const matchField = document.getElementById(ruleValue);
          if (value && value !== matchField.value) {
            isValid = false;
            errorMessage = 'Los campos no coinciden';
          }
          break;
      }
      
      if (!isValid) break;
    }

    this.setFieldValidation(field, isValid, errorMessage);
    return isValid;
  }

  validateEmailUniqueness(email) {
    if (!email || !this.isValidEmail(email)) return;

    // Simular verificación de unicidad (en producción usaría AJAX)
    const existingEmails = ['admin@example.com', 'user@example.com']; // Ejemplo
    if (existingEmails.includes(email.toLowerCase())) {
      this.setFieldValidation(document.getElementById('email'), false, 'Este correo ya está registrado');
    }
  }

  validateNameFormat(name) {
    const nameRegex = /^[A-Za-zÁáÉéÍíÓóÚúÑñ\s]+$/;
    if (name && !nameRegex.test(name)) {
      this.setFieldValidation(document.getElementById('name'), false, 'Solo se permiten letras y espacios');
    }
  }

  isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }

  isValidPattern(value, pattern) {
    const regex = new RegExp(pattern);
    return regex.test(value);
  }

  setFieldValidation(field, isValid, message) {
    field.classList.remove('is-valid', 'is-invalid');
    field.classList.add(isValid ? 'is-valid' : 'is-invalid');
    
    const errorElement = document.getElementById(field.id + '-error');
    if (errorElement) {
      errorElement.textContent = message;
    }
  }

  clearFieldError(field) {
    field.classList.remove('is-invalid');
    const errorElement = document.getElementById(field.id + '-error');
    if (errorElement) {
      errorElement.textContent = '';
    }
  }

  validatePassword() {
    const password = document.getElementById('password');
    if (!password) return true;

    const value = password.value;
    
    // Si está vacío, es válido (opcional en edición)
    if (!value) {
      this.setFieldValidation(password, true, '');
      return true;
    }

    let isValid = true;
    let errorMessage = '';

    if (value.length < 8) {
      isValid = false;
      errorMessage = 'La contraseña debe tener al menos 8 caracteres';
    } else if (!this.hasUpperCase(value)) {
      isValid = false;
      errorMessage = 'Debe incluir al menos una mayúscula';
    } else if (!this.hasLowerCase(value)) {
      isValid = false;
      errorMessage = 'Debe incluir al menos una minúscula';
    } else if (!this.hasNumber(value)) {
      isValid = false;
      errorMessage = 'Debe incluir al menos un número';
    }

    this.setFieldValidation(password, isValid, errorMessage);
    return isValid;
  }

  validatePasswordConfirmation() {
    const password = document.getElementById('password');
    const confirmation = document.getElementById('password_confirmation');
    
    if (!password || !confirmation) return true;

    // Si la contraseña está vacía, la confirmación también debe estar vacía
    if (!password.value && !confirmation.value) {
      this.setFieldValidation(confirmation, true, '');
      return true;
    }

    const isValid = password.value === confirmation.value;
    const errorMessage = isValid ? '' : 'Las contraseñas no coinciden';
    
    this.setFieldValidation(confirmation, isValid, errorMessage);
    return isValid;
  }

  hasUpperCase(str) {
    return /[A-Z]/.test(str);
  }

  hasLowerCase(str) {
    return /[a-z]/.test(str);
  }

  hasNumber(str) {
    return /\d/.test(str);
  }

  updatePasswordStrength() {
    const password = document.getElementById('password');
    const strengthBar = document.getElementById('passwordStrength');
    const strengthText = document.getElementById('passwordStrengthText');
    
    if (!password || !strengthBar || !strengthText) return;

    const value = password.value;
    
    // Si está vacío, ocultar el indicador
    if (!value) {
      document.getElementById('passwordStrengthContainer').style.display = 'none';
      return;
    }

    let strength = 0;
    let text = 'Muy débil';
    let color = 'danger';

    if (value.length >= 8) strength += 25;
    if (this.hasUpperCase(value)) strength += 25;
    if (this.hasLowerCase(value)) strength += 25;
    if (this.hasNumber(value)) strength += 25;

    if (strength >= 100) {
      text = 'Muy fuerte';
      color = 'success';
    } else if (strength >= 75) {
      text = 'Fuerte';
      color = 'info';
    } else if (strength >= 50) {
      text = 'Media';
      color = 'warning';
    } else if (strength >= 25) {
      text = 'Débil';
      color = 'danger';
    }

    strengthBar.style.width = strength + '%';
    strengthBar.className = `progress-bar bg-${color}`;
    strengthText.textContent = text;
  }

  togglePasswordStrengthVisibility() {
    const password = document.getElementById('password');
    const container = document.getElementById('passwordStrengthContainer');
    
    if (password && container) {
      if (password.value) {
        container.style.display = 'block';
      } else {
        container.style.display = 'none';
      }
    }
  }

  initializePasswordStrength() {
    const passwordInput = document.getElementById('password');
    if (passwordInput) {
      passwordInput.addEventListener('input', () => {
        this.updatePasswordStrength();
        this.togglePasswordStrengthVisibility();
      });
    }
  }

  initializePasswordToggle() {
    const togglePassword = document.getElementById('togglePassword');
    const toggleConfirmation = document.getElementById('togglePasswordConfirmation');
    const passwordInput = document.getElementById('password');
    const confirmationInput = document.getElementById('password_confirmation');

    if (togglePassword && passwordInput) {
      togglePassword.addEventListener('click', () => {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        togglePassword.innerHTML = type === 'password' ? '<i class="bx bx-show"></i>' : '<i class="bx bx-hide"></i>';
      });
    }

    if (toggleConfirmation && confirmationInput) {
      toggleConfirmation.addEventListener('click', () => {
        const type = confirmationInput.getAttribute('type') === 'password' ? 'text' : 'password';
        confirmationInput.setAttribute('type', type);
        toggleConfirmation.innerHTML = type === 'password' ? '<i class="bx bx-show"></i>' : '<i class="bx bx-hide"></i>';
      });
    }
  }

  initializeFormReset() {
    const resetBtn = document.getElementById('resetForm');
    if (resetBtn) {
      resetBtn.addEventListener('click', (e) => {
        e.preventDefault();
        if (confirm('¿Está seguro que desea restablecer todos los campos a sus valores originales?')) {
          this.form.reset();
          this.clearAllErrors();
          this.updatePasswordStrength();
          this.togglePasswordStrengthVisibility();
          this.showNotification('Formulario restablecido', 'info');
        }
      });
    }
  }

  clearAllErrors() {
    const fields = this.form.querySelectorAll('input, select');
    fields.forEach(field => {
      field.classList.remove('is-valid', 'is-invalid');
      const errorElement = document.getElementById(field.id + '-error');
      if (errorElement) {
        errorElement.textContent = '';
      }
    });
  }

  initializeSubmitHandler() {
    if (this.form) {
      this.form.addEventListener('submit', (e) => {
        if (!this.validateForm()) {
          e.preventDefault();
          this.showNotification('Por favor, corrija los errores en el formulario', 'error');
          this.scrollToFirstError();
        } else {
          this.showLoadingState();
        }
      });
    }
  }

  validateForm() {
    const fields = this.form.querySelectorAll('input, select');
    let isValid = true;

    fields.forEach(field => {
      if (!this.validateField(field)) {
        isValid = false;
      }
    });

    // Validar términos y condiciones
    const termsCheckbox = document.getElementById('terms');
    if (termsCheckbox && !termsCheckbox.checked) {
      isValid = false;
      termsCheckbox.classList.add('is-invalid');
    }

    return isValid;
  }

  scrollToFirstError() {
    const firstError = this.form.querySelector('.is-invalid');
    if (firstError) {
      firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
      firstError.focus();
    }
  }

  showLoadingState() {
    const submitBtn = document.getElementById('submitBtn');
    if (submitBtn) {
      const originalText = submitBtn.innerHTML;
      submitBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i> Actualizando...';
      submitBtn.disabled = true;

      // Restaurar después de 5 segundos por si hay error
      setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
      }, 5000);
    }
  }

  initializeRoleInfo() {
    const rolesSelect = document.getElementById('roles');
    if (rolesSelect) {
      rolesSelect.addEventListener('change', () => {
        const selectedOption = rolesSelect.options[rolesSelect.selectedIndex];
        const description = selectedOption.dataset.description;
        
        // Mostrar información del rol seleccionado
        if (description) {
          this.showRoleInfo(description);
        }
      });
    }
  }

  showRoleInfo(description) {
    // Crear o actualizar tooltip con información del rol
    const rolesSelect = document.getElementById('roles');
    if (rolesSelect) {
      rolesSelect.title = description;
      
      // Destruir tooltip existente si hay
      if (rolesSelect._tooltip) {
        rolesSelect._tooltip.dispose();
      }
      
      // Crear nuevo tooltip
      if (typeof bootstrap !== 'undefined') {
        rolesSelect._tooltip = new bootstrap.Tooltip(rolesSelect, {
          title: description,
          placement: 'top',
          trigger: 'hover'
        });
      }
    }
  }

  initializeTermsValidation() {
    const termsCheckbox = document.getElementById('terms');
    if (termsCheckbox) {
      termsCheckbox.addEventListener('change', () => {
        termsCheckbox.classList.remove('is-invalid');
      });
    }
  }

  initializeAdminRestrictions() {
    // Verificar si el usuario es administrador
    const isAdmin = document.querySelector('select[name="estado"]').disabled;
    
    if (isAdmin) {
      this.showAdminRestrictionInfo();
    }
  }

  showAdminRestrictionInfo() {
    // Mostrar información sobre restricciones de administrador
    const restrictionInfo = `
      <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="bx bx-lock me-2"></i>
        <strong>Restricciones de Administrador:</strong> 
        No se pueden modificar el rol ni el estado de un usuario administrador por seguridad del sistema.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    `;
    
    // Insertar después del header del formulario
    const cardHeader = document.querySelector('.card-header');
    if (cardHeader) {
      cardHeader.insertAdjacentHTML('afterend', restrictionInfo);
    }
  }

  showNotification(message, type = 'info') {
    if (window.userManagement && window.userManagement.showNotification) {
      window.userManagement.showNotification(message, type);
    } else if (window.NotificationSystem) {
      // Usar NotificationSystem directamente si está disponible
      const notificationSystem = new NotificationSystem();
      notificationSystem.show(message, type);
    } else {
      // Fallback con notificación personalizada
      this.showFallbackNotification(message, type);
    }
  }

  showFallbackNotification(message, type) {
    // Crear notificación de fallback si no hay sistema disponible
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = `
      top: 20px;
      right: 20px;
      z-index: 9999;
      min-width: 300px;
      max-width: 400px;
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

    document.body.appendChild(notification);

    // Auto-remove después de 5 segundos
    setTimeout(() => {
      this.hideFallbackNotification(notification);
    }, 5000);

    // Event listener para cerrar manualmente
    notification.querySelector('.btn-close').addEventListener('click', () => {
      this.hideFallbackNotification(notification);
    });
  }

  hideFallbackNotification(notification) {
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

  // Método para exportar funcionalidades
  static getInstance() {
    if (!UserEditManager.instance) {
      UserEditManager.instance = new UserEditManager();
    }
    return UserEditManager.instance;
  }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
  // Crear instancia global
  window.userEditManager = UserEditManager.getInstance();

  // Mostrar notificaciones de sesión si existen
  // Las notificaciones se manejan desde la vista Blade
});

// Exportar para uso global
window.UserEditManager = UserEditManager; 