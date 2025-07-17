<section>
    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <label for="update_password_current_password" class="form-label fw-bold">
                    <i class="bi bi-key me-2"></i>Contraseña Actual
                </label>
                <div class="input-group">
                    <input type="password" 
                           id="update_password_current_password" 
                           name="current_password" 
                           class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" 
                           autocomplete="current-password"
                           placeholder="Ingrese su contraseña actual"
                           required>
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('update_password_current_password')">
                        <i class="bi bi-eye" id="eye-current"></i>
                    </button>
                    @error('current_password', 'updatePassword')
                        <div class="invalid-feedback">
                            <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="mb-3">
                <label for="update_password_password" class="form-label fw-bold">
                    <i class="bi bi-lock me-2"></i>Nueva Contraseña
                </label>
                <div class="input-group">
                    <input type="password" 
                           id="update_password_password" 
                           name="password" 
                           class="form-control @error('password', 'updatePassword') is-invalid @enderror" 
                           autocomplete="new-password"
                           placeholder="Mínimo 8 caracteres"
                           required>
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('update_password_password')">
                        <i class="bi bi-eye" id="eye-new"></i>
                    </button>
                    @error('password', 'updatePassword')
                        <div class="invalid-feedback">
                            <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="mb-3">
                <label for="update_password_password_confirmation" class="form-label fw-bold">
                    <i class="bi bi-lock-fill me-2"></i>Confirmar Contraseña
                </label>
                <div class="input-group">
                    <input type="password" 
                           id="update_password_password_confirmation" 
                           name="password_confirmation" 
                           class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" 
                           autocomplete="new-password"
                           placeholder="Repita la nueva contraseña"
                           required>
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('update_password_password_confirmation')">
                        <i class="bi bi-eye" id="eye-confirm"></i>
                    </button>
                    @error('password_confirmation', 'updatePassword')
                        <div class="invalid-feedback">
                            <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-info border-0 shadow-sm mb-4">
        <div class="d-flex align-items-center gap-3">
            <i class="bi bi-shield-check fs-1 text-info"></i>
            <div>
                <h6 class="alert-heading mb-1"><strong>Recomendaciones de Seguridad</strong></h6>
                <ul class="mb-0 small">
                    <li>Use al menos 8 caracteres</li>
                    <li>Combine letras mayúsculas, minúsculas, números y símbolos</li>
                    <li>Evite información personal fácil de adivinar</li>
                    <li>No reutilice contraseñas de otros servicios</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="d-flex align-items-center gap-3">
        <button type="submit" class="btn btn-info btn-lg">
            <i class="bi bi-shield-check me-2"></i>Actualizar Contraseña
        </button>
    </div>
</section>

<style>
/* Estilos para los botones de mostrar/ocultar contraseña */
.input-group .btn-outline-secondary {
    border-left: none;
    border-color: #ced4da;
}

.input-group .btn-outline-secondary:hover {
    background-color: #e9ecef;
    border-color: #ced4da;
    color: #495057;
}

.input-group .form-control:focus + .btn-outline-secondary {
    border-color: #007bff;
}

/* Animación para el ícono del ojo */
.bi-eye, .bi-eye-slash {
    transition: all 0.2s ease-in-out;
}

.bi-eye-slash {
    color: #007bff;
}

/* Estilos para campos con error */
.input-group .form-control.is-invalid + .btn-outline-secondary {
    border-color: #dc3545;
}

.input-group .form-control.is-invalid:focus + .btn-outline-secondary {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}
</style>

<script>
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const eyeIcon = input.nextElementSibling.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        eyeIcon.classList.remove('bi-eye');
        eyeIcon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        eyeIcon.classList.remove('bi-eye-slash');
        eyeIcon.classList.add('bi-eye');
    }
}
</script>
