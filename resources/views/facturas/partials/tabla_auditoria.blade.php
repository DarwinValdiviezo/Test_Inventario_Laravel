<div class="table-responsive">
  <table class="table table-striped align-middle mb-0">
    <thead class="table-light">
      <tr>
        <th>Fecha</th>
        <th>Usuario</th>
        <th>Acción</th>
        <th>Detalles</th>
      </tr>
    </thead>
    <tbody>
      @forelse($auditorias as $auditoria)
        <tr>
          <td>{{ $auditoria->created_at->format('d/m/Y H:i') }}</td>
          <td>{{ $auditoria->user->name ?? 'Sistema' }}</td>
          <td><span class="badge bg-info">{{ $auditoria->accion ?? '-' }}</span></td>
          <td>{{ $auditoria->detalles ?? '-' }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="4" class="text-center text-muted">No hay registros de auditoría para facturas anuladas.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div> 