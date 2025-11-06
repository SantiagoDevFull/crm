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
        @slot('title') Agentes @endslot
    @endcomponent

    <x-select-section :idForm="'form_campaign'" :idSelect="'id_campaign'" :nameLabel="'Seleccionar Campaña'" :options="$campaigns"></x-select-section>

    @if ($id)
        <x-table :idCreateButton="'newEditAgent'" :idModal="'editAgent'" :textButton="'Crear Agente'" :headers="['SUPERVISOR','AGENTE','CAMPAÑA','ESTADO','OPCIONES']">
            @foreach ($agents as $agent)
                <tr data-id="{{ $agent->id }}">
                    
                    <td data-field="user_sup_name">
                        <div data-agent-id="{{ $agent->id }}">
                            {{ $agent->user_sup_name }}
                        </div>
                    </td>
                    <td data-field="user_agent_name">
                        <div data-agent-id="{{ $agent->id }}">
                            {{ $agent->user_agent_name }}
                        </div>
                    </td>
                    <td data-field="campaña">
                        <div data-agent-id="{{ $agent->id }}">
                            {{ $agent->camp_name }}
                        </div>
                    </td>
                    <td data-field="estado">
                        <div data-agent-id="{{ $agent->id }}">
                            <input type="checkbox" class="switch" id="switch-{{ $agent->id }}" switch="bool" data-agent-id="{{ $agent->id }}" {{ $agent->state == 1 ? 'checked' : '' }} />
                            <label for="switch-{{ $agent->id }}" data-on-label="On" data-off-label="Off"></label>
                        </div>
                    </td>
                    <td style="width: 100px" data-field="opciones">
                        <button type="button" class="btn btn-outline-info btn-sm edit" title="Edit" data-agent-id="{{ $agent->id }}" data-bs-toggle="modal" data-bs-target="#editField">
                            <i class="fas fa-pencil-alt"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm delete" title="Delete" data-agent-id="{{ $agent->id }}">
                            <i class="fas uil-trash-alt"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        </x-table>
    @endif

    <x-modal :idModal="'editAgent'" :ariaLabelledby="'editAgent'" :routeAction="route('SaveAgent')" :idTitle="'editAgentTitle'" :textTitle="'Crear Agente'">
        <input type="text" class="form-control" id="campaign_id" name="campaign_id" style="display: none;">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="sup_id">Supervisor:</label>
                    <select class="form-select" id="sup_id" name="sup_id" required>
                        @foreach ($sups as $sup)
                            <option value="{{ $sup->id }}">{{ $sup->user_name }}</option>
                        @endforeach
                    </select>
                    <div class="valid-feedback">Valido!</div>
                    <div class="invalid-feedback">El supervisor es requerido.</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="user_id">Agente:</label>
                    <select class="form-select" id="user_id" name="user_id" required>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" data-user-id="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    <div class="valid-feedback">Valido!</div>
                    <div class="invalid-feedback">El agente es requerido.</div>
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
        const agents = @json($agents);
        const sups = @json($sups);
        const agentsInSups = @json($agentsInSups);
        const titleEdit = $('#editAgentTitle')[0];
		const forms = document.getElementsByClassName('needs-validation');

        if (id) {
            $('#id_campaign').val(id);
            $('#campaign_id').val(id);
        };

        function reinitData() {
            $('#editAgent').removeClass('was-validated');
            $('#editAgent').addClass('needs-validated');
            $('#id').val("");

            $('#user_id').val("0").trigger('change');
            $('#sup_id').val("0").trigger('change');

        };
        function loadData(ID) {
            $('#editAgent').removeClass('was-validated');
            $('#editAgent').addClass('needs-validated');
            let agentData;

            for (let i = 0; i < agents.length; i++) {
                const agent = agents[i];

                if (agent.id == +ID) agentData = agent;
            };

            if (agentData) {
                const {
                    id,
                    user_id,
                    user_name,
                    camp_name,
                } = agentData;

                let agentSup;

                for (let i = 0; i < agentsInSups.length; i++) {
                    const element = agentsInSups[i];

                    if (element.agent_id == id) agentSup = element;
                };

                $('#id').val(id);

                $('#user_id').val(user_id).trigger('change');

                if (agentSup) $('#sup_id').val(agentSup.sup_id).trigger('change');
            };
        };
        function hideOptions() {
            const ids = [];

            agents.forEach(element => {
                ids.push(element.user_id);
            });

            $('#user_id option').each(function() {
                const value = parseInt($(this).val());

                if (ids.includes(value)) $(this).hide();
            })
        };
        function showOptions() {
            $('#user_id option').show();
        };

        $(document).ready(function() {

            const table = $('#datatable').DataTable();

            $('#newEditAgent').on('click', '', function () {
                hideOptions();
                reinitData();
                titleEdit.innerText = "Nuevo Agente";
            });

            $('#editAgent').on('click', 'btn-close', function () {
                reinitData();
            });

            $('#datatable tbody').on('click', '.btn.edit', function () {
                titleEdit.innerText = "Editar Agente";

                showOptions();
                reinitData();

                const agentId = this.dataset.agentId;

                loadData(agentId);

                $('#editAgent').modal('show');
            });

            $('#datatable tbody').on('dblclick', 'tr td div', function () {
                reinitData();

                const agentId = this.dataset.agentId;

                loadData(agentId);

                $('#editAgent').modal('show');
            });

            $('#datatable tbody').on('click', '.btn.delete', function () {
                const agentId = this.dataset.agentId;

                Swal.fire({
                    title: '¿Eliminar?',
                    text: "Estas seguro de eliminar este Agente",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Eliminar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonClass: 'btn btn-success mt-2',
                    cancelButtonClass: 'btn btn-danger ms-2 mt-2',
                    buttonsStyling: false
                }).then(function (result) {
                    if (result.value) {
                        fetch(`{{ route('DeleteAgent', '') }}/${agentId}`, {
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
                                text: 'Agente eliminado.',
                                icon: 'success',
                                confirmButtonColor: "#34c38f"
                            }).then(function () {
                                window.location.reload();
                            })
                        })
                        .catch(error => {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Ocurrio un error al intentar eliminar el Agente.',
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

                window.location.assign(`{{ url('sales/agents/${campaignId}') }}`);
            });

            $('#datatable input.switch').on('change', function (e) {
                const val = this.checked;
                const agentId = this.dataset.agentId;

                if (val) {
                    fetch(`{{ route('AllowAgent', '') }}/${agentId}`, {
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
                    fetch(`{{ route('DisallowAgent', '') }}/${agentId}`, {
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