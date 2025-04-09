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
        @slot('title') Pestañas de Estado @endslot
    @endcomponent

    <x-select-section :idForm="'form_campaign'" :idSelect="'id_campaign'" :nameLabel="'Seleccionar Campaña'" :options="$campaigns"></x-select-section>

    @if ($id)
        <x-table :idCreateButton="'newEditTabState'" :idModal="'editTabState'" :textButton="'Crear Pestaña de Estado'" :headers="['CAMPAÑA','NOMBRE','ORDEN','ESTADO','OPCIONES']">
            @foreach ($tabStates as $tabState)
                <tr data-id="{{ $tabState->id }}">
                    <td data-field="campaña">
                        <div data-tab-state-id="{{ $tabState->id }}">
                            {{ $tabState->campaign_name }}
                        </div>
                    </td>
                    <td data-field="nombre">
                        <div data-tab-state-id="{{ $tabState->id }}">
                            {{ $tabState->name }}
                        </div>
                    </td>
                    <td data-field="orden">
                        <div data-tab-state-id="{{ $tabState->id }}">
                            {{ $tabState->order }}
                        </div>
                    </td>
                    <td data-field="estado">
                        <div data-tab-state-id="{{ $tabState->id }}">
                            <input type="checkbox" class="switch" id="switch-{{ $tabState->id }}" switch="bool" data-tab-state-id="{{ $tabState->id }}" {{ $tabState->state == "1" ? 'checked' : '' }} />
                            <label for="switch-{{ $tabState->id }}" data-on-label="On" data-off-label="Off"></label>
                        </div>
                    </td>
                    <td style="width: 100px" data-field="opciones">
                        <button type="button" class="btn btn-outline-info btn-sm edit" title="Edit" data-tab-state-id="{{ $tabState->id }}" data-bs-toggle="modal" data-bs-target="#editCampaign">
                            <i class="fas fa-pencil-alt"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm delete" title="Delete" data-tab-state-id="{{ $tabState->id }}">
                            <i class="fas uil-trash-alt"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        </x-table>
    @endif

    <x-modal :idModal="'editTabState'" :ariaLabelledby="'editTabState'" :routeAction="route('SaveTabState')" :idTitle="'editTabStateTitle'" :textTitle="'Crear Pestaña de Estado'">
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
        const tabStates = @json($tabStates);
        const titleEdit = $('#editTabStateTitle')[0];
		const forms = document.getElementsByClassName('needs-validation');

        if (id) {
            $('#id_campaign').val(id);
            $('#campaign_id').val(id);
        };

        function reinitData() {
            $('#editTabState').removeClass('was-validated');
            $('#editTabState').addClass('needs-validated');
            $('#id').val("");
            $('#name').val("");
            $('#order').val("");
        };
        function loadData(ID) {
            $('#editTabState').removeClass('was-validated');
            $('#editTabState').addClass('needs-validated');
            let tabStateData;

            for (let i = 0; i < tabStates.length; i++) {
                const tabState = tabStates[i];

                if (tabState.id == +ID) tabStateData = tabState;
            };

            if (tabStateData) {
                const {
                    id,
                    name,
                    order,
                } = tabStateData;

                $('#id').val(id);
                $('#name').val(name);
                $('#order').val(order);
            };
        };

        $(document).ready(function() {

            const table = $('#datatable').DataTable();

            $('#newEditTabState').on('click', '', function () {
                reinitData();
                titleEdit.innerText = "Nueva Pestaña de Estado";
            });

            $('#editTabState').on('click', 'btn-close', function () {
                reinitData();
            });

            $('#datatable tbody').on('click', '.btn.edit', function () {
                titleEdit.innerText = "Editar Pestaña de Estado";

                reinitData();

                const tabStateId = this.dataset.tabStateId;

                loadData(tabStateId);

                $('#editTabState').modal('show');
            });

            $('#datatable tbody').on('dblclick', 'tr td div', function () {
                reinitData();

                const tabStateId = this.dataset.tabStateId;

                loadData(tabStateId);

                $('#editTabState').modal('show');
            });

            $('#datatable tbody').on('click', '.btn.delete', function () {
                const tabStateId = this.dataset.tabStateId;

                Swal.fire({
                    title: '¿Eliminar?',
                    text: "Estas seguro de eliminar esta Pestaña de Estado",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Eliminar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonClass: 'btn btn-success mt-2',
                    cancelButtonClass: 'btn btn-danger ms-2 mt-2',
                    buttonsStyling: false
                }).then(function (result) {
                    if (result.value) {
                        fetch(`{{ route('DeleteTabState', '') }}/${tabStateId}`, {
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
                                text: 'Ocurrio un error al intentar eliminar la Pestaña de Estado.',
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

                window.location.assign(`{{ url('sales/tab-states/${campaignId}') }}`);
            });

            $('#datatable input.switch').on('change', function (e) {
                const val = this.checked;
                const tabStateId = this.dataset.tabStateId;

                if (val) {
                    fetch(`{{ route('AllowTabState', '') }}/${tabStateId}`, {
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
                    fetch(`{{ route('DisallowTabState', '') }}/${tabStateId}`, {
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