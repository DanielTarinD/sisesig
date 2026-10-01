@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Usuarios</h2>
            <p class="text-muted mb-0">
                Administración de usuarios del sistema.
            </p>
        </div>

        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i>
            Nuevo usuario
        </a>
    </div>

    <div class="card">

        <div class="card-header bg-white py-3">
            <strong>
                Usuarios registrados
            </strong>

            <span class="text-muted ms-2">
                {{ $users->count() }}
            </span>
        </div>

        <div class="table-responsive">

            <table class="table table-hover mb-0">

                <thead class="table-light">
                    <tr>
                        <th>Nombre</th>
                        <th>Correo electrónico</th>
                        <th>Rol</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($users as $user)
                        <tr>

                            <td>
                                <div class="fw-semibold">
                                    {{ $user->name }}
                                </div>
                            </td>

                            <td>
                                {{ $user->email }}
                            </td>

                            <td>

                                @if ($user->role === 'capturista')
                                    <span class="badge text-bg-primary">
                                        Capturista
                                    </span>
                                @else
                                    <span class="badge text-bg-secondary">
                                        Visor
                                    </span>
                                @endif

                            </td>

                            <td class="text-end">

                                <div class="d-flex justify-content-end gap-1">

                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary">

                                        <i class="bi bi-pencil"></i>

                                        <span class="d-none d-sm-inline">
                                            Editar
                                        </span>

                                    </a>

                                    @if ($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('users.destroy', $user) }}"
                                            onsubmit="return confirm('¿Está seguro de eliminar este usuario? Esta acción no se puede deshacer.');">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-sm btn-outline-danger">

                                                <i class="bi bi-trash"></i>

                                                <span class="d-none d-sm-inline">
                                                    Eliminar
                                                </span>

                                            </button>

                                        </form>
                                    @endif

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                No existen usuarios registrados.
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

@endsection
