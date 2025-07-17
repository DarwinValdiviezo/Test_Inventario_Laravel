@extends('layouts.app')

@section('title', 'Detalle de Producto')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bi bi-house"></i> Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('productos.index') }}"><i class="bi bi-box"></i> Productos</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><i class="bi bi-cube"></i> {{ $producto->nombre }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <!-- Información Principal del Producto -->
        <div class="col-lg-8">
            <div class="card card-outline card-primary shadow-lg mb-4">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="bi bi-cube me-2"></i> Información del Producto
                        </h3>
                        <div class="d-flex gap-2">
                            <a href="{{ route('productos.edit', $producto) }}" class="btn btn-outline-light btn-sm">
                                <i class="bi bi-pencil me-1"></i> Editar
                            </a>
                            <a href="{{ route('productos.index') }}" class="btn btn-outline-light btn-sm">
                                <i class="bi bi-arrow-left me-1"></i> Volver
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Imagen del Producto -->
                        <div class="col-md-5 text-center mb-4">
                            <div class="product-image-container">
                                @if($producto->imagen)
                                    <img src="{{ asset('storage/productos/' . $producto->imagen) }}" 
                                         class="img-fluid rounded shadow-lg product-image" 
                                         alt="Imagen del producto"
                                         style="max-height: 280px; width: auto;">
                                @else
                                    <div class="no-image-placeholder">
                                        <i class="bi bi-image text-muted" style="font-size: 4rem;"></i>
                                        <p class="text-muted mt-2">Sin imagen</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Información del Producto -->
                        <div class="col-md-7">
                            <div class="product-info">
                                <h2 class="product-title mb-3">{{ $producto->nombre }}</h2>
                                
                                @if($producto->descripcion)
                                    <div class="product-description mb-4">
                                        <h6 class="text-muted mb-2"><i class="bi bi-card-text me-1"></i> Descripción</h6>
                                        <p class="description-text">{{ $producto->descripcion }}</p>
                                    </div>
                                @endif
                                
                                <!-- Categoría -->
                                <div class="info-item mb-3">
                                    <span class="info-label"><i class="bi bi-tag me-1"></i> Categoría:</span>
                                    <span class="badge bg-primary fs-6">{{ $producto->categoria->nombre ?? 'Sin categoría' }}</span>
                                </div>
                                
                                <!-- Stock y Precio -->
                                <div class="row mb-4">
                                    <div class="col-6">
                                        <div class="stat-card bg-success text-white">
                                            <div class="stat-icon">
                                                <i class="bi bi-box-seam"></i>
                                            </div>
                                            <div class="stat-content">
                                                <div class="stat-value">{{ $producto->stock }}</div>
                                                <div class="stat-label">Stock</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="stat-card bg-info text-white">
                                            <div class="stat-icon">
                                                <i class="bi bi-currency-dollar"></i>
                                            </div>
                                            <div class="stat-content">
                                                <div class="stat-value">${{ number_format($producto->precio, 2) }}</div>
                                                <div class="stat-label">Precio</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Estado del Producto -->
                                <div class="product-status mb-3">
                                    @if($producto->trashed())
                                        <span class="badge bg-danger fs-6">
                                            <i class="bi bi-exclamation-triangle me-1"></i> Eliminado
                                        </span>
                                    @else
                                        <span class="badge bg-success fs-6">
                                            <i class="bi bi-check-circle me-1"></i> Activo
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Panel Lateral -->
        <div class="col-lg-4">
            <!-- Información de Auditoría -->
            <div class="card card-outline card-secondary shadow-lg mb-4">
                <div class="card-header bg-gradient-secondary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock-history me-2"></i> Información de Auditoría
                    </h5>
                </div>
                <div class="card-body">
                    <div class="audit-info">
                        <div class="audit-item">
                            <span class="audit-label"><i class="bi bi-person-plus me-1"></i> Creado por:</span>
                            <span class="audit-value">
                                @if($producto->creador)
                                    {{ $producto->creador->name }} 
                                    @if($producto->creador->roles && $producto->creador->roles->count() > 0)
                                        ({{ $producto->creador->roles->first()->name }})
                                    @else
                                        (Sin rol)
                                    @endif
                                @else
                                    Sistema (Sin rol)
                                @endif
                            </span>
                        </div>
                        <div class="audit-item">
                            <span class="audit-label"><i class="bi bi-calendar-plus me-1"></i> Fecha de creación:</span>
                            <span class="audit-value">{{ $producto->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @if($producto->updated_by)
                            <div class="audit-item">
                                <span class="audit-label"><i class="bi bi-person-check me-1"></i> Última modificación:</span>
                                <span class="audit-value">
                                    @if($producto->modificador)
                                        {{ $producto->modificador->name }} 
                                        @if($producto->modificador->roles && $producto->modificador->roles->count() > 0)
                                            ({{ $producto->modificador->roles->first()->name }})
                                        @else
                                            (Sin rol)
                                        @endif
                                    @else
                                        Sistema (Sin rol)
                                    @endif
                                </span>
                            </div>
                        @endif
                        <div class="audit-item">
                            <span class="audit-label"><i class="bi bi-calendar-check me-1"></i> Última actualización:</span>
                            <span class="audit-value">{{ $producto->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @if($producto->deleted_at)
                            <div class="audit-item">
                                <span class="audit-label"><i class="bi bi-calendar-x me-1"></i> Eliminado el:</span>
                                <span class="audit-value">{{ $producto->deleted_at->format('d/m/Y H:i') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Acciones Rápidas -->
            <div class="card card-outline card-warning shadow-lg">
                <div class="card-header bg-gradient-warning text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lightning me-2"></i> Acciones Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if(!$producto->trashed())
                            <a href="{{ route('productos.edit', $producto) }}" class="btn btn-warning btn-lg">
                                <i class="bi bi-pencil-square me-2"></i> Editar Producto
                            </a>
                            <button type="button" class="btn btn-danger btn-lg" data-bs-toggle="modal" data-bs-target="#modalEliminarProducto{{ $producto->id }}">
                                <i class="bi bi-trash me-2"></i> Eliminar Producto
                            </button>
                        @else
                            <button type="button" class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#modalRestaurarProducto{{ $producto->id }}">
                                <i class="bi bi-arrow-clockwise me-2"></i> Restaurar Producto
                            </button>
                            <button type="button" class="btn btn-danger btn-lg" data-bs-toggle="modal" data-bs-target="#modalBorrarDefinitivoProducto{{ $producto->id }}">
                                <i class="bi bi-trash-fill me-2"></i> Eliminar Definitivamente
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Eliminar Producto -->
@if(!$producto->trashed())
<div class="modal fade" id="modalEliminarProducto{{ $producto->id }}" tabindex="-1" aria-labelledby="modalEliminarProductoLabel{{ $producto->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg border-0">
            <form method="POST" action="{{ route('productos.destroy', $producto) }}" class="modal-form" data-modal-id="modalEliminarProducto{{ $producto->id }}">
                @csrf
                @method('DELETE')
                <div class="modal-header bg-danger text-white align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-trash display-5"></i>
                        <h5 class="modal-title mb-0" id="modalEliminarProductoLabel{{ $producto->id }}">Eliminar Producto</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    @if ($errors->any() && session('modal') == 'eliminar-'. $producto->id)
                        <div class="alert alert-danger border-0 shadow-sm mb-4 animate__animated animate__fadeInDown">
                            <div class="d-flex align-items-center gap-3">
                                <i class="bi bi-exclamation-triangle-fill fs-1 text-danger"></i>
                                <div>
                                    <h6 class="alert-heading mb-1"><strong>Errores de Validación</strong></h6>
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="alert alert-danger border-0 shadow-sm mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-exclamation-triangle-fill fs-1 text-danger"></i>
                            <div>
                                <h6 class="alert-heading mb-1"><strong>¡ACCIÓN IRREVERSIBLE!</strong></h6>
                                <p class="mb-0">Esta acción eliminará temporalmente el producto del sistema. Podrás restaurarlo más tarde desde la sección de eliminados.</p>
                            </div>
                        </div>
                    </div>
                    <div class="card border-0 bg-light mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Información del Producto</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="row align-items-center">
                                <div class="col-md-4 text-center">
                                    <img src="{{ $producto->imagen ? asset('storage/productos/' . $producto->imagen) : asset('img/default-150x150.png') }}" class="img-fluid rounded shadow-sm" style="max-width: 100px; max-height: 100px; object-fit: contain;">
                                </div>
                                <div class="col-md-8">
                                    <div class="mb-2">
                                        <span class="form-label text-muted small">Nombre</span>
                                        <span class="fw-bold d-block">{{ $producto->nombre }}</span>
                                    </div>
                                    <div class="mb-2">
                                        <span class="form-label text-muted small">Categoría</span>
                                        <span class="d-block">{{ $producto->categoria->nombre ?? 'Sin categoría' }}</span>
                                    </div>
                                    <div class="mb-2">
                                        <span class="form-label text-muted small">Stock</span>
                                        <span class="d-block">{{ $producto->stock }}</span>
                                    </div>
                                    <div class="mb-2">
                                        <span class="form-label text-muted small">Precio</span>
                                        <span class="d-block">${{ number_format($producto->precio, 2) }}</span>
                                    </div>
                                    <div class="mb-2">
                                        <span class="form-label text-muted small">Estado</span>
                                        <span class="badge bg-success">Activo</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="mb-3 fw-bold">¿Estás seguro que deseas eliminar este producto?</p>
                    <div class="mb-3 input-group">
                        <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Contraseña de administrador" required autocomplete="off" value="{{ old('password') }}">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3 input-group">
                        <span class="input-group-text"><i class="bi bi-chat-left-text"></i></span>
                        <select name="observacion" class="form-select @error('observacion') is-invalid @enderror" required>
                            <option value="">Seleccionar motivo de eliminación</option>
                            <option value="Producto descontinuado" {{ old('observacion') == 'Producto descontinuado' ? 'selected' : '' }}>Producto descontinuado</option>
                            <option value="Stock agotado permanentemente" {{ old('observacion') == 'Stock agotado permanentemente' ? 'selected' : '' }}>Stock agotado permanentemente</option>
                            <option value="Cambio de proveedor" {{ old('observacion') == 'Cambio de proveedor' ? 'selected' : '' }}>Cambio de proveedor</option>
                            <option value="Producto defectuoso" {{ old('observacion') == 'Producto defectuoso' ? 'selected' : '' }}>Producto defectuoso</option>
                            <option value="Precio no competitivo" {{ old('observacion') == 'Precio no competitivo' ? 'selected' : '' }}>Precio no competitivo</option>
                            <option value="Baja demanda" {{ old('observacion') == 'Baja demanda' ? 'selected' : '' }}>Baja demanda</option>
                            <option value="Error en el sistema" {{ old('observacion') == 'Error en el sistema' ? 'selected' : '' }}>Error en el sistema</option>
                            <option value="Otro" {{ old('observacion') == 'Otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                        @error('observacion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i> Cancelar</button>
                    <button type="submit" class="btn btn-danger btn-lg"><i class="bi bi-trash"></i> Eliminar Producto</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Modal Restaurar Producto -->
@if($producto->trashed())
<div class="modal fade" id="modalRestaurarProducto{{ $producto->id }}" tabindex="-1" aria-labelledby="modalRestaurarProductoLabel{{ $producto->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg border-0">
            <form method="POST" action="{{ route('productos.restore', $producto->id) }}" class="modal-form" data-modal-id="modalRestaurarProducto{{ $producto->id }}">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalRestaurarProductoLabel{{ $producto->id }}">
                        <i class="bi bi-arrow-clockwise me-2"></i> Restaurar Producto
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <i class="bi bi-arrow-clockwise text-success" style="font-size: 3rem;"></i>
                        <h4 class="mt-3">¿Restaurar producto?</h4>
                        <p class="text-muted">Estás a punto de restaurar el producto <strong>"{{ $producto->nombre }}"</strong></p>
                        <p class="text-muted small">Este producto volverá a estar disponible en el sistema.</p>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="password" class="form-label fw-bold">
                                    <i class="bi bi-key me-1 text-success"></i> Contraseña <span class="text-danger">*</span>
                                </label>
                                <input type="password" name="password" id="password" class="form-control form-control-lg @error('password') is-invalid @enderror" required>
                                @error('password')<div class="invalid-feedback"><i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="observacion" class="form-label fw-bold">
                                    <i class="bi bi-chat-text me-1 text-success"></i> Observación <span class="text-danger">*</span>
                                </label>
                                <select name="observacion" id="observacion" class="form-select form-select-lg @error('observacion') is-invalid @enderror" required>
                                    <option value="">Selecciona una razón</option>
                                    <option value="Producto disponible nuevamente">Producto disponible nuevamente</option>
                                    <option value="Error en la eliminación">Error en la eliminación</option>
                                    <option value="Nuevo proveedor disponible">Nuevo proveedor disponible</option>
                                    <option value="Demanda del producto">Demanda del producto</option>
                                    <option value="Otro">Otro</option>
                                </select>
                                @error('observacion')<div class="invalid-feedback"><i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="bi bi-arrow-clockwise me-1"></i> Restaurar Producto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Borrar Definitivamente -->
<div class="modal fade" id="modalBorrarDefinitivoProducto{{ $producto->id }}" tabindex="-1" aria-labelledby="modalBorrarDefinitivoProductoLabel{{ $producto->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg border-0">
            <form method="POST" action="{{ route('productos.forceDelete', $producto->id) }}" class="modal-form" data-modal-id="modalBorrarDefinitivoProducto{{ $producto->id }}">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="modalBorrarDefinitivoProductoLabel{{ $producto->id }}">
                        <i class="bi bi-exclamation-triangle me-2"></i> Eliminar Definitivamente
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <i class="bi bi-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                        <h4 class="mt-3 text-danger">¡ADVERTENCIA!</h4>
                        <p class="text-danger fw-bold">Estás a punto de eliminar PERMANENTEMENTE el producto <strong>"{{ $producto->nombre }}"</strong></p>
                        <p class="text-muted small">Esta acción NO se puede deshacer y eliminará todos los datos asociados.</p>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="password" class="form-label fw-bold">
                                    <i class="bi bi-key me-1 text-danger"></i> Contraseña <span class="text-danger">*</span>
                                </label>
                                <input type="password" name="password" id="password" class="form-control form-control-lg @error('password') is-invalid @enderror" required>
                                @error('password')<div class="invalid-feedback"><i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="observacion" class="form-label fw-bold">
                                    <i class="bi bi-chat-text me-1 text-danger"></i> Observación <span class="text-danger">*</span>
                                </label>
                                <select name="observacion" id="observacion" class="form-select form-select-lg @error('observacion') is-invalid @enderror" required>
                                    <option value="">Selecciona una razón</option>
                                    <option value="Eliminación definitiva por error">Eliminación definitiva por error</option>
                                    <option value="Producto obsoleto">Producto obsoleto</option>
                                    <option value="Limpieza de datos">Limpieza de datos</option>
                                    <option value="Otro">Otro</option>
                                </select>
                                @error('observacion')<div class="invalid-feedback"><i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-danger btn-lg">
                        <i class="bi bi-trash-fill me-1"></i> Eliminar Definitivamente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Toast Container para notificaciones -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>

@push('styles')
<style>
.card-outline.card-primary {
    border-top: 3px solid #007bff;
    border-radius: 12px;
    overflow: hidden;
}
.card-outline.card-secondary {
    border-top: 3px solid #6c757d;
    border-radius: 12px;
    overflow: hidden;
}
.card-outline.card-warning {
    border-top: 3px solid #ffc107;
    border-radius: 12px;
    overflow: hidden;
}
.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}
.bg-gradient-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
}
.bg-gradient-warning {
    background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
}
.product-image-container {
    position: relative;
    display: inline-block;
    margin-bottom: 1.5rem;
}
.product-image {
    transition: transform 0.3s ease;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}
.product-image:hover {
    transform: scale(1.05);
}
.no-image-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 280px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 2px dashed #dee2e6;
    border-radius: 12px;
    transition: all 0.3s ease;
}
.no-image-placeholder:hover {
    border-color: #007bff;
    background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
}
.product-title {
    color: #2c3e50;
    font-weight: 700;
    border-bottom: 3px solid #007bff;
    padding-bottom: 15px;
    margin-bottom: 25px;
    position: relative;
}
.product-title::after {
    content: '';
    position: absolute;
    bottom: -3px;
    left: 0;
    width: 50px;
    height: 3px;
    background: linear-gradient(90deg, #007bff, #0056b3);
    border-radius: 2px;
}
.description-text {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 20px;
    border-radius: 12px;
    border-left: 4px solid #007bff;
    font-style: italic;
    line-height: 1.6;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.info-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 12px 0;
    border-bottom: 1px solid #f0f0f0;
}
.info-item:last-child {
    border-bottom: none;
}
.info-label {
    font-weight: 600;
    color: #495057;
    min-width: 140px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.stat-card {
    display: flex;
    align-items: center;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    transition: all 0.3s ease;
    margin-bottom: 15px;
}
.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(0,0,0,0.2);
}
.stat-icon {
    font-size: 2.5rem;
    margin-right: 20px;
    opacity: 0.9;
}
.stat-value {
    font-size: 1.8rem;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 5px;
}
.stat-label {
    font-size: 0.95rem;
    opacity: 0.9;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.audit-info {
    display: flex;
    flex-direction: column;
    gap: 15px;
}
.audit-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 10px;
    border-left: 4px solid #6c757d;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.audit-item:hover {
    transform: translateX(5px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.audit-label {
    font-weight: 600;
    color: #495057;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 8px;
}
.audit-value {
    color: #6c757d;
    font-size: 0.9rem;
    font-weight: 500;
    text-align: right;
    max-width: 60%;
}
.product-status {
    margin-top: 25px;
    padding: 15px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 10px;
    text-align: center;
}
.breadcrumb {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 12px;
    padding: 1rem 1.5rem;
    border: 1px solid #dee2e6;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}
.breadcrumb-item a {
    color: #007bff;
    text-decoration: none;
    transition: all 0.3s ease;
    font-weight: 500;
}
.breadcrumb-item a:hover {
    color: #0056b3;
    transform: translateY(-1px);
}
.breadcrumb-item.active {
    color: #6c757d;
    font-weight: 600;
}
.card {
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    border: none;
    border-radius: 15px;
    overflow: hidden;
}
.card-header {
    border-bottom: none;
    padding: 1.5rem;
}
.card-body {
    padding: 2rem;
}
.card-footer {
    border-top: none;
    padding: 1.5rem;
}
.btn-lg {
    border-radius: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease-in-out;
    padding: 0.875rem 1.75rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.btn-lg:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
}
.modal-content {
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
}
.modal-header {
    border-bottom: none;
    padding: 1.5rem;
}
.modal-body {
    padding: 2rem;
}
.modal-footer {
    border-top: none;
    padding: 1.5rem;
}
.toast {
    border: none;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}
.toast.success {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    border-left: 4px solid #28a745;
}
.toast.error {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    border-left: 4px solid #dc3545;
}
/* Estilos para modales profesionales */
.modal.fade.animated {
    animation-duration: 0.4s;
}
.fadeInDown {
    animation-name: fadeInDown;
}
@keyframes fadeInDown {
    from { opacity: 0; transform: translate3d(0, -40px, 0); }
    to { opacity: 1; transform: none; }
}
.modal {
    backdrop-filter: blur(8px);
    background: rgba(0, 0, 0, 0.4);
}
.btn-close {
    transition: all 0.3s ease;
}
.btn-close:hover {
    transform: scale(1.1);
}
.modal-body .form-select {
    border-radius: 8px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
    font-size: 0.95rem;
}
.modal-body .form-control {
    border-radius: 8px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
    font-size: 0.95rem;
}
.modal-body .form-control:focus,
.modal-body .form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
@media (max-width: 768px) {
    .card-body {
        padding: 1.5rem;
    }
    .stat-card {
        margin-bottom: 1rem;
        padding: 20px;
    }
    .audit-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    .audit-value {
        text-align: left;
        max-width: 100%;
    }
    .info-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    .info-label {
        min-width: auto;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(function() {
    // Notificación de éxito animada
    window.showSuccessNotification = function(message) {
        $('.alert-success.position-fixed').remove();
        const successHtml = '<div class="alert alert-success alert-dismissible fade show position-fixed animate__animated animate__fadeInDown" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">' +
            '<div class="d-flex align-items-center gap-2">' +
            '<i class="bi bi-check-circle-fill fs-4 text-success"></i>' +
            '<div class="flex-grow-1">' +
            '<strong>¡Éxito!</strong><br>' +
            '<small>' + message + '</small>' +
            '</div>' +
            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
            '</div>';
        $('body').append(successHtml);
        setTimeout(() => {
            $('.alert-success.position-fixed').fadeOut();
        }, 5000);
    };

    // Notificación de error animada
    window.showErrorNotification = function(message) {
        $('.alert-danger.position-fixed').remove();
        const errorHtml = '<div class="alert alert-danger alert-dismissible fade show position-fixed animate__animated animate__fadeInDown" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">' +
            '<div class="d-flex align-items-center gap-2">' +
            '<i class="bi bi-exclamation-triangle-fill fs-4 text-danger"></i>' +
            '<div class="flex-grow-1">' +
            '<strong>¡Error!</strong><br>' +
            '<small>' + message + '</small>' +
            '</div>' +
            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
            '</div>';
        $('body').append(errorHtml);
        setTimeout(() => {
            $('.alert-danger.position-fixed').fadeOut();
        }, 5000);
    };

    // Manejar formularios de modales con AJAX
    $('.modal-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const modalId = form.data('modal-id');
        const modal = $('#' + modalId);
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        // Mostrar loading en el botón
        submitBtn.html('<i class="bi bi-hourglass-split"></i> Procesando...').prop('disabled', true);
        
        $.ajax({
            url: form.attr('action'),
            method: form.attr('method'),
            data: form.serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            success: function(response, status, xhr) {
                if (xhr.status === 422) {
                    // Limpiar errores anteriores
                    modal.find('.is-invalid').removeClass('is-invalid');
                    modal.find('.invalid-feedback').html('');
                    
                    if (response.errors) {
                        let errorMessage = 'Error de validación: ';
                        Object.keys(response.errors).forEach(field => {
                            const input = modal.find('[name="' + field + '"]');
                            if (input.length) {
                                input.addClass('is-invalid');
                                const feedback = input.parent().find('.invalid-feedback');
                                if (feedback.length) {
                                    feedback.html('<i class="bi bi-exclamation-triangle me-1"></i>' + response.errors[field][0]);
                                }
                                // Agregar el primer error al mensaje
                                if (errorMessage === 'Error de validación: ') {
                                    errorMessage += response.errors[field][0];
                                }
                            }
                        });
                        showErrorNotification(errorMessage);
                    } else {
                        showErrorNotification('Error de validación. Por favor, corrige los errores.');
                    }
                } else if (response.success) {
                    modal.modal('hide');
                    showSuccessNotification(response.message);
                    setTimeout(() => {
                        window.location.href = '{{ route("productos.index") }}';
                    }, 2000);
                } else {
                    showErrorNotification(response.message || 'Ha ocurrido un error inesperado.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                if (xhr.status === 422) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        // Limpiar errores anteriores
                        modal.find('.is-invalid').removeClass('is-invalid');
                        modal.find('.invalid-feedback').html('');
                        
                        if (response.errors) {
                            let errorMessage = 'Error de validación: ';
                            Object.keys(response.errors).forEach(field => {
                                const input = modal.find('[name="' + field + '"]');
                                if (input.length) {
                                    input.addClass('is-invalid');
                                    const feedback = input.parent().find('.invalid-feedback');
                                    if (feedback.length) {
                                        feedback.html('<i class="bi bi-exclamation-triangle me-1"></i>' + response.errors[field][0]);
                                    }
                                    // Agregar el primer error al mensaje
                                    if (errorMessage === 'Error de validación: ') {
                                        errorMessage += response.errors[field][0];
                                    }
                                }
                            });
                            showErrorNotification(errorMessage);
                        } else {
                            showErrorNotification('Error de validación. Por favor, corrige los errores.');
                        }
                    } catch (e) {
                        showErrorNotification('Error de validación. Por favor, corrige los errores.');
                    }
                } else if (xhr.status === 500) {
                    showErrorNotification('Error del servidor. Por favor, intenta de nuevo.');
                } else {
                    showErrorNotification('No se pudo conectar con el servidor. Verifica tu conexión.');
                }
            },
            complete: function() {
                submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });

    // Limpiar errores al cambiar campos
    $('.modal-form input, .modal-form select').on('input change', function() {
        $(this).removeClass('is-invalid');
        $(this).parent().find('.invalid-feedback').html('');
    });
});
</script>
@endpush
@endsection 