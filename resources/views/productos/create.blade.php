@extends('layouts.app')

@section('title', 'Nuevo Producto')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bi bi-house"></i> Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('productos.index') }}"><i class="bi bi-box"></i> Productos</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><i class="bi bi-plus-circle"></i> Nuevo Producto</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card card-outline card-primary shadow-lg">
                <div class="card-header bg-gradient-primary text-white">
                    <h3 class="card-title mb-0">
                        <i class="bi bi-plus-circle me-2"></i> Crear Nuevo Producto
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('productos.index') }}" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                    </div>
                </div>
                <form method="POST" action="{{ route('productos.store') }}" enctype="multipart/form-data" autocomplete="off" id="productoForm">
                    @csrf
                    <div class="card-body row">
                        <div class="col-md-5 text-center mb-3">
                            <label for="imagen" class="form-label fw-bold">
                                <i class="bi bi-image me-1 text-primary"></i> Imagen del Producto <span class="text-danger">*</span>
                            </label>
                            <div class="form-group mb-4 text-center">
                                <label class="form-label fw-bold d-block">
                                    <i class="bi bi-image text-secondary fs-2" id="icono-preview" style="display: {{ old('imagen') ? 'none' : 'inline' }};"></i>
                                    <img id="preview-imagen" src="#" alt="Previsualización" class="img-fluid rounded shadow-sm d-none mx-auto" style="max-height: 180px;">
                                </label>
                                <input type="file" name="imagen" id="imagen" accept="image/jpeg,image/png,image/webp" class="form-control form-control-lg @error('imagen') is-invalid @enderror" required>
                            @error('imagen')
                                    <div class="invalid-feedback d-block"><i class="bi bi-exclamation-triangle me-1"></i> {{ $message }}</div>
                            @enderror
                                <div class="form-text">Solo JPG, PNG o WEBP. Máx 2MB.</div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="form-group mb-4">
                                <label for="nombre" class="form-label fw-bold">
                                    <i class="bi bi-cube me-1 text-primary"></i> Nombre <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="nombre" id="nombre"
                                    class="form-control form-control-lg @error('nombre') is-invalid @enderror"
                                    value="{{ old('nombre') }}"
                                    required
                                    maxlength="100"
                                    pattern="^[^<>]+$"
                                    placeholder="Nombre del producto"
                                    autofocus>
                                @error('nombre')<div class="invalid-feedback"><i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}</div>@enderror
                                <small class="form-text text-danger" id="nombreHelp"></small>
                            </div>
                            <div class="form-group mb-4">
                                <label for="descripcion" class="form-label fw-bold">
                                    <i class="bi bi-card-text me-1 text-primary"></i> Descripción
                                </label>
                                <textarea name="descripcion" id="descripcion"
                                    class="form-control @error('descripcion') is-invalid @enderror"
                                    rows="3"
                                    maxlength="1000"
                                    pattern="^[^<>]*$"
                                    placeholder="Descripción del producto (opcional)">{{ old('descripcion') }}</textarea>
                                @error('descripcion')<div class="invalid-feedback"><i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}</div>@enderror
                                <small class="form-text text-danger" id="descripcionHelp"></small>
                            </div>
                            <div class="form-group mb-4">
                                <label for="categoria_id" class="form-label fw-bold">
                                    <i class="bi bi-tag me-1 text-primary"></i> Categoría
                                </label>
                                <select name="categoria_id" id="categoria_id" class="form-select @error('categoria_id') is-invalid @enderror">
                                    <option value="">Sin categoría</option>
                                    @foreach($categorias as $cat)
                                        <option value="{{ $cat->id }}" {{ old('categoria_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('categoria_id')<div class="invalid-feedback"><i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group mb-4">
                                <label for="stock" class="form-label fw-bold">
                                    <i class="bi bi-box-seam me-1 text-primary"></i> Stock <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="stock" id="stock" class="form-control form-control-lg @error('stock') is-invalid @enderror" value="{{ old('stock', 0) }}" min="0" max="1000" required placeholder="Cantidad en stock">
                                @error('stock')<div class="invalid-feedback"><i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}</div>@enderror
                                <small class="form-text text-danger" id="stockHelp"></small>
                            </div>
                            <div class="form-group mb-4">
                                <label for="precio" class="form-label fw-bold">
                                    <i class="bi bi-currency-dollar me-1 text-primary"></i> Precio <span class="text-danger">*</span>
                                </label>
                                <input type="number" step="0.01" name="precio" id="precio" class="form-control form-control-lg @error('precio') is-invalid @enderror" value="{{ old('precio') }}" min="0.01" max="10000" required placeholder="Precio del producto">
                                @error('precio')<div class="invalid-feedback"><i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}</div>@enderror
                                <small class="form-text text-danger" id="precioHelp"></small>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            <small><i class="bi bi-info-circle me-1"></i> Los campos marcados con <span class="text-danger">*</span> son obligatorios</small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('productos.index') }}" class="btn btn-secondary btn-lg">
                                <i class="bi bi-x-lg me-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                <span class="btn-text"><i class="bi bi-check-lg me-1"></i> Guardar Producto</span>
                                <span class="btn-loading d-none"><span class="spinner-border spinner-border-sm me-1"></span> Guardando...</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Toast Container para notificaciones -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>

