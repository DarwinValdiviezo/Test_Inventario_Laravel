@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-md-7">
      <div class="card shadow-sm">
        <div class="card-body">
          <h4 class="mb-4">Gestión de usuarios</h4>
          <form method="GET" action="{{ route('dashboard') }}" class="row g-2 align-items-end mb-4">
            <div class="col-9">
              <label for="usuario_id" class="form-label">Selecciona un usuario</label>
              <select name="usuario_id" id="usuario_id" class="form-select" required>
                <option value="">-- Selecciona --</option>
                @foreach(\App\Models\User::orderBy('name')->get() as $user)
                  <option value="{{ $user->id }}" {{ request('usuario_id') == $user->id ? 'selected' : '' }}>
                    {{ $user->name }} ({{ $user->email }})
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-3">
              <button type="submit" class="btn btn-primary w-100">Ver</button>
            </div>
          </form>

          @if(request('usuario_id'))
            @php 
              $user = \App\Models\User::with('roles')->find(request('usuario_id'));
              $tokens = $user ? $user->tokens()->orderByDesc('created_at')->get() : collect();
            @endphp
            @if($user)
              <div class="mb-4">
                <ul class="list-group user-list-group">
                  <li class="list-group-item"><b>ID:</b> {{ $user->id }}</li>
                  <li class="list-group-item"><b>Nombre:</b> {{ $user->name }}</li>
                  <li class="list-group-item"><b>Email:</b> {{ $user->email }}</li>
                  <li class="list-group-item"><b>Estado:</b> <span class="badge bg-{{ $user->estado == 'activo' ? 'success' : 'secondary' }}">{{ $user->estado }}</span></li>
                  <li class="list-group-item"><b>Roles:</b> {{ $user->roles->pluck('name')->join(', ') }}</li>
                </ul>
              </div>
              <form method="POST" action="{{ route('users.crearToken') }}" class="row g-2 align-items-end mb-4">
                @csrf
                <input type="hidden" name="usuario_id" value="{{ $user->id }}">
                <div class="col-8">
                  <label for="token_name" class="form-label">Nombre del token</label>
                  <input type="text" name="token_name" id="token_name" class="form-control" required>
                </div>
                <div class="col-4">
                  <button type="submit" class="btn btn-success w-100">Crear token</button>
                </div>
              </form>
              @if(session('token'))
                <div class="alert alert-success mt-3">
                  <strong>Token generado (guárdalo, solo se muestra una vez):</strong><br>
                  <code id="token-generado" style="word-break: break-all;">{{ session('token') }}</code>
                </div>
              @endif
              {{-- Tabla de tokens activos del usuario --}}
              <div class="mt-5">
                <h5 class="mb-3">Tokens activos de este usuario</h5>
                <div class="table-responsive">
                  <table class="table table-bordered table-sm align-middle">
                    <thead class="table-light">
                      <tr>
                        <th>Nombre</th>
                        <th>Token</th>
                        <th>Creado</th>
                        <th>Último uso</th>
                        <th>Expira</th>
                        <th>Acciones</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse($tokens as $token)
                        <tr>
                          <td>{{ $token->name }}</td>
                          <td style="max-width: 300px; word-break: break-all;"><code>{{ $token->id }}|{{ $token->token }}</code></td>
                          <td>{{ $token->created_at->format('Y-m-d H:i') }}</td>
                          <td>{{ $token->last_used_at ? $token->last_used_at->format('Y-m-d H:i') : '-' }}</td>
                          <td>{{ $token->expires_at ? $token->expires_at->format('Y-m-d H:i') : '-' }}</td>
                          <td><!-- Aquí podrías poner botón para revocar/eliminar -->
                            <button type="button" class="btn btn-sm btn-outline-primary crear-token-ajax" data-token-name="{{ $token->name }}">Ver token</button>
                            <button type="button" class="btn btn-sm btn-outline-warning regenerar-token-ajax" data-token-id="{{ $token->id }}">Regenerar token</button>
                          </td>
                        </tr>
                      @empty
                        <tr><td colspan="6" class="text-center">Sin tokens</td></tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>
              </div>
              {{-- Tabla de auditoría de tokens generados --}}
              @php
                $auditorias = \App\Models\Auditoria::where('action', 'crear_token')->orderByDesc('created_at')->limit(10)->get();
              @endphp
              <div class="mt-5">
                <h5 class="mb-3">Auditoría de tokens generados</h5>
                <div class="table-responsive">
                  <table class="table table-bordered table-sm align-middle">
                    <thead class="table-light">
                      <tr>
                        <th>Fecha</th>
                        <th>Generado por</th>
                        <th>Para usuario</th>
                        <th>Nombre del token</th>
                        <th>IP</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse($auditorias as $a)
                        <tr>
                          <td>{{ $a->created_at->format('Y-m-d H:i') }}</td>
                          <td>{{ optional($a->user)->name ?? 'N/A' }}</td>
                          <td>
                            @php
                              $desc = $a->description;
                              preg_match('/usuario (.*?) \((.*?)\)/', $desc, $matches);
                            @endphp
                            {{ $matches[1] ?? '-' }}<br><small>{{ $matches[2] ?? '' }}</small>
                          </td>
                          <td>{{ $a->observacion ? str_replace('Nombre del token: ', '', $a->observacion) : '-' }}</td>
                          <td>{{ $a->ip_address }}</td>
                        </tr>
                      @empty
                        <tr><td colspan="5" class="text-center">Sin registros</td></tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>
              </div>
            @else
              <div class="alert alert-warning">Usuario no encontrado.</div>
            @endif
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Modal para mostrar el token generado/regenerado -->
<div class="modal fade" id="modalToken" tabindex="-1" aria-labelledby="modalTokenLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTokenLabel">Token generado</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body text-center">
        <div class="mb-3">
          <code id="modal-token-value" style="word-break: break-all; font-size: 1.1rem; background: #f8fafc; padding: 0.7rem 1rem; border-radius: 0.7rem; display: inline-block;"></code>
        </div>
        <button type="button" class="btn btn-outline-primary" id="btn-copiar-token">Copiar token</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
