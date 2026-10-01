@csrf



<div class="capture-section">



    <div class="capture-section-title">

        <div class="capture-icon">

            <i class="bi bi-person-vcard"></i>

        </div>



        <div>

            <h5 class="mb-0">Datos del paciente</h5>

            <small class="text-muted">Información del expediente</small>

        </div>

    </div>





    <div class="row g-3">



        {{-- =====================================================

        NOMBRE

        ====================================================== --}}



        <div class="col-12">



            <label class="form-label capture-label" for="patient-name">

                Nombre completo

                <span class="text-danger">*</span>

            </label>



            <div class="position-relative">



                <input type="text" name="name" id="patient-name" class="form-control capture-input"
                    value="{{ old('name', $patient->name ?? '') }}" autocomplete="off" autocapitalize="words"
                    spellcheck="false" placeholder="Escriba el nombre del paciente" required>



                <div id="name-suggestions" class="list-group capture-suggestions"></div>

                <input type="hidden" name="confirm_duplicate" id="confirm_duplicate" value="0">

                <div id="duplicate-warning" class="alert alert-warning mt-3 d-none" role="alert">

                    <div class="d-flex align-items-start gap-2">



                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>



                        <div class="flex-grow-1">



                            <div class="fw-bold mb-1">

                                Posible paciente existente

                            </div>



                            <div id="duplicate-results" class="small"></div>



                        </div>



                    </div>

                </div>



            </div>



            <div class="form-text">

                <i class="bi bi-search"></i>

                Escriba al menos 2 letras para buscar en el catálogo.

            </div>



        </div>





        {{-- =====================================================

        FECHA DE NACIMIENTO

        ====================================================== --}}



        <div class="col-12 col-sm-6">



            <label class="form-label capture-label" for="date_of_birth">

                Fecha de nacimiento

                <span class="text-danger">*</span>

            </label>



            <input type="date" name="date_of_birth" id="date_of_birth" class="form-control capture-input"
                value="{{ old('date_of_birth', isset($patient) && $patient->date_of_birth ? $patient->date_of_birth->format('Y-m-d') : '') }}"
                required>



        </div>





        {{-- =====================================================

        COLONIA

        ====================================================== --}}



        <div class="col-12 col-md-6">



            @php

                $coloniaActual = old('colonia', $patient->colonia ?? null);

            @endphp



            <label for="colonia" class="form-label capture-label">

                Colonia

            </label>



            <select name="colonia" id="colonia"
                class="form-select capture-input @error('colonia') is-invalid @enderror" style="width: 100%;">



                <option value=""></option>



                @foreach ($colonias as $colonia)
                    <option value="{{ $colonia->name }}" data-municipio="{{ $colonia->municipio }}"
                        @selected($coloniaActual === $colonia->name)>
                        {{ $colonia->name }}
                    </option>
                @endforeach



            </select>



            @error('colonia')
                <div class="invalid-feedback d-block">

                    {{ $message }}

                </div>
            @enderror



            <div class="form-text">

                <i class="bi bi-search"></i>

                Escriba para buscar o agregar una colonia.

            </div>



        </div>



    </div>



</div>





{{-- =========================================================

PRIMERA VEZ

\========================================================= --}}



<div class="capture-section">



    <div class="capture-section-title">



        <div class="capture-icon">

            <i class="bi bi-calendar-check"></i>

        </div>



        <div>

            <h5 class="mb-0">Primera vez</h5>

            <small class="text-muted">

                Registro único del expediente

            </small>

        </div>



    </div>





    <div class="capture-toggle-row">



        <div>



            <div class="fw-semibold">

                ¿Es primera vez?

            </div>



            <small class="text-muted">

                Al activarlo se registra la fecha.

            </small>



        </div>





        <div class="form-check form-switch capture-switch">



            <input class="form-check-input" type="checkbox" role="switch" name="first_visit" value="1"
                id="first_visit" @checked(old('first_visit', isset($patient) && $patient->first_visit_date))>



        </div>



    </div>





    <div id="first-visit-date-wrapper" class="mt-3">



        <label class="form-label capture-label" for="first_visit_date">

            Fecha de primera vez

        </label>



        <input type="date" name="first_visit_date" id="first_visit_date" class="form-control capture-input"
            value="{{ old(
                'first_visit_date',
            
                isset($patient) && $patient->first_visit_date ? $patient->first_visit_date->format('Y-m-d') : '',
            ) }}">



    </div>