@push('styles')
<style>
.card-outline.card-primary {
    border-top: 3px solid #007bff;
    border-radius: 12px;
    overflow: hidden;
}
.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}
.form-control-lg, .form-select-lg {
    border-radius: 8px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease-in-out;
    font-size: 1rem;
    padding: 0.75rem 1rem;
}
.form-control-lg:focus, .form-select-lg:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    transform: translateY(-1px);
}
.form-control:focus, .form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
.form-label {
    color: #495057;
    font-size: 0.95rem;
    margin-bottom: 0.5rem;
}
.btn-lg {
    border-radius: 8px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease-in-out;
    padding: 0.75rem 1.5rem;
}
.btn-lg:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.breadcrumb {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 8px;
    padding: 0.75rem 1rem;
    border: 1px solid #dee2e6;
}
.breadcrumb-item a {
    color: #007bff;
    text-decoration: none;
    transition: color 0.2s ease;
}
.breadcrumb-item a:hover {
    color: #0056b3;
}
.breadcrumb-item.active {
    color: #6c757d;
}
.card {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    border: none;
}
.card-header {
    border-bottom: none;
    padding: 1.5rem;
}
.card-body {
    padding: 2rem;
}
.card-footer {
    border-top: 1px solid #dee2e6;
    padding: 1.5rem;
}
.btn-loading {
    display: inline-flex;
    align-items: center;
}
.d-none {
    display: none !important;
}
@media (max-width: 768px) {
    .card-footer .d-flex {
        flex-direction: column;
        gap: 1rem;
    }
    .btn-lg {
        width: 100%;
    }
    .card-body {
        padding: 1.5rem;
    }
}
.input-group .btn {
    border-radius: 0 8px 8px 0;
    border-left: none;
    transition: all 0.2s ease-in-out;
}
.input-group .btn:hover {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
}
.input-group .form-control {
    border-radius: 8px 0 0 8px;
}
textarea.form-control {
    resize: vertical;
    min-height: 100px;
}
.form-select option {
    padding: 0.5rem;
}
.form-control:hover, .form-select:hover {
    border-color: #007bff;
}
.btn:hover {
    transform: translateY(-1px);
}
* {
    transition: all 0.2s ease-in-out;
}
.toast {
    border: none;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}
.toast.success {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    border-left: 4px solid #28a745;
}
.toast.error {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    border-left: 4px solid #dc3545;
}
.toast.warning {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    border-left: 4px solid #ffc107;
}
.toast.info {
    background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
    border-left: 4px solid #17a2b8;
}
</style>
@endpush

