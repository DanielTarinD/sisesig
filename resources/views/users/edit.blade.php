@extends('layouts.app')

@section('title', 'Editar usuario')

@section('content')

<div class="row justify-content-center">

    <div class="col-12 col-lg-8">

        <div class="d-flex align-items-center gap-3 mb-4">

            <a href="{{ route('users.index') }}"
               class="btn btn-outline-secondary">

                <i class="bi bi-arrow-left"></i>

            </a>

            <div>
                <h2 class="fw-bold mb-1">Editar usuario</h2>

                <p class="text-muted mb-0">
                    {{ $user->name }}
                </p>
            </div>

        </div>

        <div class="card">

            <div class="card-body p-4">

                <form method="POST"
                      action="{{ route('users.update', $user) }}">

                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Nombre
                        </label>

                        <input type="text"
                               name="name"
                               class="form-control"
                               value="{{ old('name', $user->name) }}"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Correo electrónico
                        </label>

                        <input type="email"
                               name="email"
                               class="form-control"
                               value="{{ old('email', $user->email) }}"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Rol
                        </label>

                        <select name="role" class="form-select" required>

                            <option value="capturista"
                                @selected(old('role', $user->role) === 'capturista')>
                                Capturista
                            </option>

                            <option value="visor"
                                @selected(old('role', $user->role) === 'visor')>
                                Visor
                            </option>

                        </select>
                    </div>

                    <hr class="my-4">

                    <div class="alert alert-light border">
                        <i class="bi bi-info-circle me-1"></i>
                        Si no deseas cambiar la contraseña, deja estos campos vacíos.
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Nueva contraseña
                        </label>

                        <input type="password"
                               name="password"
                               class="form-control"
                               minlength="8">

                        <div class="form-text">
                            Mínimo 8 caracteres.
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            Confirmar nueva contraseña
                        </label>

                        <input type="password"
                               name="password_confirmation"
                               class="form-control"
                               minlength="8">
                    </div>

                    <div class="d-flex flex-column flex-sm-row gap-2 justify-content-end">

                        <a href="{{ route('users.index') }}"
                           class="btn btn-outline-secondary">
                            Cancelar
                        </a>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>
                            Guardar cambios
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

@endsection