{{-- Modal Eliminar Producto --}}
<div class="modal fade" id="modalEliminarProducto{{ $producto->id }}" tabindex="-1" aria-labelledby="modalEliminarProductoLabel{{ $producto->id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <form method="POST" action="{{ route('productos.destroy', $producto) }}" autocomplete="off">
        @csrf
        @method('DELETE')
        <div class="modal-header bg-danger text-white align-items-center">
          <div class="d-flex align-items-center gap-3 w-100">
            <div class="avatar avatar-lg me-2">
              <img src="{{ $producto->imagen ? asset('storage/productos/' . $producto->imagen) : asset('img/default-150x150.png') }}" class="rounded-circle border border-3 border-white shadow" style="width: 60px; height: 60px; object-fit: cover; background: #fff;">
            </div>
            <div class="flex-grow-1">
              <h5 class="modal-title mb-0" id="modalEliminarProductoLabel{{ $producto->id }}">Eliminar Producto</h5>
              <span class="fw-light small">ID: #{{ $producto->id }}</span>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
        </div>
        <div class="modal-body">
          <div class="alert alert-danger border-0 mb-4">
            <div class="d-flex align-items-center">
              <i class="bi bi-exclamation-triangle-fill fs-1 text-danger me-3"></i>
              <div>
                <h6 class="alert-heading mb-1"><strong>¡Advertencia!</strong></h6>
                <p class="mb-0">Esta acción eliminará temporalmente el producto. Podrás restaurarlo más tarde desde la sección de eliminados.</p>
              </div>
            </div>
          </div>
          <div class="mb-3 p-2 bg-light rounded border">
            <strong>Nombre:</strong> {{ $producto->nombre }}<br>
            <strong>Categoría:</strong> {{ $producto->categoria->nombre ?? 'Sin categoría' }}<br>
            <strong>Stock:</strong> {{ $producto->stock }}<br>
            <strong>Precio:</strong> ${{ number_format($producto->precio, 2) }}
          </div>
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label fw-bold"><i class="bi bi-key me-1 text-danger"></i> Contraseña de Administrador <span class="text-danger">*</span></label>
              <input type="password" name="password" class="form-control" placeholder="Ingrese su contraseña" required autocomplete="off">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold"><i class="bi bi-chat-text me-1 text-danger"></i> Motivo de Eliminación <span class="text-danger">*</span></label>
              <select name="observacion" class="form-select" required>
                <option value="">Seleccionar motivo</option>
                <option value="Producto descontinuado">Producto descontinuado</option>
                <option value="Stock agotado permanentemente">Stock agotado permanentemente</option>
                <option value="Cambio de proveedor">Cambio de proveedor</option>
                <option value="Producto defectuoso">Producto defectuoso</option>
                <option value="Precio no competitivo">Precio no competitivo</option>
                <option value="Baja demanda">Baja demanda</option>
                <option value="Error en el sistema">Error en el sistema</option>
                <option value="Otro">Otro</option>
              </select>
            </div>
          </div>
          <div class="mb-4">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="confirmEliminarProducto{{ $producto->id }}" required>
              <label class="form-check-label" for="confirmEliminarProducto{{ $producto->id }}">
                <strong>Confirmo que deseo eliminar este producto</strong>
              </label>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i> Cancelar</button>
          <button type="submit" class="btn btn-danger" id="btnEliminarProducto{{ $producto->id }}" disabled><i class="bi bi-trash"></i> Eliminar Producto</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Modal Restaurar Producto --}}
<div class="modal fade" id="modalRestaurarProducto{{ $producto->id }}" tabindex="-1" aria-labelledby="modalRestaurarProductoLabel{{ $producto->id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" action="{{ route('productos.restore', $producto->id) }}" autocomplete="off">
        @csrf
        <div class="modal-header bg-success text-white align-items-center">
          <div class="d-flex align-items-center gap-3 w-100">
            <div class="avatar avatar-lg me-2">
              <img src="{{ $producto->imagen ? asset('storage/productos/' . $producto->imagen) : asset('img/default-150x150.png') }}" class="rounded-circle border border-3 border-white shadow" style="width: 60px; height: 60px; object-fit: cover; background: #fff;">
            </div>
            <div class="flex-grow-1">
              <h5 class="modal-title mb-0" id="modalRestaurarProductoLabel{{ $producto->id }}">Restaurar Producto</h5>
              <span class="fw-light small">ID: #{{ $producto->id }}</span>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
        </div>
        <div class="modal-body">
          <div class="alert alert-success border-0 mb-4">
            <div class="d-flex align-items-center">
              <i class="bi bi-check-circle fs-1 text-success me-3"></i>
              <div>
                <h6 class="alert-heading mb-1"><strong>Restaurar Producto</strong></h6>
                <p class="mb-0">Esta acción restaurará el producto y todos sus datos asociados.</p>
              </div>
            </div>
          </div>
          <div class="mb-3 p-2 bg-light rounded border">
            <strong>Nombre:</strong> {{ $producto->nombre }}<br>
            <strong>Categoría:</strong> {{ $producto->categoria->nombre ?? 'Sin categoría' }}<br>
            <strong>Stock:</strong> {{ $producto->stock }}<br>
            <strong>Precio:</strong> ${{ number_format($producto->precio, 2) }}
          </div>
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label fw-bold"><i class="bi bi-key me-1 text-success"></i> Contraseña de Administrador <span class="text-danger">*</span></label>
              <input type="password" name="password" class="form-control" placeholder="Ingrese su contraseña" required autocomplete="off">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold"><i class="bi bi-chat-text me-1 text-success"></i> Motivo de Restauración <span class="text-danger">*</span></label>
              <select name="observacion" class="form-select" required>
                <option value="">Seleccionar motivo</option>
                <option value="Producto disponible nuevamente">Producto disponible nuevamente</option>
                <option value="Error en la eliminación">Error en la eliminación</option>
                <option value="Nuevo proveedor disponible">Nuevo proveedor disponible</option>
                <option value="Demanda del producto">Demanda del producto</option>
                <option value="Otro">Otro</option>
              </select>
            </div>
          </div>
          <div class="mb-4">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="confirmRestaurarProducto{{ $producto->id }}" required>
              <label class="form-check-label" for="confirmRestaurarProducto{{ $producto->id }}">
                <strong>Confirmo que deseo restaurar este producto</strong>
              </label>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i> Cancelar</button>
          <button type="submit" class="btn btn-success" id="btnRestaurarProducto{{ $producto->id }}" disabled><i class="bi bi-arrow-clockwise"></i> Restaurar Producto</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Modal Borrar Definitivo Producto --}}