</div>





{{-- =========================================================

COMORBILIDADES

\========================================================= --}}



<div class="capture-section">



    <div class="capture-section-title">



        <div class="capture-icon">

            <i class="bi bi-heart-pulse"></i>

        </div>



        <div>

            <h5 class="mb-0">Comorbilidades</h5>



            <small class="text-muted">

                Puede seleccionar más de una

            </small>

        </div>



    </div>





    @php

        $selectedComorbidities = old(
            'comorbidities',

            isset($patient) ? $patient->comorbidities->pluck('id')->all() : [],
        );

    @endphp





    <div class="row g-2">



        @foreach ($comorbidities as $comorbidity)
            @php

                $isSelected = in_array(
                    $comorbidity->id,

                    old('comorbidities', isset($patient) ? $patient->comorbidities->pluck('id')->toArray() : []),
                );

            @endphp



            <div class="form-check mb-2">



                <input class="form-check-input" type="checkbox" name="comorbidities[]" value="{{ $comorbidity->id }}"
                    id="comorbidity-{{ $comorbidity->id }}" @checked($isSelected)>



                <label class="form-check-label" for="comorbidity-{{ $comorbidity->id }}">



                    {{ $comorbidity->name }}



                    @if (!$comorbidity->active)
                        <span class="badge bg-secondary ms-1">

                            Inactiva

                        </span>
                    @endif



                </label>



            </div>
        @endforeach



    </div>



</div>


{{-- =========================================================
     MODAL - NUEVA COLONIA
     ========================================================= --}}

<div class="modal fade" id="modalNuevaColonia" tabindex="-1" aria-labelledby="modalNuevaColoniaLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title" id="modalNuevaColoniaLabel">

                    <i class="bi bi-geo-alt me-1"></i>
                    Nueva colonia

                </h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar">
                </button>

            </div>


            <div class="modal-body">

                <div class="alert alert-info small">

                    <i class="bi bi-info-circle me-1"></i>

                    La colonia se agregará al catálogo y quedará
                    disponible para futuros registros.

                </div>


                {{-- Municipio --}}

                <div class="mb-3">

                    <label for="nueva_colonia_municipio" class="form-label fw-semibold">

                        Municipio
                        <span class="text-danger">*</span>

                    </label>

                    <select id="nueva_colonia_municipio" class="form-select">

                        <option value="">
                            Seleccione un municipio...
                        </option>

                        @foreach ($municipios as $municipio)
                            <option value="{{ $municipio }}">
                                {{ $municipio }}
                            </option>
                        @endforeach

                    </select>

                </div>


                {{-- Colonia --}}

                <div class="mb-2">

                    <label for="nueva_colonia_nombre" class="form-label fw-semibold">

                        Nombre de la colonia
                        <span class="text-danger">*</span>

                    </label>

                    <input type="text" id="nueva_colonia_nombre" class="form-control" maxlength="255"
                        autocomplete="off">

                </div>

                <div id="nueva-colonia-error" class="alert alert-danger d-none mt-3 mb-0">
                </div>

            </div>


            <div class="modal-footer">

                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">

                    Cancelar

                </button>

                <button type="button" id="btnGuardarNuevaColonia" class="btn btn-primary">

                    <i class="bi bi-check-circle me-1"></i>
                    Guardar colonia

                </button>

            </div>

        </div>

    </div>

</div>


{{-- =========================================================

SCRIPT

\========================================================= --}}



