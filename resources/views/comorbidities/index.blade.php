@extends('layouts.app')

@section('title', 'Catálogo de Comorbilidades')

@section('content')

<div class="page-heading d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">

    <div>
        <h2 class="mb-1">
            <i class="bi bi-heart-pulse"></i>
            Comorbilidades
        </h2>

        <p class="text-muted mb-0">
            Administración del catálogo de comorbilidades.
        </p>
    </div>

    <a href="{{ route('comorbidities.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i>
        Nueva comorbilidad
    </a>

</div>


<div class="card">

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover mb-0">

                <thead class="table-light">

                    <tr>
                        <th class="ps-3">Comorbilidad</th>
                        <th class="text-center">Pacientes</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end pe-3">Acciones</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($comorbidities as $comorbidity)

                        <tr>

                            <td class="ps-3 fw-semibold">
                                {{ $comorbidity->name }}
                            </td>

                            <td class="text-center">
                                <span class="badge bg-secondary">
                                    {{ $comorbidity->patients_count }}
                                </span>
                            </td>

                            <td class="text-center">

                                @if($comorbidity->active)

                                    <span class="badge bg-success">
                                        Activa
                                    </span>

                                @else

                                    <span class="badge bg-secondary">
                                        Inactiva
                                    </span>

                                @endif

                            </td>

                            <td class="text-end pe-3">

                                <div class="d-flex justify-content-end gap-2">

                                    <a
                                        href="{{ route('comorbidities.edit', $comorbidity) }}"
                                        class="btn btn-sm btn-outline-primary"
                                        title="Editar"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('comorbidities.toggle', $comorbidity) }}"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="btn btn-sm {{ $comorbidity->active ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                            title="{{ $comorbidity->active ? 'Desactivar' : 'Activar' }}"
                                        >
                                            <i class="bi {{ $comorbidity->active ? 'bi-toggle-on' : 'bi-toggle-off' }}"></i>
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                No hay comorbilidades registradas.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection