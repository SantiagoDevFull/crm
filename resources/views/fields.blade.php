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
        @slot('pagetitle') Configuración de Campañas @endslot
        @slot('title') Campos @endslot
    @endcomponent

    <x-select-section :idForm="'form_campaign'" :idSelect="'id_campaign'" :nameLabel="'Seleccionar Campaña'" :options="$campaigns"></x-select-section>

    @if ($id)
        <x-table :idCreateButton="'newEditField'" :idModal="'editField'" :textButton="'Crear Campo'" :headers="['CAMPAÑA','BLOQUE DE CAMPOS','NOMBRE','ORDEN','ESTADO','OPCIONES']">
            @foreach ($fields as $field)
                <tr data-id="{{ $field->id }}">
                    <td data-field="campaña">
                        <div data-field-id="{{ $field->id }}">
                            {{ $field->campaign_name }}
                        </div>
                    </td>
                    <td data-field="bloque de campos">
                        <div data-field-id="{{ $field->id }}">
                            {{ $field->block_name }}
                        </div>
                    </td>
                    <td data-field="nombre">
                        <div data-field-id="{{ $field->id }}">
                            {{ $field->name }}
                        </div>
                    </td>
                    <td data-field="orden">
                        <div data-field-id="{{ $field->id }}">
                            {{ $field->order }}
                        </div>
                    </td>
                    <td data-field="estado">
                        <div data-field-id="{{ $field->id }}">
                            <input type="checkbox" class="switch" id="switch-{{ $field->id }}" switch="bool" data-field-id="{{ $field->id }}" {{ $field->state == "1" ? 'checked' : '' }} />
                            <label for="switch-{{ $field->id }}" data-on-label="On" data-off-label="Off"></label>
                        </div>
                    </td>
                    <td style="width: 100px" data-field="opciones">
                        <button type="button" class="btn btn-outline-info btn-sm edit" title="Edit" data-field-id="{{ $field->id }}" data-bs-toggle="modal" data-bs-target="#editField">
                            <i class="fas fa-pencil-alt"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm delete" title="Delete" data-field-id="{{ $field->id }}">
                            <i class="fas uil-trash-alt"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        </x-table>
    @endif

    <x-modal :idModal="'editField'" :ariaLabelledby="'editField'" :routeAction="route('SaveField')" :idTitle="'editFieldTitle'" :textTitle="'Crear Campo'">
        <input type="text" class="form-control" id="campaign_id" name="campaign_id" style="display: none;">
        <div class="row">
            <div class="col-md-8">
                <div class="mb-3">
                    <label class="form-label" for="name">Nombre:</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                    <div class="valid-feedback">Valido!</div>
                    <div class="invalid-feedback">El nombre es requerido.</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label" for="order">Orden:</label>
                    <input type="text" class="form-control" id="order" name="order" required>
                    <div class="valid-feedback">Valido!</div>
                    <div class="invalid-feedback">El orden es requerido.</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="block_id">Bloque de Campos:</label>
                    <select class="form-select" id="block_id" name="block_id" value="0" required>
                        <option value="0" selected>Seleccionar</option>
                        @foreach ($blocks as $block)
                            <option value="{{ $block->id }}">{{ $block->name }}</option>
                        @endforeach
                    </select>
                    <div class="valid-feedback">Valido!</div>
                    <div class="invalid-feedback">El bloque de campos es requerido.</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="type_field_id">Tipo de Campo:</label>
                    <select class="form-select" id="type_field_id" name="type_field_id" value="0" required>
                        <option value="0" selected>Seleccionar</option>
                        @foreach ($type_fields as $type_field)
                            <option value="{{ $type_field->id }}">{{ $type_field->name }}</option>
                        @endforeach
                    </select>
                    <div class="valid-feedback">Valido!</div>
                    <div class="invalid-feedback">El tipo de campo es requerido.</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="width_id">Ancho del Campo:</label>
                    <select class="form-select" id="width_id" name="width_id" value="0" required>
                        <option value="0" selected>Seleccionar</option>
                        @foreach ($widths as $width)
                            <option value="{{ $width->id }}">{{ $width->col }}/12 | {{ number_format(($width->col / 12) * 100, 2) }}%</option>
                        @endforeach
                    </select>
                    <div class="valid-feedback">Valido!</div>
                    <div class="invalid-feedback">El ancho es requerido.</div>
                </div>
            </div>
            <div class="col-md-6" id="options-section" style="display: none;">
                <div class="mb-3">
                    <label class="form-label" for="options">Opciones:</label>
                    <textarea class="form-control" id="options" name="options"></textarea>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="tab_states">Pestaña de Estados en los que va a aparecer este campo:</label>
                    <select class="select2 form-control select2-multiple" id="tab_states" name="tab_states[]" style="width: 100%;" multiple="multiple" data-placeholder="Selecciona">
                        @foreach ($tab_states as $tab_state)
                            <option value="{{ $tab_state->id }}">{{ $tab_state->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="edits">Pueden acceder a este campo [edición]:</label>
                    <select class="select2 form-control select2-multiple" id="edits" name="edits[]" style="width: 100%;" multiple="multiple" data-placeholder="Selecciona">
                        @foreach ($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="views">Pueden acceder a este campo [solo visualización]:</label>
                    <select class="select2 form-control select2-multiple" id="views" name="views[]" style="width: 100%;" multiple="multiple" data-placeholder="Selecciona">
                        @foreach ($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="comments">Pueden agregar comentarios sobre este campo:</label>
                    <select class="select2 form-control select2-multiple" id="comments" name="comments[]" style="width: 100%;" multiple="multiple" data-placeholder="Selecciona">
                        @foreach ($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3" style="display: flex; align-items: center; justify-content: start; column-gap: 4px;">
                    <input type="checkbox" switch="bool" id="required" name="required" />
                    <label for="required" class="mb-0" data-on-label="On" data-off-label="Off"></label>
                    <label class="form-check-label" for="required">Campo Obligatorio</label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3" style="display: flex; align-items: center; justify-content: start; column-gap: 4px;">
                    <input type="checkbox" switch="bool" id="unique" name="unique" />
                    <label for="unique" class="mb-0" data-on-label="On" data-off-label="Off"></label>
                    <label class="form-check-label" for="unique">Campo Único</label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3" style="display: flex; align-items: center; justify-content: start; column-gap: 4px;">
                    <input type="checkbox" switch="bool" id="bloq_mayus" name="bloq_mayus" />
                    <label for="bloq_mayus" class="mb-0" data-on-label="On" data-off-label="Off"></label>
                    <label class="form-check-label" for="bloq_mayus">Campo es en Mayúsculas</label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3" style="display: flex; align-items: center; justify-content: start; column-gap: 4px;">
                    <input type="checkbox" switch="bool" id="in_solds_list" name="in_solds_list" />
                    <label for="in_solds_list" class="mb-0" data-on-label="On" data-off-label="Off"></label>
                    <label class="form-check-label" for="in_solds_list">Agregar campo al listado de Ventas</label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3" style="display: flex; align-items: center; justify-content: start; column-gap: 4px;">
                    <input type="checkbox" switch="bool" id="in_notifications" name="in_notifications" />
                    <label for="in_notifications" class="mb-0" data-on-label="On" data-off-label="Off"></label>
                    <label class="form-check-label" for="in_notifications">Agregar campo a las notificaciones</label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3" style="display: flex; align-items: center; justify-content: start; column-gap: 4px;">
                    <input type="checkbox" switch="bool" id="in_general_search" name="in_general_search" />
                    <label for="in_general_search" class="mb-0" data-on-label="On" data-off-label="Off"></label>
                    <label class="form-check-label" for="in_general_search">Agregar campo en el buscador general</label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3" style="display: flex; align-items: center; justify-content: start; column-gap: 4px;">
                    <input type="checkbox" switch="bool" id="has_edit" name="has_edit" />
                    <label for="has_edit" class="mb-0" data-on-label="On" data-off-label="Off"></label>
                    <label class="form-check-label" for="has_edit">Puede editarse en el listado de ventas</label>
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
        const blocks = @json($blocks);
        const fields = @json($fields);
        const type_fields = @json($type_fields);
        const widths = @json($widths);
        const groups = @json($groups);
        const tab_states = @json($tab_states);
        const states = @json($states);
        const groupFieldEdit = @json($groupFieldEdit);
        const groupFieldView = @json($groupFieldView);
        const groupFieldHaveComment = @json($groupFieldHaveComment);
        const tabStateField = @json($tabStateField);
        const titleEdit = $('#editFieldTitle')[0];
		const forms = document.getElementsByClassName('needs-validation');

        if (id) {
            $('#id_campaign').val(id);
            $('#campaign_id').val(id);
        };

        function reinitData() {
            $('#editField').removeClass('was-validated');
            $('#editField').addClass('needs-validated');
            $('#id').val("");
            $('#name').val("");
            $('#order').val("");
            $('#block_id').val("");
            $('#type_field_id').val("");
            $('#width_id').val("");

            $('#tab_states').val([]).trigger('change');
            $('#edits').val([]).trigger('change');
            $('#views').val([]).trigger('change');
            $('#comments').val([]).trigger('change');

            $('#required').prop("checked", 0);
            $('#unique').prop("checked", 0);
            $('#bloq_mayus').prop("checked", 0);
            $('#in_solds_list').prop("checked", 0);
            $('#in_notifications').prop("checked", 0);
            $('#in_general_search').prop("checked", 0);
            $('#has_edit').prop("checked", 0);
        };
        function loadData(ID) {
            $('#editField').removeClass('was-validated');
            $('#editField').addClass('needs-validated');
            let fieldData;

            const fieldEdit = groupFieldEdit.filter(i => i.field_id == +ID);
            const fieldView = groupFieldView.filter(i => i.field_id == +ID);
            const fieldHaveComment = groupFieldHaveComment.filter(i => i.field_id == +ID);
            const tabState = tabStateField.filter(i => i.field_id == +ID);

            for (let i = 0; i < fields.length; i++) {
                const field = fields[i];

                if (field.id == +ID) fieldData = field;
            };

            if (fieldData) {
               
                const {
                    id,
                    name,
                    order,
                    block_id,
                    type_field_id,
                    width_col,
                    required,
                    unique,
                    bloq_mayus,
                    in_solds_list,
                    in_notifications,
                    in_general_search,
                    has_edit,
                } = fieldData;

                const fieldEditIds = fieldEdit.map(i => i.group_id);
                const fieldViewIds = fieldView.map(i => i.group_id);
                const fieldHaveCommentIds = fieldHaveComment.map(i => i.group_id);
                const tabStateIds = tabState.map(i => i.tab_state_id);

                $('#id').val(id);
                $('#name').val(name);
                $('#order').val(order);
                $('#block_id').val(block_id);
                $('#type_field_id').val(type_field_id);
                $('#width_id').val(width_col);

                $('#tab_states').val(tabStateIds).trigger('change');
                $('#edits').val(fieldEditIds).trigger('change');
                $('#views').val(fieldViewIds).trigger('change');
                $('#comments').val(fieldHaveCommentIds).trigger('change');

                $('#required').prop("checked", required);
                $('#unique').prop("checked", unique);
                $('#bloq_mayus').prop("checked", bloq_mayus);
                $('#in_solds_list').prop("checked", in_solds_list);
                $('#in_notifications').prop("checked", in_notifications);
                $('#in_general_search').prop("checked", in_general_search);
                $('#has_edit').prop("checked", has_edit);
            };
        };

        $(document).ready(function() {

            const table = $('#datatable').DataTable();

            $('#tab_states').select2({
                dropdownParent: $('#editField')
            });
            $('#edits').select2({
                dropdownParent: $('#editField')
            });
            $('#views').select2({
                dropdownParent: $('#editField')
            });
            $('#comments').select2({
                dropdownParent: $('#editField')
            });

            $('#type_field_id').on('change', function (e) {
                const val = e.target.value;

                if (val == "3" || val == "4") {
                    $('#options-section').css('display','flex');
                } else {
                    $('#options-section').css('display','none');
                };
            });

            $('#newEditField').on('click', '', function () {
                reinitData();
                titleEdit.innerText = "Nuevo Campo";
            });

            $('#editField').on('click', 'btn-close', function () {
                reinitData();
            });

            $('#datatable tbody').on('click', '.btn.edit', function () {
                titleEdit.innerText = "Editar Campo";

                reinitData();

                const fieldId = this.dataset.fieldId;

                loadData(fieldId);

                $('#editField').modal('show');
            });

            $('#datatable tbody').on('dblclick', 'tr td div', function () {
                reinitData();

                const fieldId = this.dataset.fieldId;

                loadData(fieldId);

                $('#editField').modal('show');
            });

            $('#datatable tbody').on('click', '.btn.delete', function () {
                const fieldId = this.dataset.fieldId;

                Swal.fire({
                    title: '¿Eliminar?',
                    text: "Estas seguro de eliminar este Campo",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Eliminar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonClass: 'btn btn-success mt-2',
                    cancelButtonClass: 'btn btn-danger ms-2 mt-2',
                    buttonsStyling: false
                }).then(function (result) {
                    if (result.value) {
                        fetch(`{{ route('DeleteField', '') }}/${fieldId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            const { error } = data;

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
                                }).then(function () {
                                    window.location.reload();
                                })
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Ocurrio un error al intentar eliminar el Campo.',
                                icon: 'error',
                                confirmButtonColor: "#34c38f"
                            });
                        });
                    };
                });
            });

            $('#form_campaign').on('submit', '', function (e) {
                e.preventDefault();

                const campaignId = $('#id_campaign').val();

                window.location.assign(`{{ url('sales/fields/${campaignId}') }}`);
            });

            $('#datatable input.switch').on('change', function (e) {
                const val = this.checked;
                const fieldId = this.dataset.fieldId;

                if (val) {
                    fetch(`{{ route('AllowField', '') }}/${fieldId}`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log(data)
                    })
                    .catch(error => {
                        console.log(error)
                    });
                } else {
                    fetch(`{{ route('DisallowField', '') }}/${fieldId}`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log(data)
                    })
                    .catch(error => {
                        console.log(error)
                    });
                };
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