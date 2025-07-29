@extends('layouts.app')
@section('title', 'Dashboard Cliente')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row justify-content-center mb-4">
    <div class="col-lg-8">
      <div class="card shadow-lg border-0 rounded-4 animate__animated animate__fadeInDown mb-4">
        <div class="card-body text-center">
          <h2 class="fw-bold mb-2" style="color: #1976d2;">¡Bienvenido, {{ Auth::user()->name }}!</h2>
          <p class="text-muted mb-3">Este es tu panel de cliente. Aquí podrás ver tus compras, facturas y novedades.</p>
        </div>
      </div>
      <!-- Generar y copiar token API -->
      <div class="card shadow-sm border-0 rounded-3 text-center p-4 mb-4 animate__animated animate__fadeIn">
        <h5 class="fw-bold mb-2">Token de API personal</h5>
        <form id="form-token" method="POST" action="{{ route('cliente.generar_token') }}">
          @csrf
          <input type="hidden" name="token_name" value="Token Cliente">
          <button type="submit" class="btn btn-primary">Generar Token API</button>
        </form>
        @if(session('token'))
          <div class="alert alert-success mt-3">
            <strong>Token generado:</strong>
            <input type="text" id="api-token" class="form-control" value="{{ session('token') }}" readonly>
            <button class="btn btn-secondary mt-2" onclick="copiarToken()">Copiar Token</button>
          </div>
          <script>
            function copiarToken() {
              var copyText = document.getElementById("api-token");
              copyText.select();
              copyText.setSelectionRange(0, 99999);
              document.execCommand("copy");
              alert("¡Token copiado!");
            }
          </script>
        @endif
      </div>
      <!-- Tabla de facturas recientes del cliente -->
      <div class="card shadow-sm border-0 rounded-3 p-4 mb-4 animate__animated animate__fadeIn">
        <h5 class="fw-bold mb-3">Tus últimas facturas</h5>
        @if($facturasRecientes->count())
          <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Fecha</th>
                  <th>Total</th>
                  <th>Estado</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                @foreach($facturasRecientes as $factura)
                  <tr>
                    <td>{{ $factura->getNumeroFormateado() }}</td>
                    <td>{{ $factura->created_at->format('Y-m-d H:i') }}</td>
                    <td>$ {{ number_format($factura->total, 2) }}</td>
                    <td>
                      <span class="badge bg-{{ $factura->estado === 'activa' ? 'success' : 'secondary' }}">
                        {{ ucfirst($factura->estado) }}
                      </span>
                    </td>
                    <td>
                      <a href="{{ route('facturas.show', $factura->id) }}" class="btn btn-sm btn-outline-primary" title="Ver detalle">
                        <i class="bx bx-search"></i> Ver
                      </a>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div class="alert alert-info mb-0">No tienes facturas registradas aún.</div>
        @endif
      </div>
      <div class="row g-3 mb-4">
        <div class="col-md-4">
          <div class="card shadow-sm border-0 rounded-3 text-center p-4 animate__animated animate__fadeInLeft">
            <div class="mb-2"><i class="bx bx-cart-alt fs-1 text-primary"></i></div>
            <div class="fw-bold fs-4">{{ $comprasCliente ?? 0 }}</div>
            <div class="text-muted">Compras realizadas</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card shadow-sm border-0 rounded-3 text-center p-4 animate__animated animate__fadeInUp">
            <div class="mb-2"><i class="bx bx-file fs-1 text-success"></i></div>
            <div class="fw-bold fs-4">{{ $facturasCliente ?? 0 }}</div>
            <div class="text-muted">Facturas</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card shadow-sm border-0 rounded-3 text-center p-4 animate__animated animate__fadeInRight">
            <div class="mb-2"><i class="bx bx-dollar-circle fs-1 text-warning"></i></div>
            <div class="fw-bold fs-4">${{ number_format($totalGastado ?? 0, 2) }}</div>
            <div class="text-muted">Total gastado</div>
          </div>
        </div>
      </div>
      <div class="row g-3">
        <div class="col-md-6">
          <div class="card shadow-sm border-0 rounded-3 text-center p-4 animate__animated animate__fadeInLeft">
            <div class="mb-3"><i class="bx bx-store-alt bx-spin fs-1 text-primary"></i></div>
            <h5 class="fw-bold mb-2">¡Próximamente Tienda Virtual!</h5>
            <p class="text-muted mb-0">Muy pronto podrás comprar productos directamente desde tu cuenta. ¡Espéralo!</p>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card shadow-sm border-0 rounded-3 text-center p-4 animate__animated animate__fadeInRight">
            <div class="mb-3"><i class="bx bx-bulb fs-2 text-info"></i></div>
            <h6 class="fw-bold">Anuncios y novedades</h6>
            <p class="text-muted mb-0">¡Pronto más funciones y sorpresas para ti!</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection 