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
        @slot('title') Back Offices / Jefes de Equipo @endslot
    @endcomponent

    <x-select-section :idForm="'form_campaign'" :idSelect="'id_campaign'" :nameLabel="'Seleccionar Campaña'" :options="$campaigns"></x-select-section>

    @if ($id)
        <x-table :idCreateButton="'newEditBack'" :idModal="'editBack'" :textButton="'Crear Back office / Jefe de campo'" :headers="['ID','NOMBRE','CAMPAÑA','ESTADO','OPCIONES']">
            @foreach ($backs as $back)
                <tr data-id="{{ $back->id }}">
                    <td data-field="id">
                        <div data-back-id="{{ $back->id }}">
                            {{ $back->user_id }}
                        </div>
                    </td>
                    <td data-field="nombre">
                        <div data-back-id="{{ $back->id }}">
                            {{ $back->user_name }}
                        </div>
                    </td>
                    <td data-field="campaña">
                        <div data-back-id="{{ $back->id }}">
                            {{ $back->camp_name }}
                        </div>
                    </td>
                    <td data-field="estado">
                        <div data-back-id="{{ $back->id }}">
                            <input type="checkbox" class="switch" id="switch-{{ $back->id }}" switch="bool" data-back-id="{{ $back->id }}" {{ $back->state == 1 ? 'checked' : '' }} />
                            <label for="switch-{{ $back->id }}" data-on-label="On" data-off-label="Off"></label>
                        </div>
                    </td>
                    <td style="width: 100px" data-field="opciones">
                        <button type="button" class="btn btn-outline-info btn-sm edit" title="Edit" data-back-id="{{ $back->id }}" data-bs-toggle="modal" data-bs-target="#editField">
                            <i class="fas fa-pencil-alt"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm delete" title="Delete" data-back-id="{{ $back->id }}">
                            <i class="fas uil-trash-alt"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        </x-table>
    @endif

    <x-modal :idModal="'editBack'" :ariaLabelledby="'editBack'" :routeAction="route('SaveBack')" :idTitle="'editBackTitle'" :textTitle="'Crear Back office / Jefe de Equipo'">
        <input type="text" class="form-control" id="campaign_id" name="campaign_id" style="display: none;">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="user_id">Back office / Jefe de equipo:</label>
                    <select class="form-select" id="user_id" name="user_id" required>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    <div class="valid-feedback">Valido!</div>
                    <div class="invalid-feedback">El back office / jefe de equipo es requerido.</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="sups">Supervisores:</label>
                    <select class="select2 form-control select2-multiple" id="sups" name="sups[]" style="width: 100%;" multiple="multiple" data-placeholder="Selecciona">
                        @foreach ($sups as $sup)
                            <option value="{{ $sup->id }}">{{ $sup->user_name }}</option>
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
        const backs = @json($backs);
        const supsInBacks = @json($supsInBacks);
        const titleEdit = $('#editBackTitle')[0];
		const forms = document.getElementsByClassName('needs-validation');

        if (id) {
            $('#id_campaign').val(id);
            $('#campaign_id').val(id);
        };

        function reinitData() {
            $('#editBack').removeClass('was-validated');
            $('#editBack').addClass('needs-validated');
            $('#id').val("");

            $('#user_id').val("0").trigger('change');

            $('#sups').val([]).trigger('change');

        };
        function loadData(ID) {
            $('#editBack').removeClass('was-validated');
            $('#editBack').addClass('needs-validated');
            let backData;

            const sups = supsInBacks.filter(i => i.back_id == +ID);

            for (let i = 0; i < backs.length; i++) {
                const back = backs[i];

                if (back.id == +ID) backData = back;
            };

            if (backData) {
                const {
                    id,
                    user_id,
                    user_name,
                    camp_name,
                } = backData;

                const supsIds = sups.map(i => i.sup_id);

                $('#id').val(id);

                $('#user_id').val(user_id).trigger('change');

                $('#sups').val(supsIds).trigger('change');
            };
        };

        $(document).ready(function() {

            const table = $('#datatable').DataTable();

            $('#sups').select2({
                dropdownParent: $('#editBack')
            });

            $('#newEditBack').on('click', '', function () {
                reinitData();
                titleEdit.innerText = "Nuevo Back office / Jefe de equipo";
            });

            $('#editBack').on('click', 'btn-close', function () {
                reinitData();
            });

            $('#datatable tbody').on('click', '.btn.edit', function () {
                titleEdit.innerText = "Editar Back office / Jefe de equipo";

                reinitData();

                const backId = this.dataset.backId;

                loadData(backId);

                $('#editBack').modal('show');
            });

            $('#datatable tbody').on('dblclick', 'tr td div', function () {
                reinitData();

                const backId = this.dataset.backId;

                loadData(backId);

                $('#editBack').modal('show');
            });

            $('#datatable tbody').on('click', '.btn.delete', function () {
                const backId = this.dataset.backId;

                Swal.fire({
                    title: '¿Eliminar?',
                    text: "Estas seguro de eliminar este Back office / Jefe de equipo",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Eliminar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonClass: 'btn btn-success mt-2',
                    cancelButtonClass: 'btn btn-danger ms-2 mt-2',
                    buttonsStyling: false
                }).then(function (result) {
                    if (result.value) {
                        fetch(`{{ route('DeleteBack', '') }}/${backId}`, {
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
                                text: 'Back office / Jefe de equipo eliminado.',
                                icon: 'success',
                                confirmButtonColor: "#34c38f"
                            }).then(function () {
                                window.location.reload();
                            })
                        })
                        .catch(error => {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Ocurrio un error al intentar eliminar el Back office / Jefe de equipo.',
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

                window.location.assign(`{{ url('sales/backs/${campaignId}') }}`);
            });

            $('#datatable input.switch').on('change', function (e) {
                const val = this.checked;
                const backId = this.dataset.backId;

                if (val) {
                    fetch(`{{ route('AllowBack', '') }}/${backId}`, {
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
                    fetch(`{{ route('DisallowBack', '') }}/${backId}`, {
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