@push('scripts')
    <script>
        $(function() {



            /*

            ==========================================================

            PRIMERA VEZ

            ==========================================================

            */



            const syncFirstVisit = function() {



                const checked = $('#first_visit').is(':checked');



                const input = $('#first_visit_date');



                const wrapper = $('#first-visit-date-wrapper');





                input.prop('disabled', !checked);



                wrapper.toggleClass(

                    'd-none',

                    !checked

                );





                if (checked && !input.val()) {



                    const d = new Date();



                    const localDate =

                        new Date(

                            d.getTime() -

                            d.getTimezoneOffset() * 60000

                        )

                        .toISOString()

                        .slice(0, 10);



                    input.val(localDate);

                }





                if (!checked) {



                    input.val('');



                }



            };





            $('#first_visit').on(

                'change',

                syncFirstVisit

            );





            syncFirstVisit();





            /*

            ==========================================================

            SELECT2 - COLONIA

            ==========================================================

            */



            const $colonia = $('#colonia');





            if ($colonia.length && typeof $.fn.select2 !== 'undefined') {



                $colonia.select2({



                    placeholder: 'Buscar o seleccionar colonia...',



                    allowClear: true,



                    width: '100%',



                    language: {



                        noResults: function() {



                            return 'No se encontró la colonia';



                        },



                        searching: function() {



                            return 'Buscando...';



                        }



                    },



                    escapeMarkup: function(markup) {



                        return markup;



                    },



                    templateResult: function(data) {

                        if (data.id === '__new__') {

                            return $(
                                '<span class="select2-add-option">' +
                                '<i class="bi bi-plus-circle me-1"></i>' +
                                data.text +
                                '</span>'
                            );

                        }

                        if (!data.id) {
                            return data.text;
                        }

                        const municipio = $(data.element).data('municipio');

                        if (municipio) {
                            return $('<span>').text(
                                municipio + ' - ' + data.text
                            );
                        }

                        return data.text;
                    },

                    templateSelection: function(data) {

                        if (!data.id) {
                            return data.text;
                        }

                        const municipio = $(data.element).data('municipio');

                        if (municipio) {
                            return municipio + ' - ' + data.text;
                        }

                        return data.text;
                    }

                });





                /*

                ======================================================

                AL ABRIR SELECT2

                ======================================================

                */



                $colonia.on(

                    'select2:open',

                    function() {



                        const $search =

                            $('.select2-container--open')

                            .find('.select2-search__field');





                        $search

                            .off('input.colonia')

                            .on(

                                'input.colonia',

                                function() {



                                    const texto =

                                        $.trim(

                                            $(this).val()

                                        );





                                    /*

                                    Menos de 2 caracteres

                                    */



                                    if (texto.length < 2) {



                                        $('.select2-add-new-option')

                                            .remove();



                                        return;



                                    }





                                    /*

                                    Comprobar si ya existe

                                    */



                                    let existe = false;





                                    $colonia

                                        .find('option')

                                        .each(function() {



                                            const nombre =

                                                $.trim(

                                                    $(this).val() || ''

                                                );





                                            if (

                                                nombre.toLowerCase() ===

                                                texto.toLowerCase()

                                            ) {



                                                existe = true;



                                                return false;



                                            }



                                        });





                                    /*

                                    Eliminar opción anterior

                                    */



                                    $('.select2-add-new-option')

                                        .remove();





                                    /*

                                    Si no existe,

                                    mostrar opción para crear

                                    */



                                    if (!existe) {



                                        const $results =

                                            $('.select2-results__options');





                                        const $option =

                                            $('<li>', {



                                                class: 'select2-results__option select2-add-new-option',



                                                role: 'option'



                                            });





                                        const textoSeguro =

                                            $('<div>')

                                            .text(texto)

                                            .html();





                                        $option.html(



                                            '<span class="select2-add-option">' +



                                            '<i class="bi bi-plus-circle me-1"></i>' +



                                            'Agregar "' +



                                            textoSeguro +



                                            '"' +



                                            '</span>'



                                        );





                                        /*

                                        Al hacer clic

                                        */



                                        $option.on(

                                            'mousedown',

                                            function(e) {



                                                e.preventDefault();



                                                agregarColonia(

                                                    texto

                                                );



                                            }

                                        );





                                        $results.prepend(

                                            $option

                                        );



                                    }



                                }

                            );



                    }

                );





                /*

                ======================================================

                CREAR COLONIA

                ======================================================

                */



                let nombreColoniaPendiente = '';

                function agregarColonia(nombre) {
                    nombre = $.trim(nombre);

                    if (!nombre) {
                        return;
                    }

                    nombreColoniaPendiente = nombre;

                    /*
                     * Cerrar Select2.
                     */

                    $colonia.select2('close');


                    /*
                     * Limpiar formulario del modal.
                     */

                    $('#nueva_colonia_municipio').val('');

                    $('#nueva_colonia_nombre').val(nombre);

                    $('#nueva-colonia-error')
                        .addClass('d-none')
                        .empty();


                    /*
                     * Mostrar modal.
                     */

                    const modalElement =
                        document.getElementById('modalNuevaColonia');

                    const modal =
                        bootstrap.Modal.getOrCreateInstance(modalElement);

                    modal.show();


                    /*
                     * Colocar foco en municipio.
                     */

                    setTimeout(function() {

                        $('#nueva_colonia_municipio').trigger('focus');

                    }, 300);
                }

                $('#btnGuardarNuevaColonia').on(
                    'click',
                    function() {

                        const municipio =
                            $.trim(
                                $('#nueva_colonia_municipio').val()
                            );

                        const nombre =
                            $.trim(
                                $('#nueva_colonia_nombre').val()
                            );

                        const $error =
                            $('#nueva-colonia-error');


                        /*
                         * Validaciones
                         */

                        if (!municipio) {

                            $error
                                .removeClass('d-none')
                                .text(
                                    'Debe seleccionar un municipio.'
                                );

                            return;
                        }


                        if (!nombre) {

                            $error
                                .removeClass('d-none')
                                .text(
                                    'Debe escribir el nombre de la colonia.'
                                );

                            return;
                        }


                        /*
                         * Estado del botón
                         */

                        const $button =
                            $('#btnGuardarNuevaColonia');

                        const textoOriginal =
                            $button.html();

                        $button
                            .prop('disabled', true)
                            .html(
                                '<span class="spinner-border spinner-border-sm me-1"></span>' +
                                'Guardando...'
                            );

                        $error
                            .addClass('d-none')
                            .empty();


                        /*
                         * Guardar
                         */

                        $.ajax({

                            url: '{{ route('colonias.quickStore') }}',

                            method: 'POST',

                            data: {

                                _token: '{{ csrf_token() }}',

                                municipio: municipio,

                                name: nombre

                            },


                            success: function(response) {

                                if (!response.success) {

                                    $error
                                        .removeClass('d-none')
                                        .text(
                                            'No fue posible agregar la colonia.'
                                        );

                                    return;
                                }


                                /*
                                 * Crear nueva opción.
                                 */

                                const nuevaOpcion =
                                    new Option(
                                        response.name,
                                        response.name,
                                        true,
                                        true
                                    );


                                /*
                                 * Guardar municipio como atributo.
                                 */

                                $(nuevaOpcion).attr(
                                    'data-municipio',
                                    response.municipio
                                );


                                /*
                                 * Agregar al Select2.
                                 */

                                $colonia.append(
                                    nuevaOpcion
                                );


                                /*
                                 * Seleccionarla.
                                 */

                                $colonia
                                    .val(response.name)
                                    .trigger('change');


                                /*
                                 * Cerrar modal.
                                 */

                                const modalElement =
                                    document.getElementById(
                                        'modalNuevaColonia'
                                    );

                                const modal =
                                    bootstrap.Modal.getInstance(
                                        modalElement
                                    );

                                if (modal) {
                                    modal.hide();
                                }

                            },


                            error: function(xhr) {

                                let mensaje =
                                    'No fue posible agregar la colonia.';


                                if (
                                    xhr.responseJSON &&
                                    xhr.responseJSON.errors
                                ) {

                                    const errors =
                                        xhr.responseJSON.errors;


                                    if (errors.municipio) {

                                        mensaje =
                                            errors.municipio[0];

                                    } else if (errors.name) {

                                        mensaje =
                                            errors.name[0];

                                    }

                                }


                                $error
                                    .removeClass('d-none')
                                    .text(mensaje);

                            },


                            complete: function() {

                                $button
                                    .prop('disabled', false)
                                    .html(textoOriginal);

                            }

                        });

                    }
                );



            }



            /*

            ==========================================================

            DETECCIÓN DE POSIBLES DUPLICADOS

            ==========================================================

            */



            let duplicateTimer = null;



            let duplicateRequest = null;





            function revisarDuplicado() {



                clearTimeout(duplicateTimer);





                const nombre =

                    $.trim(

                        $('#patient-name').val()

                    );





                const fechaNacimiento =

                    $('#date_of_birth').val();





                const $warning =

                    $('#duplicate-warning');





                const $results =

                    $('#duplicate-results');





                /*

                No revisar hasta tener ambos datos

                */



                if (

                    nombre.length < 3 ||

                    !fechaNacimiento

                ) {



                    $warning.addClass('d-none');



                    $results.empty();



                    return;



                }





                /*

                Esperar un momento antes de consultar

                */



                duplicateTimer = setTimeout(

                    function() {



                        /*

                        Cancelar consulta anterior

                        */



                        if (duplicateRequest) {



                            duplicateRequest.abort();



                        }





                        duplicateRequest = $.ajax({



                            url: '{{ route('patients.checkDuplicate') }}',



                            method: 'GET',



                            data: {



                                name: nombre,



                                date_of_birth: fechaNacimiento,



                                patient_id: @json($patient->id ?? null)



                            },





                            success: function(data) {



                                $results.empty();





                                /*

                                No hay coincidencias

                                */



                                if (!data.length) {



                                    $warning.addClass('d-none');



                                    return;



                                }





                                /*

                                Mostrar advertencia

                                */



                                $warning.removeClass('d-none');





                                data.forEach(

                                    function(patient) {



                                        const $item =

                                            $('<div>', {

                                                class: 'border rounded p-2 mb-2 bg-white'

                                            });





                                        const $name =

                                            $('<div>', {

                                                class: 'fw-semibold'

                                            });





                                        const $link =

                                            $('<a>', {



                                                href: '{{ url('/patients') }}/' +

                                                    patient.id,



                                                class: 'text-decoration-none',



                                                text: patient.name



                                            });





                                        $name.append(

                                            $link

                                        );





                                        const $details =

                                            $('<div>', {



                                                class: 'text-muted mt-1'



                                            });





                                        $details.html(



                                            '<div>' +

                                            '<strong>Fecha de nacimiento:</strong> ' +

                                            patient.date_of_birth +

                                            '</div>' +



                                            '<div>' +

                                            '<strong>Edad:</strong> ' +

                                            patient.age +

                                            ' años' +

                                            '</div>' +



                                            '<div>' +

                                            '<strong>Colonia:</strong> ' +

                                            $('<div>')

                                            .text(patient.colonia)

                                            .html() +

                                            '</div>' +



                                            '<div>' +

                                            '<strong>Última atención:</strong> ' +

                                            $('<div>')

                                            .text(patient.last_attention)

                                            .html() +

                                            '</div>'



                                        );





                                        $item.append(

                                            $name

                                        );



                                        $item.append(

                                            $details

                                        );





                                        $results.append(

                                            $item

                                        );



                                    }

                                );





                                /*

                                Mensaje final

                                */



                                $results.append(



                                    $('<div>', {



                                        class: 'mt-2',



                                        html: '<strong>Revise este expediente antes de continuar.</strong> ' +

                                            'Si corresponde a la misma persona, utilice el expediente existente.'



                                    })



                                );



                            },





                            error: function(xhr, status) {



                                /*

                                No mostrar error si cancelamos

                                la consulta anterior

                                */



                                if (

                                    status === 'abort'

                                ) {



                                    return;



                                }



                            }



                        });



                    },

                    400

                );



            }





            $('#patient-name').on(

                'input',

                revisarDuplicado

            );





            $('#date_of_birth').on(

                'change',

                revisarDuplicado

            );



            /*

            ==========================================================

            CONFIRMACIÓN DE POSIBLE DUPLICADO

            ==========================================================

            */



            const $patientForm = $('#patient-name').closest('form');





            function resetDuplicateConfirmation() {



                $('#confirm_duplicate').val('0');



            }





            $('#patient-name').on(

                'input',

                resetDuplicateConfirmation

            );





            $('#date_of_birth').on(

                'change',

                resetDuplicateConfirmation

            );





            $patientForm.on(

                'submit',

                function(e) {



                    const $warning =

                        $('#duplicate-warning');





                    /*

                    No hay posible duplicado.

                    El formulario continúa normalmente.

                    */



                    if (

                        $warning.hasClass('d-none')

                    ) {



                        return;



                    }





                    /*

                    Ya fue confirmado anteriormente.

                    */



                    if (

                        $('#confirm_duplicate').val() === '1'

                    ) {



                        return;



                    }





                    /*

                    Detener temporalmente el envío.

                    */



                    e.preventDefault();





                    const confirmar = window.confirm(



                        'Se encontró un posible paciente existente con el mismo nombre y fecha de nacimiento.\n\n' +



                        'Revise el expediente mostrado en la advertencia.\n\n' +



                        '¿Está seguro de que se trata de una persona diferente y desea continuar?'



                    );





                    if (!confirmar) {



                        return;



                    }





                    /*

                    El usuario confirmó que es otra persona.

                    */



                    $('#confirm_duplicate').val('1');





                    /*

                    Volver a enviar el formulario.

                    */



                    this.submit();



                }

            );



        });
    </script>
