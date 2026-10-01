@extends('layouts.app')

@section('title', 'Editar Comorbilidad')

@section('content')

<div class="page-heading mb-4">

    <h2 class="mb-1">
        <i class="bi bi-pencil"></i>
        Editar comorbilidad
    </h2>

    <p class="text-muted mb-0">
        Modificar el nombre de la comorbilidad.
    </p>

</div>


<div class="card">

    <div class="card-body">

        <form
            method="POST"
            action="{{ route('comorbidities.update', $comorbidity) }}"
        >

            @csrf
            @method('PUT')

            <div class="mb-4">

                <label for="name" class="form-label fw-semibold">
                    Nombre
                    <span class="text-danger">*</span>
                </label>

                <input
                    type="text"
                    name="name"
                    id="name"
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $comorbidity->name) }}"
                    maxlength="255"
                    required
                    autofocus
                >

                @error('name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            <div class="alert alert-info">

                <i class="bi bi-info-circle"></i>

                Esta comorbilidad ha sido utilizada por
                <strong>{{ $comorbidity->patients_count ?? $comorbidity->patients()->count() }}</strong>
                paciente(s).

                Modificar el nombre no elimina los registros históricos.

            </div>


            <div class="d-flex flex-column flex-sm-row gap-2 justify-content-end">

                <a
                    href="{{ route('comorbidities.index') }}"
                    class="btn btn-outline-secondary"
                >
                    Cancelar
                </a>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i>
                    Guardar cambios
                </button>

            </div>

        </form>

    </div>

</div>

@endsection