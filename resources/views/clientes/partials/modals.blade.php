@if(!$cliente->deleted_at)
<!-- Modal Eliminar Cliente -->
<div class="modal fade" id="modalEliminarCliente{{ $cliente->id }}" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bx bx-trash text-danger me-2"></i> Eliminar Cliente
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="{{ route('clientes.destroy', $cliente) }}">
        @csrf
        @method('DELETE')
        <div class="modal-body">
          @if ($errors->any() && session('modal') == 'eliminar-'.$cliente->id)
            <div class="alert alert-danger">
              <strong>Errores de Validación:</strong>
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif
          <p>¿Está seguro que desea eliminar al cliente <strong>{{ $cliente->nombre }}</strong>?</p>
          <p class="text-muted small">Esta acción eliminará temporalmente al cliente. Puede ser restaurado posteriormente.</p>
          <div class="mb-3">
            <label class="form-label">Contraseña de administrador</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="off">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Motivo/Observación</label>
            <textarea name="observacion" class="form-control @error('observacion') is-invalid @enderror" required rows="2"></textarea>
            @error('observacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-danger">
            <i class="bx bx-trash me-1"></i> Eliminar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endif

@if($cliente->deleted_at)
<!-- Modal Restaurar Cliente -->
<div class="modal fade" id="modalRestaurarCliente{{ $cliente->id }}" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bx bx-refresh text-success me-2"></i> Restaurar Cliente
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="{{ route('clientes.restore', $cliente->id) }}">
        @csrf
        <div class="modal-body">
          @if ($errors->any() && session('modal') == 'restaurar-'.$cliente->id)
            <div class="alert alert-danger">
              <strong>Errores de Validación:</strong>
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif
          <p>¿Está seguro que desea restaurar al cliente <strong>{{ $cliente->nombre }}</strong>?</p>
          <p class="text-muted small">El cliente será reactivado y podrá ser gestionado normalmente.</p>
          <div class="mb-3">
            <label class="form-label">Contraseña de administrador</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="off">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Motivo/Observación</label>
            <textarea name="observacion" class="form-control @error('observacion') is-invalid @enderror" required rows="2"></textarea>
            @error('observacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success">
            <i class="bx bx-refresh me-1"></i> Restaurar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Modal Eliminar Definitivamente Cliente -->
<div class="modal fade" id="modalEliminarDefinitivoCliente{{ $cliente->id }}" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bx bx-x-circle text-danger me-2"></i> Eliminar Definitivamente
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="{{ route('clientes.force-delete', $cliente->id) }}">
        @csrf
        @method('DELETE')
        <div class="modal-body">
          @if ($errors->any() && session('modal') == 'borrar-definitivo-'.$cliente->id)
            <div class="alert alert-danger">
              <strong>Errores de Validación:</strong>
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif
          <div class="alert alert-danger">
            <i class="bx bx-error-circle me-2"></i>
            <strong>¡Advertencia!</strong> Esta acción no se puede deshacer.
          </div>
          <p>¿Está seguro que desea eliminar definitivamente al cliente <strong>{{ $cliente->nombre }}</strong>?</p>
          <p class="text-muted small">Todos los datos asociados serán eliminados permanentemente.</p>
          <div class="mb-3">
            <label class="form-label">Contraseña de administrador</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="off">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Motivo/Observación</label>
            <textarea name="observacion" class="form-control @error('observacion') is-invalid @enderror" required rows="2"></textarea>
            @error('observacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-danger">
            <i class="bx bx-x-circle me-1"></i> Eliminar Definitivamente
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endif 