@endpush



@push('styles')
    <style>
        //=========================================================

        SELECT2 - COLONIA=========================================================*/ #colonia+.select2-container {

            width: 100% !important;

        }





        /* Campo principal */



        .select2-container--default .select2-container--focus .select2-selection--single,



        .select2-container--default .select2-selection--single {



            height: 52px !important;



            min-height: 52px !important;



            border: 1px solid #dee2e6 !important;



            border-radius: 12px !important;



            background-color: #fff !important;



            transition:

                border-color .15s ease,

                box-shadow .15s ease;



        }





        /* Texto seleccionado */



        .select2-container--default .select2-selection--single .select2-selection__rendered {



            line-height: 50px !important;



            padding-left: 15px !important;



            padding-right: 40px !important;



            color: #212529 !important;



            font-size: 1rem;



        }





        /* Placeholder */



        .select2-container--default .select2-selection--single .select2-selection__placeholder {



            color: #6c757d !important;



        }





        /* Flecha */



        .select2-container--default .select2-selection--single .select2-selection__arrow {



            height: 50px !important;



            width: 38px !important;



            right: 4px !important;



        }





        /* Estado focus */



        .select2-container--default.select2-container--focus .select2-selection--single,



        .select2-container--default.select2-container--open .select2-selection--single {



            border-color: #86b7fe !important;



            box-shadow:

                0 0 0 .2rem rgba(13, 110, 253, .12) !important;



        }





        /* =========================================================

                                           DROPDOWN

                                           ========================================================= */



        .select2-dropdown {



            border: 1px solid #dee2e6 !important;



            border-radius: 12px !important;



            overflow: hidden;



            box-shadow:

                0 8px 24px rgba(0, 0, 0, .12) !important;



        }





        /* Caja de búsqueda */



        .select2-search--dropdown {



            padding: 10px !important;



            background: #f8f9fa;



            border-bottom: 1px solid #e9ecef;



        }





        .select2-search--dropdown .select2-search__field {



            height: 44px;



            border: 1px solid #dee2e6 !important;



            border-radius: 10px !important;



            padding: 8px 12px !important;



            font-size: 1rem;



            outline: none !important;



        }





        .select2-search--dropdown .select2-search__field:focus {



            border-color: #86b7fe !important;



            box-shadow:

                0 0 0 .15rem rgba(13, 110, 253, .10) !important;



        }





        /* =========================================================

                                           RESULTADOS

                                           ========================================================= */



        .select2-results__options {



            max-height: 280px !important;



        }





        .select2-results__option {



            padding: 11px 14px !important;



            font-size: .95rem;



            transition:

                background-color .12s ease;



        }





        /* Hover / seleccionado */



        .select2-container--default .select2-results__option--highlighted[aria-selected] {



            background-color: #0d6efd !important;



            color: #fff !important;



        }





        /* Opción seleccionada */



        .select2-container--default .select2-results__option[aria-selected="true"] {



            background-color: #eef4ff;



            color: #0d6efd;



            font-weight: 600;



        }





        /* =========================================================

                                           AGREGAR COLONIA

                                           ========================================================= */



        .select2-add-new-option {



            border-top: 1px solid #e9ecef;



            background: #f8fff9 !important;



        }





        .select2-add-option {



            display: flex;



            align-items: center;



            gap: 6px;



            color: #198754;



            font-weight: 600;



        }





        .select2-add-new-option:hover .select2-add-option {



            color: #146c43;



        }





        /* =========================================================

                                           MENSAJES

                                           ========================================================= */



        .select2-results__message {



            padding: 12px 14px !important;



            color: #6c757d;



        }





        /* =========================================================

                                           MOBILE

                                           ========================================================= */



        @media (max-width: 767.98px) {



            .select2-container--default .select2-selection--single {



                height: 52px !important;



            }





            .select2-container--default .select2-selection--single .select2-selection__rendered {



                font-size: 1rem;



                padding-left: 14px !important;



            }





            .select2-search--dropdown .select2-search__field {



                height: 48px;



                font-size: 16px !important;



            }





            .select2-results__option {



                min-height: 48px;



                display: flex;



                align-items: center;



            }



        }
    </style>
@endpush