.card {
  border-radius: 1.2rem;
  box-shadow: 0 4px 24px 0 rgba(80,80,120,0.08);
}
.card-body {
  background: #f8fafc;
  border-radius: 1.2rem;
}
.user-list-group {
  border-radius: 1rem;
  box-shadow: 0 2px 8px 0 rgba(80,80,120,0.06);
  overflow: hidden;
}
.user-list-group .list-group-item {
  font-size: 1.08rem;
  border: none;
  border-bottom: 1px solid #e9ecef;
  background: #fff;
  padding: 1rem 1.2rem;
}
.user-list-group .list-group-item:last-child {
  border-bottom: none;
}
.btn-success, .btn-primary {
  font-weight: 500;
  letter-spacing: 0.5px;
  border-radius: 0.7rem;
  box-shadow: 0 2px 8px 0 rgba(80,200,120,0.08);
}
input.form-control, select.form-select {
  border-radius: 0.7rem;
  box-shadow: 0 1px 4px 0 rgba(80,80,120,0.04);
}
label.form-label {
  font-weight: 500;
  color: #495057;
}
@media (max-width: 768px) {
  .card-body { padding: 1.2rem !important; }
  .user-list-group .list-group-item { padding: 0.7rem 0.7rem; }
}
/* Modal personalizado */
#modalToken .modal-content {
  border-radius: 1.2rem;
  box-shadow: 0 4px 24px 0 rgba(80,80,120,0.13);
}
#modalToken .modal-header {
  border-bottom: none;
}
#modalToken .modal-title {
  font-weight: 600;
  color: #3b3b5c;
}
#modalToken .btn-close {
  background: none;
  border: none;
}
</style>
@endpush

@push('scripts')
<script>
document.querySelectorAll('.toggle-token').forEach(function(btn) {
  btn.addEventListener('click', function() {
    const td = btn.closest('tr').querySelector('td:nth-child(2)');
    const oculto = td.querySelector('.token-oculto');
    const mostrado = td.querySelector('.token-mostrado');
    if (oculto.style.display === 'none') {
      oculto.style.display = 'inline';
      mostrado.style.display = 'none';
      btn.textContent = '🙈';
    } else {
      oculto.style.display = 'none';
      mostrado.style.display = 'inline';
      btn.textContent = '👁️';
    }
  });
});

// AJAX para crear y mostrar token

document.querySelectorAll('.crear-token-ajax').forEach(function(btn) {
  btn.addEventListener('click', function() {
    const tokenName = btn.getAttribute('data-token-name');
    fetch("{{ route('tokens.create') }}", {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Accept': 'application/json'
      },
      body: JSON.stringify({ token_name: tokenName })
    })
    .then(res => res.json())
    .then(data => {
      if (data.token) {
        mostrarModalToken(data.token);
      } else {
        alert('No se pudo generar el token.');
      }
    });
  });
});

// AJAX para regenerar token

document.querySelectorAll('.regenerar-token-ajax').forEach(function(btn) {
  btn.addEventListener('click', function() {
    if (!confirm('¿Seguro que deseas regenerar este token? El anterior dejará de funcionar.')) return;
    const tokenId = btn.getAttribute('data-token-id');
    fetch("{{ route('tokens.regenerate') }}", {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content'),
        'Accept': 'application/json'
      },
      body: JSON.stringify({ token_id: tokenId })
    })
    .then(res => res.json())
    .then(data => {
      if (data.token) {
        mostrarModalToken(data.token);
        setTimeout(() => { location.reload(); }, 2000);
      } else {
        alert('No se pudo regenerar el token.');
      }
    });
  });
});

// Mostrar token en modal bonito y copiar
function mostrarModalToken(token) {
  document.getElementById('modal-token-value').textContent = token;
  var modal = new bootstrap.Modal(document.getElementById('modalToken'));
  modal.show();
  document.getElementById('btn-copiar-token').onclick = function() {
    navigator.clipboard.writeText(token);
    this.textContent = '¡Copiado!';
    setTimeout(() => { this.textContent = 'Copiar token'; }, 1500);
  };
}
</script>
@endpush

