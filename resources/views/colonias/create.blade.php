@extends('layouts.app')

@section('title', 'Nueva colonia')

@section('content')

<div class="container-fluid">

    <div class="mb-4">

        <a href="{{ route('colonias.index') }}"
           class="text-decoration-none">

            <i class="bi bi-arrow-left"></i>
            Regresar al catálogo

        </a>

    </div>


    <div class="row justify-content-center">

        <div class="col-12 col-md-8 col-lg-6">

            <div class="card shadow-sm border-0">

                <div class="card-header bg-white py-3">

                    <h1 class="h5 mb-1">
                        <i class="bi bi-plus-circle me-1"></i>
                        Nueva colonia
                    </h1>

                    <small class="text-muted">
                        Registre el municipio y el nombre de la colonia.
                    </small>

                </div>


                <div class="card-body">

                    <form method="POST"
                          action="{{ route('colonias.store') }}">

                        @csrf


                        {{-- Municipio --}}

                        <div class="mb-3">

                            <label for="municipio"
                                   class="form-label fw-semibold">

                                Municipio
                                <span class="text-danger">*</span>

                            </label>

                            <input type="text"
                                   name="municipio"
                                   id="municipio"
                                   value="{{ old('municipio') }}"
                                   class="form-control @error('municipio') is-invalid @enderror"
                                   maxlength="255"
                                   required
                                   autofocus>

                            @error('municipio')

                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>


                        {{-- Colonia --}}

                        <div class="mb-4">

                            <label for="name"
                                   class="form-label fw-semibold">

                                Nombre de la colonia
                                <span class="text-danger">*</span>

                            </label>

                            <input type="text"
                                   name="name"
                                   id="name"
                                   value="{{ old('name') }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   maxlength="255"
                                   required>

                            @error('name')

                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>


                        <div class="d-flex flex-column flex-sm-row gap-2">

                            <a href="{{ route('colonias.index') }}"
                               class="btn btn-outline-secondary flex-fill">

                                <i class="bi bi-x-circle me-1"></i>
                                Cancelar

                            </a>


                            <button type="submit"
                                    class="btn btn-primary flex-fill">

                                <i class="bi bi-check-circle me-1"></i>
                                Guardar colonia

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection