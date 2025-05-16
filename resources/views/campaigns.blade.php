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
        @slot('title') Campañas @endslot
    @endcomponent

    <x-table :idCreateButton="'newEditCampaign'" :idModal="'editCampaign'" :textButton="'Crear Campaña'" :headers="['ID','NOMBRE','ESTADO','PARÁMETROS','OPCIONES']">
        @foreach ($campaigns as $campaign)
            <tr data-id="{{ $campaign->id }}">
                <td data-field="id">
                    <div data-campaign-id="{{ $campaign->id }}">
                        {{ $campaign->id }}
                    </div>
                </td>
                <td data-field="nombre">
                    <div data-campaign-id="{{ $campaign->id }}">
                        {{ $campaign->name }}
                    </div>
                </td>
                <td data-field="estado">
                    <div data-campaign-id="{{ $campaign->id }}">
                        <input type="checkbox" class="switch" id="switch-{{ $campaign->id }}" switch="bool" data-campaign-id="{{ $campaign->id }}" {{ $campaign->state == "1" ? 'checked' : '' }} />
                        <label for="switch-{{ $campaign->id }}" data-on-label="On" data-off-label="Off"></label>
                    </div>
                </td>
                <td data-field="parametros">
                    <div data-campaign-id="{{ $campaign->id }}">
                        <a href="{{ url('sales/tab-states', $campaign->id) }}" class="py-px px-1 text-2xs rounded-1 no-underline" style="background-color: rgb(251, 85, 151); color: rgb(255, 255, 255);">Pestañas de Estados</a>
                        <a href="{{ url('sales/states', $campaign->id) }}" class="py-px px-1 text-2xs rounded-1 no-underline" style="background-color: rgb(255, 91, 87); color: rgb(255, 255, 255);">Estados</a>
                        <a href="{{ url('sales/blocks', $campaign->id) }}" class="py-px px-1 text-2xs rounded-1 no-underline" style="background-color: rgb(144, 202, 75); color: rgb(255, 255, 255);">Bloque de Campos</a>
                        <a href="{{ url('sales/fields', $campaign->id) }}" class="py-px px-1 text-2xs rounded-1 no-underline" style="background-color: rgb(50, 169, 50); color: rgb(255, 255, 255);">Campos</a>
                        <a href="#" class="py-px px-1 text-2xs rounded-1 no-underline" style="background-color: rgb(114, 124, 182); color: rgb(255, 255, 255);">Categorías</a>
                        <a href="#" class="py-px px-1 text-2xs rounded-1 no-underline" style="background-color: rgb(135, 83, 222); color: rgb(255, 255, 255);">Productos</a>
                        <a href="#" class="py-px px-1 text-2xs rounded-1 no-underline" style="background-color: rgb(73, 182, 214); color: rgb(255, 255, 255);">Promociones</a>
                        <a href="{{ url('sales/sups', $campaign->id) }}" class="py-px px-1 text-2xs rounded-1 no-underline" style="background-color: rgb(255, 217, 0); color: rgb(255, 255, 255);">Supervisores</a>
                        <a href="{{ url('sales/agents', $campaign->id) }}" class="py-px px-1 text-2xs rounded-1 no-underline" style="background-color: rgb(245, 156, 26); color: rgb(255, 255, 255);">Agentes</a>
                        <a href="#" class="py-px px-1 text-2xs rounded-1 no-underline" style="background-color: rgb(57, 66, 74); color: rgb(255, 255, 255);">Listas</a>
                        <a href="#" class="py-px px-1 text-2xs rounded-1 no-underline" style="background-color: rgb(167, 182, 191); color: rgb(255, 255, 255);">Tipificaciones</a>
                    </div>
                </td>
                <td style="width: 100px">
                    <button type="button" class="btn btn-outline-info btn-sm edit" title="Edit" data-campaign-id="{{ $campaign->id }}" data-bs-toggle="modal" data-bs-target="#editCampaign">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm delete" title="Delete" data-campaign-id="{{ $campaign->id }}">
                        <i class="fas uil-trash-alt"></i>
                    </button>
                </td>
            </tr>
        @endforeach
    </x-table>

    <x-modal :idModal="'editCampaign'" :ariaLabelledby="'editCampaign'" :routeAction="route('SaveCampaign')" :idTitle="'editCampaignTitle'" :textTitle="'Crear Campaña'">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="name">Nombre:</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                    <div class="valid-feedback">Valido!</div>
                    <div class="invalid-feedback">El nombre es requerido.</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="country">País:</label>
                    <select class="form-select" id="country" name="country_id" required>
                        @foreach ($countries as $country)
                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                        @endforeach
                    </select>
                    <div class="valid-feedback">Valido!</div>
                    <div class="invalid-feedback">El país es requerido.</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3 align-items-center justify-content-between" style="display: flex;">
                    <label class="form-check-label" for="geolocation">Activar Geolocazación</label>
                    <input type="checkbox" id="geolocation" name="geolocation" switch="bool" />
                    <label class="mb-0" for="geolocation" data-on-label="On" data-off-label="Off"></label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3 align-items-center justify-content-between" style="display: flex;">
                    <label class="form-check-label" for="view_products">Ver Productos</label>
                    <input type="checkbox" id="view_products" name="view_products" switch="bool" />
                    <label class="mb-0" for="view_products" data-on-label="On" data-off-label="Off"></label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3 align-items-center justify-content-between" style="display: flex;">
                    <label class="form-check-label" for="back_state">Retroceder Estado</label>
                    <input type="checkbox" id="back_state" name="back_state" switch="bool" />
                    <label class="mb-0" for="back_state" data-on-label="On" data-off-label="Off"></label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3 align-items-center justify-content-between" style="display: flex;">
                    <label class="form-check-label" for="sold_new_window">Venta nueva en otra pestaña</label>
                    <input type="checkbox" id="sold_new_window" name="sold_new_window" switch="bool" />
                    <label class="mb-0" for="sold_new_window" data-on-label="On" data-off-label="Off"></label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3 align-items-center justify-content-between" style="display: flex;">
                    <label class="form-check-label" for="sold_exists_window">Venta existente en otra pestaña</label>
                    <input type="checkbox" id="sold_exists_window" name="sold_exists_window" switch="bool" />
                    <label class="mb-0" for="sold_exists_window" data-on-label="On" data-off-label="Off"></label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3 align-items-center justify-content-between" style="display: flex;">
                    <label class="form-check-label" for="sold_notes">Notas en la venta</label>
                    <input type="checkbox" id="sold_notes" name="sold_notes" switch="bool" />
                    <label class="mb-0" for="sold_notes" data-on-label="On" data-off-label="Off"></label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3 align-items-center justify-content-between" style="display: flex;">
                    <label class="form-check-label" for="list_sold_notes">Notas en el listado de Ventas</label>
                    <input type="checkbox" id="list_sold_notes" name="list_sold_notes" switch="bool" />
                    <label class="mb-0" for="list_sold_notes" data-on-label="On" data-off-label="Off"></label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3 align-items-center justify-content-between" style="display: flex;">
                    <label class="form-check-label" for="change_state_list_sold">Permitir cambiar de Estado</label>
                    <input type="checkbox" id="change_state_list_sold" name="change_state_list_sold" switch="bool" />
                    <label class="mb-0" for="change_state_list_sold" data-on-label="On" data-off-label="Off"></label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3 align-items-center justify-content-between" style="display: flex;">
                    <label class="form-check-label" for="option_duplicate_sold">Habilitar duplicar Venta</label>
                    <input type="checkbox" id="option_duplicate_sold" name="option_duplicate_sold" switch="bool" />
                    <label class="mb-0" for="option_duplicate_sold" data-on-label="On" data-off-label="Off"></label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3 align-items-center justify-content-between" style="display: flex;">
                    <label class="form-check-label" for="show_history_sold">Ver historial de Ventas</label>
                    <input type="checkbox" id="show_history_sold" name="show_history_sold" switch="bool" />
                    <label class="mb-0" for="show_history_sold" data-on-label="On" data-off-label="Off"></label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="range_date_id">Rango de fechas predeterminado en el listado de ventas:</label>
                    <select class="form-select" id="range_date_id" name="range_date_id" value="0">
                        <option value="0" selected>Seleccionar</option>
                        @foreach ($rangeDates as $rangeDate)
                            <option value="{{ $rangeDate->id }}">{{ $rangeDate->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3" style="display: flex; flex-direction: column;">
                    <label class="form-label" for="export_list_solds">Pueden exportar el listado de ventas:</label>
                    <select class="select2 form-control select2-multiple" id="export_list_solds" name="export_list_solds[]" multiple="multiple" data-placeholder="Selecciona">
                        <option value="0" selected>Seleccionar</option>
                        @foreach ($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3" style="display: flex; flex-direction: column;">
                    <label class="form-label" for="show_sold_edit">Pueden ver quién(es) ha(n) abierto una venta en modo edición:</label>
                    <select class="select2 form-control select2-multiple" id="show_sold_edit" name="show_sold_edit[]" multiple="multiple" data-placeholder="Selecciona">
                        <option value="0" selected>Seleccionar</option>
                        @foreach ($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3" style="display: flex; flex-direction: column;">
                    <label class="form-label" for="audit_data_sold">Pueden auditar los datos de trazabilidad de las ventas:</label>
                    <select class="select2 form-control select2-multiple" id="audit_data_sold" name="audit_data_sold[]" multiple="multiple" data-placeholder="Selecciona">
                        <option value="0" selected>Seleccionar</option>
                        @foreach ($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3" style="display: flex; flex-direction: column;">
                    <label class="form-label" for="charge_massive_sold">Pueden cargar masivamente ventas:</label>
                    <select class="select2 form-control select2-multiple" id="charge_massive_sold" name="charge_massive_sold[]" multiple="multiple" data-placeholder="Selecciona">
                        <option value="0" selected>Seleccionar</option>
                        @foreach ($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3" style="display: flex; flex-direction: column;">
                    <label class="form-label" for="autorize_duplicate_sold">Pueden autorizar ventas con valores duplicados:</label>
                    <select class="select2 form-control select2-multiple" id="autorize_duplicate_sold" name="autorize_duplicate_sold[]" multiple="multiple" data-placeholder="Selecciona">
                        <option value="0" selected>Seleccionar</option>
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
                    <label class="form-label" for="description">Descripción:</label>
                    <textarea class="form-control" id="description" name="description"></textarea>
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
        const campaigns = @json($campaigns);
        const groups = @json($groups);
        const countries = @json($countries);
        const rangeDates = @json($rangeDates);
        const groupsAuthorizeDuplicateSold = @json($groupsAuthorizeDuplicateSold);
        const groupsUploadMassiveSold = @json($groupsUploadMassiveSold);
        const groupsExportSold = @json($groupsExportSold);
        const groupsViewEdition = @json($groupsViewEdition);
        const groupsAuditDataSold = @json($groupsAuditDataSold);
        const titleEdit = $('#editCampaignTitle')[0];
		const forms = document.getElementsByClassName('needs-validation');

        function reinitData() {
            $('#editCampaign').removeClass('was-validated');
            $('#editCampaign').addClass('needs-validated');
            $('#id').val("");
            $('#name').val("");
            $('#country').val("0");
            $('#geolocation').prop("checked", 0);
            $('#view_products').prop("checked", 0);
            $('#back_state').prop("checked", 0);
            $('#sold_new_window').prop("checked", 0);
            $('#sold_exists_window').prop("checked", 0);
            $('#sold_notes').prop("checked", 0);
            $('#list_sold_notes').prop("checked", 0);
            $('#change_state_list_sold').prop("checked", 0);
            $('#option_duplicate_sold').prop("checked", 0);
            $('#show_history_sold').prop("checked", 0);
            $('#range_date_id').val("");
            $('#description').val("");
            $('#export_list_solds').val([]).trigger('change');
            $('#show_sold_edit').val([]).trigger('change');
            $('#audit_data_sold').val([]).trigger('change');
            $('#charge_massive_sold').val([]).trigger('change');
            $('#autorize_duplicate_sold').val([]).trigger('change');
        };
        function loadData(ID) {
            $('#editCampaign').removeClass('was-validated');
            $('#editCampaign').addClass('needs-validated');
            let campaignData;

            const exportSold = groupsExportSold.filter(i => i.campain_id == +ID);
            const viewEdition = groupsViewEdition.filter(i => i.campain_id == +ID);
            const auditDataSold = groupsAuditDataSold.filter(i => i.campain_id == +ID);
            const uploadMassiveSold = groupsUploadMassiveSold.filter(i => i.campain_id == +ID);
            const authorizeDuplicateSold = groupsAuthorizeDuplicateSold.filter(i => i.campain_id == +ID);

            for (let i = 0; i < campaigns.length; i++) {
                const campaign = campaigns[i];

                if (campaign.id == +ID) campaignData = campaign;
            };

            if (campaignData) {
                const {
                    id,
                    name,
                    country_id,
                    geolocation,
                    view_products,
                    back_state,
                    sold_new_window,
                    sold_exists_window,
                    sold_notes,
                    list_sold_notes,
                    change_state_list_sold,
                    option_duplicate_sold,
                    show_history_sold,
                    range_date_id,
                    description,
                } = campaignData;

                const exportSoldId = exportSold.map(i => i.group_id);
                const viewEditionId = viewEdition.map(i => i.group_id);
                const auditDataSoldId = auditDataSold.map(i => i.group_id);
                const uploadMassiveSoldId = uploadMassiveSold.map(i => i.group_id);
                const authorizeDuplicateSoldId = authorizeDuplicateSold.map(i => i.group_id);

                $('#id').val(id);
                $('#name').val(name);
                $('#country').val(country_id);
                $('#geolocation').prop("checked", geolocation);
                $('#view_products').prop("checked", view_products);
                $('#back_state').prop("checked", back_state);
                $('#sold_new_window').prop("checked", sold_new_window);
                $('#sold_exists_window').prop("checked", sold_exists_window);
                $('#sold_notes').prop("checked", sold_notes);
                $('#list_sold_notes').prop("checked", list_sold_notes);
                $('#change_state_list_sold').prop("checked", change_state_list_sold);
                $('#option_duplicate_sold').prop("checked", option_duplicate_sold);
                $('#show_history_sold').prop("checked", show_history_sold);
                $('#range_date_id').val(range_date_id);
                $('#description').val(description);
                $('#export_list_solds').val(exportSoldId).trigger('change');
                $('#show_sold_edit').val(viewEditionId).trigger('change');
                $('#audit_data_sold').val(auditDataSoldId).trigger('change');
                $('#charge_massive_sold').val(uploadMassiveSoldId).trigger('change');
                $('#autorize_duplicate_sold').val(authorizeDuplicateSoldId).trigger('change');
            };
        };

        $(document).ready(function() {
            const table = $('#datatable').DataTable();
            $('#export_list_solds').select2({
                dropdownParent: $('#editCampaign')
            });
            $('#show_sold_edit').select2({
                dropdownParent: $('#editCampaign')
            });
            $('#audit_data_sold').select2({
                dropdownParent: $('#editCampaign')
            });
            $('#charge_massive_sold').select2({
                dropdownParent: $('#editCampaign')
            });
            $('#autorize_duplicate_sold').select2({
                dropdownParent: $('#editCampaign')
            });

            $('#newEditCampaign').on('click', '', function () {
                reinitData();
                titleEdit.innerText = "Nueva Campaña";
            });

            $('#editCampaign').on('click', 'btn-close', function () {
                reinitData();
            });

            $('#datatable tbody').on('click', '.btn.edit', function () {
                titleEdit.innerText = "Editar Campaña";

                reinitData();

                const campaignId = this.dataset.campaignId;

                loadData(campaignId);

                $('#editCampaign').modal('show');
            });

            $('#datatable tbody').on('dblclick', 'tr td div', function () {
                reinitData();

                const campaignId = this.dataset.campaignId;

                loadData(campaignId);

                $('#editCampaign').modal('show');
            });

            $('#datatable tbody').on('click', '.btn.delete', function () {
                const campaignId = this.dataset.campaignId;

                Swal.fire({
                    title: '¿Eliminar?',
                    text: "Estas seguro de eliminar este Campaña",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Eliminar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonClass: 'btn btn-success mt-2',
                    cancelButtonClass: 'btn btn-danger ms-2 mt-2',
                    buttonsStyling: false
                }).then(function (result) {
                    if (result.value) {
                        fetch(`{{ route('DeleteCampaign', '') }}/${campaignId}`, {
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
                                text: error,
                                icon: 'error',
                                confirmButtonColor: "#34c38f"
                            });
                        });
                    };
                });
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

        $('#datatable input.switch').on('change', function (e) {
            const val = this.checked;
            const campaignId = this.dataset.campaignId;

            if (val) {
                fetch(`{{ route('AllowCampaign', '') }}/${campaignId}`, {
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
                fetch(`{{ route('DisallowCampaign', '') }}/${campaignId}`, {
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
    </script>
@endsection