<div class="modal fade" id="modalBorrarDefinitivoProducto{{ $producto->id }}" tabindex="-1" aria-labelledby="modalBorrarDefinitivoProductoLabel{{ $producto->id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <form method="POST" action="{{ route('productos.forceDelete', $producto->id) }}" autocomplete="off">
        @csrf
        <div class="modal-header bg-dark text-white align-items-center">
          <div class="d-flex align-items-center gap-3 w-100">
            <div class="avatar avatar-lg me-2">
              <img src="{{ $producto->imagen ? asset('storage/productos/' . $producto->imagen) : asset('img/default-150x150.png') }}" class="rounded-circle border border-3 border-white shadow" style="width: 60px; height: 60px; object-fit: cover; background: #fff;">
            </div>
            <div class="flex-grow-1">
              <h5 class="modal-title mb-0" id="modalBorrarDefinitivoProductoLabel{{ $producto->id }}">Eliminar Definitivamente</h5>
              <span class="fw-light small">ID: #{{ $producto->id }}</span>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
        </div>
        <div class="modal-body">
          <div class="alert alert-dark border-0 mb-4">
            <div class="d-flex align-items-center">
              <i class="bi bi-exclamation-triangle-fill fs-1 text-danger me-3"></i>
              <div>
                <h6 class="alert-heading mb-1"><strong>¡ACCIÓN IRREVERSIBLE!</strong></h6>
                <p class="mb-0">Esta acción eliminará permanentemente el producto y todos sus datos. Esta operación no se puede deshacer.</p>
              </div>
            </div>
          </div>
          <div class="mb-3 p-2 bg-light rounded border">
            <strong>Nombre:</strong> {{ $producto->nombre }}<br>
            <strong>Categoría:</strong> {{ $producto->categoria->nombre ?? 'Sin categoría' }}<br>
            <strong>Stock:</strong> {{ $producto->stock }}<br>
            <strong>Precio:</strong> ${{ number_format($producto->precio, 2) }}
          </div>
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label fw-bold"><i class="bi bi-key me-1 text-dark"></i> Contraseña de Administrador <span class="text-danger">*</span></label>
              <input type="password" name="password" class="form-control" placeholder="Ingrese su contraseña" required autocomplete="off">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold"><i class="bi bi-chat-text me-1 text-dark"></i> Motivo de Eliminación Definitiva <span class="text-danger">*</span></label>
              <select name="observacion" class="form-select" required>
                <option value="">Seleccionar motivo</option>
                <option value="Producto obsoleto">Producto obsoleto</option>
                <option value="Error en el sistema">Error en el sistema</option>
                <option value="Duplicado en el sistema">Duplicado en el sistema</option>
                <option value="Problemas de seguridad">Problemas de seguridad</option>
                <option value="Limpieza de base de datos">Limpieza de base de datos</option>
                <option value="Otro">Otro</option>
              </select>
            </div>
          </div>
          <div class="mb-4">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="confirmBorrarDefinitivoProducto{{ $producto->id }}" required>
              <label class="form-check-label" for="confirmBorrarDefinitivoProducto{{ $producto->id }}">
                <strong>Confirmo que deseo eliminar este producto permanentemente</strong>
              </label>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i> Cancelar</button>
          <button type="submit" class="btn btn-dark" id="btnBorrarDefinitivoProducto{{ $producto->id }}" disabled><i class="bi bi-x-circle"></i> Eliminar Definitivamente</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('styles')
<style>
.alert-danger {
  background: linear-gradient(135deg, #ff3b3b 0%, #b80000 100%) !important;
  color: #fff !important;
  border: none !important;
}
.alert-danger .btn-close {
  filter: invert(1);
}
</style>
@endpush

@push('scripts')
<script>
// Habilitar el botón solo si todos los campos están completos y el checkbox está marcado
function validarModalProducto(id, tipo) {
  let pass = document.querySelector(`#modal${tipo}Producto${id} input[name='password']`);
  let obs = document.querySelector(`#modal${tipo}Producto${id} select[name='observacion']`);
  let check = document.querySelector(`#modal${tipo}Producto${id} input[type='checkbox']`);
  let btn = document.getElementById(`btn${tipo}Producto${id}`);
  if (btn) {
    btn.disabled = !(pass && pass.value && obs && obs.value && check && check.checked);
  }
}

// Para cada modal, agregar listeners
@foreach($productos as $producto)
  ['Eliminar','Restaurar','BorrarDefinitivo'].forEach(function(tipo) {
    let modalId = `modal${tipo}Producto{{ $producto->id }}`;
    let modal = document.getElementById(modalId);
    if (modal) {
      modal.addEventListener('input', function() {
        validarModalProducto({{ $producto->id }}, tipo);
      });
      modal.addEventListener('change', function() {
        validarModalProducto({{ $producto->id }}, tipo);
      });
      // Reset al abrir
      modal.addEventListener('show.bs.modal', function() {
        setTimeout(() => validarModalProducto({{ $producto->id }}, tipo), 100);
      });
    }
  });
@endforeach
</script>
@endpush 