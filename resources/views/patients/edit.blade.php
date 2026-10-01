@extends('layouts.app')

@section('title', 'Editar expediente')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3 page-heading">

    <div>

        <h2 class="fw-bold mb-1">
            <i class="bi bi-pencil-square me-1"></i>
            Editar expediente
        </h2>

        <p
            class="text-muted mb-0 text-truncate"
            style="max-width:70vw"
        >
            {{ $patient->name }}
        </p>

    </div>


    <a
        href="{{ route('patients.show', $patient) }}"
        class="btn btn-outline-secondary"
    >
        <i class="bi bi-x-lg"></i>

        <span class="mobile-hide-text ms-1">
            Cancelar
        </span>
    </a>

</div>


<form
    method="POST"
    action="{{ route('patients.update', $patient) }}"
    autocomplete="off"
>

    @method('PUT')

    @include('patients._form')


    <div class="capture-actions no-print">

        <a
            href="{{ route('patients.show', $patient) }}"
            class="btn btn-outline-secondary"
        >
            Cancelar
        </a>

        <button
            type="submit"
            class="btn btn-primary"
        >
            <i class="bi bi-save me-1"></i>
            Guardar cambios
        </button>

    </div>

</form>

@endsection


@push('scripts')

<script>

$(function () {

    /*
    ==========================================================
    BÚSQUEDA DE PACIENTES EXISTENTES
    ==========================================================
    */

    let timer;


    $('#patient-name').on(
        'input',
        function () {

            clearTimeout(timer);


            const q =
                $(this)
                .val()
                .trim();


            const box =
                $('#name-suggestions')
                .empty();


            /*
            No buscar con menos de 2 caracteres
            */

            if (q.length < 2) {

                return;

            }


            /*
            Esperar 250 ms antes de consultar
            */

            timer = setTimeout(
                function () {

                    $.get(
                        '{{ route('patients.search') }}',
                        {
                            q: q
                        },
                        function (data) {

                            /*
                            Mostrar pacientes encontrados
                            */

                            data.forEach(
                                function (p) {

                                    box.append(

                                        $('<a/>', {

                                            href:
                                                '{{ url('/patients') }}/' +
                                                p.id,

                                            class:
                                                'list-group-item list-group-item-action',

                                            html:
                                                '<strong>' +
                                                $('<div>')
                                                    .text(p.name)
                                                    .html() +
                                                '</strong>' +

                                                '<small class="d-block text-muted">' +
                                                'Nacimiento: ' +
                                                $('<div>')
                                                    .text(p.date_of_birth)
                                                    .html() +
                                                '</small>'

                                        })

                                    );

                                }
                            );


                            /*
                            Si no existen coincidencias
                            */

                            if (!data.length) {

                                box.append(

                                    $('<div/>', {

                                        class:
                                            'list-group-item text-muted',

                                        text:
                                            'No existe otro paciente con ese nombre. El nombre actual puede mantenerse.'

                                    })

                                );

                            }

                        }
                    );

                },
                250
            );

        }
    );


    /*
    ==========================================================
    CERRAR SUGERENCIAS AL HACER CLIC FUERA
    ==========================================================
    */

    $(document).on(
        'click',
        function (e) {

            if (
                !$(e.target).closest(
                    '#patient-name, #name-suggestions'
                ).length
            ) {

                $('#name-suggestions').empty();

            }

        }
    );

});

</script>

@endpush