@extends('layouts.master')
@section('title')
    @lang('translation.Editable_Table')
@endsection
@section('css')
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('common-components.breadcrumb')
        @slot('pagetitle')
            Configuración de Campañas
        @endslot
        @slot('title')
            Estados
        @endslot
    @endcomponent

    <x-select-section :idForm="'form_campaign'" :idSelect="'id_campaign'" :nameLabel="'Seleccionar Campaña'" :options="$campaigns"></x-select-section>

    @if ($id)
        <x-table :idCreateButton="'newEditState'" :idModal="'editState'" :textButton="'Crear Estado'" :headers="[
            'CAMPAÑA',
            'NOMBRE',
            'PESTAÑA',
            'ORDEN',
            'COLOR',
            'RESALTAR EN NOTIFICACIONES',
            'ESTADO ES AGENDADO',
            'ESTADO ES COMISIONABLE',
            'ESTADO',
            'OPCIONES',
        ]">
            @foreach ($states as $state)
                <tr data-id="{{ $state->id }}">
                    <td data-field="campaña">
                        <div data-state-id="{{ $state->id }}">
                            {{ $state->campaign_name }}
                        </div>
                    </td>
                    <td data-field="nombre">
                        <div data-state-id="{{ $state->id }}">
                            {{ $state->name }}
                        </div>
                    </td>
                    <td data-field="pestaña">
                        <div data-state-id="{{ $state->id }}">
                            {{ $state->tab_state_name }}
                        </div>
                    </td>
                    <td data-field="orden">
                        <div data-state-id="{{ $state->id }}">
                            {{ $state->order }}
                        </div>
                    </td>
                    <td data-field="color">
                        <div data-state-id="{{ $state->id }}">
                            <div style="width: 30px; height: 30px; background-color: {{ $state->color }}; border: 1px solid #ccc; border-radius: 40px;"></div>
                        </div>
                    </td>
                    <td data-field="resaltar en notificaciones">
                        <div data-state-id="{{ $state->id }}">
                            {{ $state->not }}
                        </div>
                    </td>
                    <td data-field="estado es agendado">
                        <div data-state-id="{{ $state->id }}">
                            {{ $state->age }}
                        </div>
                    </td>
                    <td data-field="estado es comisionable">
                        <div data-state-id="{{ $state->id }}">
                            {{ $state->com }}
                        </div>
                    </td>
                    <td data-field="estado">
                        <div data-state-id="{{ $state->id }}">
                            <input type="checkbox" id="switch-{{ $state->id }}" switch="bool"
                                {{ $state->state == '1' ? 'checked' : '' }} />
                            <label for="switch-{{ $state->id }}" data-on-label="On" data-off-label="Off"></label>
                        </div>
                    </td>
                    <td style="width: 100px" data-field="opciones">
                        <button type="button" class="btn btn-outline-info btn-sm edit" title="Edit"
                            data-state-id="{{ $state->id }}" data-bs-toggle="modal" data-bs-target="#editState">
                            <i class="fas fa-pencil-alt"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm delete" title="Delete"
                            data-state-id="{{ $state->id }}">
                            <i class="fas uil-trash-alt"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        </x-table>
    @endif

    <x-modal :idModal="'editState'" :ariaLabelledby="'editState'" :routeAction="route('SaveState')" :idTitle="'editStateTitle'" :textTitle="'Crear Estado'">
        <input type="text" class="form-control" id="campaign_id" name="campaign_id" style="display: none;">
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="name">Nombre:</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                    <div class="valid-feedback">Valido!</div>
                    <div class="invalid-feedback">El nombre es requerido.</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="tab_state_id">Pestaña de Estado:</label>
                    <select class="form-select" id="tab_state_id" name="tab_state_id" required>
                        @foreach ($tabStates as $tabState)
                            <option value="{{ $tabState->id }}">{{ $tabState->name }}</option>
                        @endforeach
                    </select>
                    <div class="valid-feedback">Valido!</div>
                    <div class="invalid-feedback">La pestaña de estado es requerida.</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="color">Color:</label>
                    <input type="color" class="form-control form-control-color w-full" id="color" name="color"
                        style="width: 100%;" required>
                    <div class="valid-feedback">Valido!</div>
                    <div class="invalid-feedback">El color es requerido.</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="order">Orden:</label>
                    <input type="text" class="form-control" id="order" name="order" required>
                    <div class="valid-feedback">Valido!</div>
                    <div class="invalid-feedback">El orden es requerido.</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="state_user">Estados usado para:</label>
                    <select class="form-select" id="state_user" name="state_user" value="0" required>
                        <option value="0" selected>Crear Ventas</option>
                        <option value="1">Editar Ventas</option>
                        <option value="2">Crear y Editar Ventas</option>
                    </select>
                    <div class="valid-feedback">Valido!</div>
                    <div class="invalid-feedback">La pestaña de estado es requerida.</div>
                </div>
            </div>
        </div>
        <div class="row" id="row-states">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="states">Estados sobre los que se va a cambiar la venta a este
                        estado:</label>
                    <select class="select2 form-control select2-multiple" id="states" name="states[]"
                        style="width: 100%;" multiple="multiple" data-placeholder="Selecciona">
                        <option value="0" selected>Seleccionar</option>
                        @foreach ($states as $state)
                            <option value="{{ $state->id }}">{{ $state->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="row" id="row-grupos">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="grupos">Grupos con permisos de visualizar los estados:</label>
                    <select class="select2 form-control select2-multiple" id="grupos" name="grupos[]"
                        style="width: 100%;" multiple="multiple" data-placeholder="Selecciona">
                        <option value="0" selected>Seleccionar</option>
                        @foreach ($grupos as $grupo)
                            <option value="{{ $grupo->id }}">{{ $grupo->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3" style="display: flex; align-items: center; justify-content: start; column-gap: 4px;">
                    <input type="checkbox" switch="bool" id="not" name="not" />
                    <label for="not" class="mb-0" data-on-label="On" data-off-label="Off"></label>
                    <label class="form-check-label" for="not">Resaltar en notificaciones</label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3" style="display: flex; align-items: center; justify-content: start; column-gap: 4px;">
                    <input type="checkbox" switch="bool" id="age" name="age" />
                    <label for="age" class="mb-0" data-on-label="On" data-off-label="Off"></label>
                    <label class="form-check-label" for="age">Estado es agendado</label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3" style="display: flex; align-items: center; justify-content: start; column-gap: 4px;">
                    <input type="checkbox" switch="bool" id="com" name="com" />
                    <label for="com" class="mb-0" data-on-label="On" data-off-label="Off"></label>
                    <label class="form-check-label" for="com">Estado es comisionable</label>
                </div>
            </div>
        </div>
    </x-modal>

@endsection
@section('script')
    <script src="{{ URL::asset('/assets/libs/parsleyjs/parsleyjs.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/ckeditor/ckeditor.min.js') }}"></script>
    <script>
        const id = @json($id);
        const campaigns = @json($campaigns);
        const states = @json($states);
        const grupos = @json($grupos);
        const permisos = @json($permisos);
        const tabStates = @json($tabStates);
        const titleEdit = $('#editStateTitle')[0];
        const stateStates = @json($stateStates);
        const forms = document.getElementsByClassName('needs-validation');

        if (id) {
            $('#id_campaign').val(id);
            $('#campaign_id').val(id);
        };

        function reinitData() {
            $('#editState').removeClass('was-validated');
            $('#editState').addClass('needs-validated');
            $('#id').val("");
            $('#name').val("");
            $('#color').val("");
            $('#order').val("");
            $('#tab_state_id').val("");

            $('#state_user').val("null").change();

            $('#not').prop("checked", 0);
            $('#age').prop("checked", 0);
            $('#com').prop("checked", 0);
            $('#states').val([]).trigger('change');
            $('#grupos').val([]).trigger('change');
        };

        function loadData(ID) {
            $('#editState').removeClass('was-validated');
            $('#editState').addClass('needs-validated');
            let stateData;

            const groupStates = stateStates.filter(i => i.from_state_id == +ID);
            const groupStates_2 = permisos.filter(i => i.state_id == +ID);
            // alert(+ID);
            //alert(JSON.stringify(groupStates_2, null, 2));


            for (let i = 0; i < states.length; i++) {
                const state = states[i];

                if (state.id == +ID) stateData = state;
            };

            if (stateData) {
                const {
                    id,
                    name,
                    order,
                    tab_state_id,
                    age,
                    campaign_id,
                    campaign_name,
                    color,
                    com,
                    not,
                    state,
                    tab_state_name,
                    state_user
                } = stateData;

                const statesID = groupStates.map(i => i.to_state_id);
                const gruposID = groupStates_2.map(i => i.group_id);


                $('#id').val(id);
                $('#name').val(name);
                $('#color').val(color);
                $('#order').val(order);
                $('#tab_state_id').val(tab_state_id);

                $('#state_user').val(state_user).change();

                if (not == "on") $('#not').prop("checked", 1);
                if (age == "on") $('#age').prop("checked", 1);
                if (com == "on") $('#com').prop("checked", 1);

                $('#states').val(statesID).trigger('change');
                $('#grupos').val(gruposID).trigger('change');
            };
        };

        $(document).ready(function() {

            const table = $('#datatable').DataTable();

            $('#states').select2({
                dropdownParent: $('#editState')
            });

            $('#grupos').select2({
                dropdownParent: $('#editState')
            });

            $('#state_user').on('change', function(e) {
                const val = e.target.value;

                if (val == "1" || val == "2") {
                    $('#row-states').css('display', 'flex');
                    $('#row-grupos').css('display', 'flex');
                } else {
                    $('#row-states').css('display', 'none');
                    $('#row-grupos').css('display', 'none');
                };
            });

            $('#newEditState').on('click', '', function() {
                reinitData();
                titleEdit.innerText = "Nuevo Estado";
            });

            $('#editState').on('click', 'btn-close', function() {
                reinitData();
            });

            $('#datatable tbody').on('click', '.btn.edit', function() {
                titleEdit.innerText = "Editar Estado";

                reinitData();

                const stateId = this.dataset.stateId;

                loadData(stateId);

                $('#editState').modal('show');
            });

            $('#datatable tbody').on('dblclick', 'tr td div', function() {
                reinitData();

                const stateId = this.dataset.stateId;

                loadData(stateId);

                $('#editState').modal('show');
            });

            $('#datatable tbody').on('click', '.btn.delete', function() {
                const stateId = this.dataset.stateId;

                Swal.fire({
                    title: '¿Eliminar?',
                    text: "Estas seguro de eliminar este Estado",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Eliminar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonClass: 'btn btn-success mt-2',
                    cancelButtonClass: 'btn btn-danger ms-2 mt-2',
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.value) {
                        fetch(`{{ route('DeleteState', '') }}/${stateId}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json',
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                const {
                                    error
                                } = data;

                                if (error) {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: error,
                                        icon: 'error',
                                        confirmButtonColor: "#34c38f"
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Eliminado!',
                                        text: 'Campaña eliminado.',
                                        icon: 'success',
                                        confirmButtonColor: "#34c38f"
                                    }).then(function() {
                                        window.location.reload();
                                    })
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    title: 'Error!',
                                    text: error.error,
                                    icon: 'error',
                                    confirmButtonColor: "#34c38f"
                                });
                            });
                    };
                });
            });

            $('#form_campaign').on('submit', '', function(e) {
                e.preventDefault();

                const campaignId = $('#id_campaign').val();

                window.location.assign(`{{ url('sales/states/${campaignId}') }}`);
            });
            Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        });
    </script>
@endsection
