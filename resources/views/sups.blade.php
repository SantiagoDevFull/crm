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
        @slot('title') Supervisores @endslot
    @endcomponent

    <x-select-section :idForm="'form_campaign'" :idSelect="'id_campaign'" :nameLabel="'Seleccionar Campaña'" :options="$campaigns"></x-select-section>

    @if ($id)
        <x-table :idCreateButton="'newEditSup'" :idModal="'editSup'" :textButton="'Crear Supervisor'" :headers="['ID','NOMBRE','CAMPAÑA','ESTADO','OPCIONES']">
            @foreach ($sups as $sup)
                <tr data-id="{{ $sup->id }}">
                    <td data-field="id">
                        <div data-sup-id="{{ $sup->id }}">
                            {{ $sup->user_id }}
                        </div>
                    </td>
                    <td data-field="nombre">
                        <div data-sup-id="{{ $sup->id }}">
                            {{ $sup->user_name }}
                        </div>
                    </td>
                    <td data-field="campaña">
                        <div data-sup-id="{{ $sup->id }}">
                            {{ $sup->camp_name }}
                        </div>
                    </td>
                    <td data-field="estado">
                        <div data-sup-id="{{ $sup->id }}">
                            <input type="checkbox" class="switch" id="switch-{{ $sup->id }}" switch="bool" data-sup-id="{{ $sup->id }}" {{ $sup->state == 1 ? 'checked' : '' }} />
                            <label for="switch-{{ $sup->id }}" data-on-label="On" data-off-label="Off"></label>
                        </div>
                    </td>
                    <td style="width: 100px" data-field="opciones">
                        <button type="button" class="btn btn-outline-info btn-sm edit" title="Edit" data-sup-id="{{ $sup->id }}" data-bs-toggle="modal" data-bs-target="#editField">
                            <i class="fas fa-pencil-alt"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm delete" title="Delete" data-sup-id="{{ $sup->id }}">
                            <i class="fas uil-trash-alt"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        </x-table>
    @endif

    <x-modal :idModal="'editSup'" :ariaLabelledby="'editSup'" :routeAction="route('SaveSup')" :idTitle="'editSupTitle'" :textTitle="'Crear Supervisor'">
        <input type="text" class="form-control" id="campaign_id" name="campaign_id" style="display: none;">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="user_id">Supervisor:</label>
                    <select class="form-select" id="user_id" name="user_id" required>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    <div class="valid-feedback">Valido!</div>
                    <div class="invalid-feedback">El supervisor es requerido.</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="agents">Agentes:</label>
                    <select class="select2 form-control select2-multiple" id="agents" name="agents[]" style="width: 100%;" multiple="multiple" data-placeholder="Selecciona">
                        @foreach ($agents as $agent)
                            <option value="{{ $agent->id }}">{{ $agent->user_name }}</option>
                        @endforeach
                    </select>
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
        const sups = @json($sups);
        const agentsInSups = @json($agentsInSups);
        const titleEdit = $('#editSupTitle')[0];
		const forms = document.getElementsByClassName('needs-validation');

        if (id) {
            $('#id_campaign').val(id);
            $('#campaign_id').val(id);
        };

        function reinitData() {
            $('#editSup').removeClass('was-validated');
            $('#editSup').addClass('needs-validated');
            $('#id').val("");

            $('#user_id').val("0").trigger('change');

            $('#agents').val([]).trigger('change');

        };
        function loadData(ID) {
            $('#editSup').removeClass('was-validated');
            $('#editSup').addClass('needs-validated');
            let supData;

            const agents = agentsInSups.filter(i => i.sup_id == +ID);

            for (let i = 0; i < sups.length; i++) {
                const sup = sups[i];

                if (sup.id == +ID) supData = sup;
            };

            if (supData) {
                const {
                    id,
                    user_id,
                    user_name,
                    camp_name,
                } = supData;

                const agentsIds = agents.map(i => i.sup_id);

                $('#id').val(id);

                $('#user_id').val(user_id).trigger('change');

                $('#agents').val(agentsIds).trigger('change');
            };
        };

        $(document).ready(function() {

            const table = $('#datatable').DataTable();

            $('#agents').select2({
                dropdownParent: $('#editSup')
            });

            $('#newEditSup').on('click', '', function () {
                reinitData();
                titleEdit.innerText = "Nuevo Supervisor";
            });

            $('#editSup').on('click', 'btn-close', function () {
                reinitData();
            });

            $('#datatable tbody').on('click', '.btn.edit', function () {
                titleEdit.innerText = "Editar Supervisor";

                reinitData();

                const supId = this.dataset.supId;

                loadData(supId);

                $('#editSup').modal('show');
            });

            $('#datatable tbody').on('dblclick', 'tr td div', function () {
                reinitData();

                const supId = this.dataset.supId;

                loadData(supId);

                $('#editSup').modal('show');
            });

            $('#datatable tbody').on('click', '.btn.delete', function () {
                const supId = this.dataset.supId;

                Swal.fire({
                    title: '¿Eliminar?',
                    text: "Estas seguro de eliminar este Supervisor",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Eliminar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonClass: 'btn btn-success mt-2',
                    cancelButtonClass: 'btn btn-danger ms-2 mt-2',
                    buttonsStyling: false
                }).then(function (result) {
                    if (result.value) {
                        fetch(`{{ route('DeleteSup', '') }}/${supId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            Swal.fire({
                                title: 'Eliminado!',
                                text: 'Supervisor eliminado.',
                                icon: 'success',
                                confirmButtonColor: "#34c38f"
                            }).then(function () {
                                window.location.reload();
                            })
                        })
                        .catch(error => {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Ocurrio un error al intentar eliminar el Supervisor.',
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

                window.location.assign(`{{ url('sales/sups/${campaignId}') }}`);
            });

            $('#datatable input.switch').on('change', function (e) {
                const val = this.checked;
                const supId = this.dataset.supId;

                if (val) {
                    fetch(`{{ route('AllowSup', '') }}/${supId}`, {
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
                    fetch(`{{ route('DisallowSup', '') }}/${supId}`, {
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