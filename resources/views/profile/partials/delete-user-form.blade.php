@php $user = Auth::user(); @endphp
@if(!$user->pending_delete_at && !$user->deleted_at)
    <!-- Botón para abrir el modal -->
    <button type="button" class="btn btn-danger btn-lg w-100" data-bs-toggle="modal" data-bs-target="#modalEliminarCuentaPerfil">
        <i class="bx bx-trash me-2"></i> Eliminar mi cuenta
    </button>

    <!-- Modal profesional para eliminar cuenta -->
    <div class="modal fade" id="modalEliminarCuentaPerfil" tabindex="-1" aria-labelledby="modalEliminarCuentaPerfilLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow-lg border-0 rounded-4 animate__animated animate__fadeInDown">
                <div class="modal-header bg-danger text-white align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bx bx-trash display-5"></i>
                        <h5 class="modal-title mb-0" id="modalEliminarCuentaPerfilLabel">Eliminar Mi Cuenta</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form method="POST" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')
                    <div class="modal-body">
                        <!-- Alerta de advertencia -->
                        <div class="alert alert-danger border-0 shadow-sm mb-4">
                            <div class="d-flex align-items-center gap-3">
                                <i class="bx bx-error-circle fs-1 text-danger"></i>
                                <div>
                                    <h6 class="alert-heading mb-1"><strong>¡ACCIÓN IMPORTANTE!</strong></h6>
                                    <p class="mb-0">Su cuenta se marcará para eliminación y se eliminará definitivamente en 3 días. Durante este período podrá cancelar la eliminación iniciando sesión.</p>
                                </div>
                            </div>
                        </div>
                        <!-- Información del usuario -->
                        <div class="card border-0 bg-light mb-4">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0"><i class="bx bx-id-card me-2"></i>Información de Su Cuenta</h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="row align-items-center">
                                    <div class="col-md-4 text-center">
                                        <i class="bx bx-user-circle display-3 text-secondary"></i>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="mb-2">
                                            <span class="form-label text-muted small">Nombre</span>
                                            <span class="fw-bold d-block">{{ $user->name }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="form-label text-muted small">Email</span>
                                            <span class="d-block">{{ $user->email }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="form-label text-muted small">Roles</span>
                                            <span class="d-block">
                                                @foreach($user->getRoleNames() as $role)
                                                    <span class="badge bg-info">{{ $role }}</span>
                                                @endforeach
                                            </span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="form-label text-muted small">Miembro desde</span>
                                            <span class="d-block">{{ $user->created_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="mb-3 fw-bold">¿Está seguro que desea eliminar su cuenta?</p>
                        <div class="mb-3 input-group">
                            <span class="input-group-text"><i class="bx bx-lock"></i></span>
                            <input type="password" name="password" class="form-control @if($errors->userDeletion->has('password')) is-invalid @endif" placeholder="Confirme su contraseña actual" required autocomplete="current-password" value="{{ old('password') }}">
                            @if($errors->userDeletion->has('password'))
                                <div class="invalid-feedback d-block">{{ $errors->userDeletion->first('password') }}</div>
                            @endif
                        </div>
                        <div class="mb-3 input-group">
                            <span class="input-group-text"><i class="bx bx-message-square-dots"></i></span>
                            <textarea name="motivo" class="form-control" rows="3" placeholder="Motivo de la eliminación (opcional)">{{ old('motivo') }}</textarea>
                        </div>
                        <div class="alert alert-info border-0 shadow-sm">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bx bx-info-circle"></i>
                                <div>
                                    <strong>Importante:</strong> Tendrá 3 días para cancelar la eliminación iniciando sesión nuevamente.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bx bx-x me-1"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-danger btn-lg">
                            <i class="bx bx-trash me-1"></i> Eliminar Mi Cuenta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