@push('scripts')
<script>
// Previsualización de imagen con icono
const inputImagen = document.getElementById('imagen');
const preview = document.getElementById('preview-imagen');
const icono = document.getElementById('icono-preview');
inputImagen.addEventListener('change', function(e) {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(ev) {
            preview.src = ev.target.result;
            preview.classList.remove('d-none');
            icono.style.display = 'none';
        }
        reader.readAsDataURL(file);
    } else {
        preview.classList.add('d-none');
        icono.style.display = 'inline';
    }
});

const form = document.getElementById('productoForm');
const submitBtn = document.getElementById('submitBtn');
const btnText = submitBtn.querySelector('.btn-text');
const btnLoading = submitBtn.querySelector('.btn-loading');

// Toast para notificaciones
function showToast(type, title, message) {
    const container = document.querySelector('.toast-container');
    const toastId = 'toast-' + Date.now();
    const iconMap = {
        success: 'bi-check-circle-fill',
        error: 'bi-exclamation-triangle-fill',
        warning: 'bi-exclamation-triangle-fill',
        info: 'bi-info-circle-fill'
    };
    const colorMap = {
        success: 'success',
        error: 'danger',
        warning: 'warning',
        info: 'info'
    };
    const toast = document.createElement('div');
    toast.className = `toast ${type} show`;
    toast.id = toastId;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    toast.innerHTML = `
        <div class="toast-header">
            <i class="bi ${iconMap[type]} text-${colorMap[type]} me-2"></i>
            <strong class="me-auto">${title}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            ${message}
        </div>
    `;
    container.appendChild(toast);
    setTimeout(() => {
        const toastElement = document.getElementById(toastId);
        if (toastElement) {
            toastElement.remove();
        }
    }, 5000);
}

// Validación en tiempo real
const inputs = form.querySelectorAll('input, select, textarea');
inputs.forEach(input => {
    input.addEventListener('blur', function() {
        if (this.hasAttribute('required') && !this.value.trim()) {
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-invalid');
        }
    });
    input.addEventListener('input', function() {
        if (this.classList.contains('is-invalid')) {
            this.classList.remove('is-invalid');
            const feedback = this.parentElement.querySelector('.invalid-feedback');
            if (feedback) {
                feedback.innerHTML = '';
            }
        }
    });
});

// Envío del formulario con AJAX
form.addEventListener('submit', function(e) {
    e.preventDefault();
    btnText.classList.add('d-none');
    btnLoading.classList.remove('d-none');
    submitBtn.disabled = true;
    const formData = new FormData(form);
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(async response => {
        let data;
        try {
            data = await response.json();
        } catch (e) {
            showToast('error', 'Error inesperado', 'El servidor devolvió una respuesta inesperada. Intenta de nuevo o contacta al administrador.');
            return { status: response.status, data: null };
        }
        return { status: response.status, data: data };
    })
    .then(result => {
        if (result.status === 422) {
            showToast('error', 'Error de Validación', 'Por favor, corrige los errores en el formulario.');
            if (result.data.errors) {
                mostrarErroresNombre(result.data.errors);
                Object.keys(result.data.errors).forEach(field => {
                    // Si el error viene en '0', lo mostramos en nombre
                    if (field === '0') {
                        const input = document.getElementById('nombre');
                        if (input) {
                            input.classList.add('is-invalid');
                            const feedback = input.parentElement.querySelector('.invalid-feedback');
                            if (feedback) {
                                feedback.innerHTML = `<i class="bi bi-exclamation-triangle me-1"></i>${result.data.errors[field][0]}`;
                            }
                        }
                    } else {
                        const input = document.getElementById(field);
                        if (input) {
                            input.classList.add('is-invalid');
                            const feedback = input.parentElement.querySelector('.invalid-feedback');
                            if (feedback) {
                                feedback.innerHTML = `<i class="bi bi-exclamation-triangle me-1"></i>${result.data.errors[field][0]}`;
                            }
                        }
                    }
                });
            }
        } else if (result.data.success) {
            showToast('success', '¡Producto Creado!', result.data.message);
            setTimeout(() => {
                window.location.href = result.data.redirect || '{{ route("productos.index") }}';
            }, 2000);
        } else {
            showToast('error', 'Error al crear producto', result.data.message || 'Ha ocurrido un error inesperado.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Error de Conexión', 'No se pudo conectar con el servidor. Verifique su conexión.');
    })
    .finally(() => {
        btnText.classList.remove('d-none');
        btnLoading.classList.add('d-none');
        submitBtn.disabled = false;
    });
});

// Validación en tiempo real y ayuda visual
const nombreInput = document.getElementById('nombre');
const descripcionInput = document.getElementById('descripcion');
const stockInput = document.getElementById('stock');
const precioInput = document.getElementById('precio');
const imagenInput = document.getElementById('imagen');

document.addEventListener('input', function(e) {
    if (e.target === nombreInput) {
        if (nombreInput.value.length > 100) {
            document.getElementById('nombreHelp').textContent = 'El nombre no puede tener más de 100 caracteres.';
        } else if (/[<>]/.test(nombreInput.value)) {
            document.getElementById('nombreHelp').textContent = 'El nombre no puede contener < ni >.';
        } else {
            document.getElementById('nombreHelp').textContent = '';
        }
    }
    if (e.target === descripcionInput) {
        if (descripcionInput.value.length > 1000) {
            document.getElementById('descripcionHelp').textContent = 'La descripción no puede tener más de 1000 caracteres.';
        } else if (/[<>]/.test(descripcionInput.value) || /<script|<\/script/i.test(descripcionInput.value)) {
            document.getElementById('descripcionHelp').textContent = 'La descripción no puede contener etiquetas <, > ni scripts.';
        } else {
            document.getElementById('descripcionHelp').textContent = '';
        }
    }
    if (e.target === stockInput) {
        if (parseInt(stockInput.value) < 0) {
            document.getElementById('stockHelp').textContent = 'El stock no puede ser menor que 0.';
        } else if (parseInt(stockInput.value) > 1000) {
            document.getElementById('stockHelp').textContent = 'El stock no puede ser mayor a 1000 unidades por día.';
    } else {
            document.getElementById('stockHelp').textContent = '';
        }
    }
    if (e.target === precioInput) {
        if (parseFloat(precioInput.value) <= 0) {
            document.getElementById('precioHelp').textContent = 'El precio debe ser mayor a 0.';
        } else if (parseFloat(precioInput.value) > 10000) {
            document.getElementById('precioHelp').textContent = 'El precio no puede ser mayor a $10,000.';
    } else {
            document.getElementById('precioHelp').textContent = '';
        }
    }
    if (e.target === imagenInput) {
        const file = imagenInput.files[0];
        if (file) {
            const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                document.getElementById('imagenHelp').textContent = 'La imagen debe ser JPG, JPEG, PNG o WEBP.';
            } else if (file.size > 2 * 1024 * 1024) {
                document.getElementById('imagenHelp').textContent = 'La imagen no debe pesar más de 2MB.';
            } else {
                document.getElementById('imagenHelp').textContent = 'Formatos permitidos: JPG, JPEG, PNG, WEBP. Tamaño máximo: 2MB.';
            }
        } else {
            document.getElementById('imagenHelp').textContent = 'Formatos permitidos: JPG, JPEG, PNG, WEBP. Tamaño máximo: 2MB.';
        }
    }
});

// Mostrar error de nombre duplicado aunque venga en la clave '0'
function mostrarErroresNombre(errors) {
    const input = document.getElementById('nombre');
    let feedback = input.parentElement.querySelector('.invalid-feedback');
    if (!feedback) {
        feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        input.parentElement.appendChild(feedback);
    }
    input.classList.add('is-invalid');
    if (errors['nombre']) {
        feedback.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i> ' + errors['nombre'][0];
    } else if (errors['0']) {
        feedback.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i> ' + errors['0'][0];
    }
}
</script>
@endpush
@endsection
