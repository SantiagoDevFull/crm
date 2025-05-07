@extends('layouts.master')
@section('title')
    @lang('translation.Editable_Table')
@endsection
@section('css')
    <!-- DataTables -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/themes/default/style.min.css" rel="stylesheet">
@endsection

@section('content')
    @component('common-components.breadcrumb')
        @slot('pagetitle')
            Administración de Usuarios
        @endslot
        @slot('title')
            Grupos de Usuarios
        @endslot
    @endcomponent

    <x-table :idCreateButton="'newEditGroup'" :idModal="'editGroup'" :textButton="'Crear Grupo'" :headers="['GRUPO', 'IP', 'HORARIO', 'ESTADO', 'OPCIONES']">
        @foreach ($groups as $group)
            <tr data-id="{{ $group->id }}">
                <td data-field="grupo">
                    <div data-group-id="{{ $group->id }}">
                        {{ $group->name }}
                    </div>
                </td>
                <td data-field="ip">
                    <div data-group-id="{{ $group->id }}">
                        {{ $group->ip }}
                    </div>
                </td>
                <td data-field="horario">
                    <div data-group-id="{{ $group->id }}">
                        {{ $group->horario_name }}
                    </div>
                </td>
                <td data-field="estado">
                    <div data-group-id="{{ $group->id }}">
                        <input type="checkbox" class="switch" id="switch-{{ $group->id }}" switch="bool"
                            data-group-id="{{ $group->id }}" {{ $group->state == '1' ? 'checked' : '' }} />
                        <label for="switch-{{ $group->id }}" data-on-label="On" data-off-label="Off"></label>
                    </div>
                </td>
                <td style="width: 100px">
                    <button type="button" class="btn btn-outline-info btn-sm edit" title="Edit"
                        data-group-id="{{ $group->id }}" data-bs-toggle="modal" data-bs-target="#editGroup">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm delete" title="Delete"
                        data-group-id="{{ $group->id }}">
                        <i class="fas uil-trash-alt"></i>
                    </button>
                </td>
            </tr>
        @endforeach
    </x-table>

    <x-modal :idModal="'editGroup'" :ariaLabelledby="'editGroup'" :routeAction="route('SaveGroup')" :idTitle="'editGroupTitle'" :textTitle="'Crear Grupo'">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="company_id">Compañía:</label>
                    <select class="form-select" id="company_id" name="company_id" required>
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                    <div class="valid-feedback">Valido!</div>
                    <div class="invalid-feedback">La compañia es requerida.</div>
                </div>
            </div>
            <div class="col-md-6">
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
                    <label class="form-label" for="ip">IP:</label>
                    <input type="text" class="form-control" id="ip" name="ip" required>
                    <div class="valid-feedback">Valido!</div>
                    <div class="invalid-feedback">La IP es requerida.</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="horario_id">Horario:</label>
                    <select class="form-select" id="horario_id" name="horario_id" required>
                        @foreach ($hours as $hour)
                            <option value="{{ $hour->id }}">{{ $hour->name }}</option>
                        @endforeach
                    </select>
                    <div class="valid-feedback">Valido!</div>
                    <div class="invalid-feedback">El horario es requerido.</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <label class="form-label">Permisos:</label>

                {{-- <div class="accordion" id="permissions">
                    @foreach ($allModules as $module)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="module-{{ $module->id }}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#module-collapse-{{ $module->id }}" aria-expanded="false" aria-controls="module-collapse-{{ $module->id }}">
                                    <input type="checkbox" id="m-{{ $module->id }}" name="modules[{{ $module->id }}][module]" class="form-check-input parent-check ms-2 me-2">
                                    <label class="mb-0" for="m-{{ $module->id }}">{{ $module->name }}</label>
                                </button>
                            </h2>
                            <div id="module-collapse-{{ $module->id }}" class="accordion-collapse collapse" aria-labelledby="module-{{ $module->id }}" data-bs-parent="#permissions">
                                <div class="accordion-body p-3">
                                    <ul class="mb-0 p-0">
                                        @foreach ($module->sections as $section)
                                            <li id="section-permissions-{{ $section->id }}" class="row accordion mb-3 list-item">
                                                <div class="accordion-item p-0">
                                                    <h2 class="accordion-header" id="section-{{ $section->id }}">
                                                        <button class="accordion-button collapsed p-2" type="button" data-bs-toggle="collapse" data-bs-target="#section-collapse-{{ $section->id }}" aria-expanded="false" aria-controls="section-collapse-{{ $section->id }}">
                                                            <input type="checkbox" id="s-{{ $section->id }}" name="modules[{{ $module->id }}][sections][{{ $section->id }}][section]" class="form-check-input parent-check ms-2 me-2">
                                                            <label class="mb-0" for="s-{{ $section->id }}">{{ $section->name }}</label>
                                                        </button>
                                                    </h2>
                                                    <div id="section-collapse-{{ $section->id }}" class="accordion-collapse collapse accordion" aria-labelledby="section-{{ $section->id }}" data-bs-parent="#section-permissions-{{ $section->id }}">
                                                        <div class="accordion-body">
                                                            <ul class="mb-0 p-0">
                                                                @foreach ($section->subSections as $subSection)
                                                                    <li class="row cursor-pointer">
                                                                        <label class="col-md-12">
                                                                            <input type="checkbox" id="ss-{{ $subSection->id }}" name="modules[{{ $module->id }}][sections][{{ $section->id }}][subSections][{{ $subSection->id }}]" class="form-check-input parent-check ms-2 me-2">
                                                                            <label class="mb-0" for="ss-{{ $subSection->id }}">{{ $subSection->name }}</label>
                                                                        </label>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div> --}}

                <div id="tree-container"></div>
                <input type="hidden" name="selected_nodes" id="selected-nodes" style="display: none;">
            </div>
        </div>

    </x-modal>
@endsection
@section('script')
    <script src="{{ URL::asset('/assets/libs/parsleyjs/parsleyjs.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/jstree.min.js"></script>
    <script>
        const companies = @json($companies);
        const groups = @json($groups);
        const hours = @json($hours);
        const allModules = @json($allModules);
        const modulesGroup = @json($modulesGroup);
        const sectionsGroup = @json($sectionsGroup);
        const subSectionsGroup = @json($subSectionsGroup);
        const titleEditGroup = $('#editGroupTitle')[0];
        const forms = document.getElementsByClassName('needs-validation');
        console.log(groups)

        function reinitData() {
            $('#editGroup').removeClass('was-validated');
            $('#editGroup').addClass('needs-validated');
            const tree = $(`#tree-container`).jstree(true);
            tree.uncheck_all();
            tree.close_all();

            $('#id')[0].value = "";
            $('#name')[0].value = "";
            $('#ip')[0].value = "";
            $('#company_id')[0].value = "0";
            $('#horario_id')[0].value = "0";
        };

        function loadData(groupId) {
            $('#editGroup').removeClass('was-validated');
            $('#editGroup').addClass('needs-validated');
            let groupData;

            for (let i = 0; i < groups.length; i++) {
                const group = groups[i];

                if (group.id == +groupId) groupData = group;
            };

            if (groupData) {
                const {
                    campana_id,
                    company_id,
                    created_at,
                    created_at_user,
                    deleted_at,
                    horario_id,
                    horario_name,
                    id,
                    ip,
                    name,
                    perfil_id,
                    permissions,
                    state,
                    updated_at,
                    updated_at_user
                } = groupData;

                $('#id')[0].value = id;
                $('#name')[0].value = name;
                $('#ip')[0].value = ip;
                $('#company_id')[0].value = company_id;
                $('#horario_id')[0].value = horario_id;
            };
        };

        function getModules(groupId) {
            const dataFilter = modulesGroup.filter(i => i.group_id == +groupId);

            return dataFilter;
        };

        function getSections(groupId) {
            const dataFilter = sectionsGroup.filter(i => i.group_id == +groupId);

            return dataFilter;
        };

        function getSubSections(groupId) {
            const dataFilter = subSectionsGroup.filter(i => i.group_id == +groupId);

            return dataFilter;
        };

        function treePermissions(data) {
            const modulesData = data.map(i => {
                const {
                    sections
                } = i;

                let opened = false;

                const dat = {
                    id: `module_${i.id}`,
                    text: i.name,
                    state: {
                        opened: false
                    },
                    children: []
                }

                const sectionsData = sections.map(e => {
                    const {
                        subSections
                    } = e;

                    let opened = false;

                    const dat = {
                        id: `section_${e.id}`,
                        text: e.name,
                        state: {
                            opened: false
                        },
                        children: []
                    }

                    const subSectionsData = subSections.map(a => {
                        return {
                            id: `subSection_${a.id}`,
                            text: a.name
                        }
                    });

                    dat.children = subSectionsData;

                    return dat;
                })

                dat.children = sectionsData;

                return dat
            })

            return modulesData
        };

        function checkPermissions(modules, sections, subSections) {
            const tree = $(`#tree-container`).jstree(true);

            modules.forEach(item => {
                tree.check_node(`module_${item.module_id}`)
            });
            sections.forEach(item => {
                tree.check_node(`section_${item.section_id}`)
            });
            subSections.forEach(item => {
                tree.check_node(`subSection_${item.sub_section_id}`)
            });
        };

        $(document).ready(function() {
            const table = $('#datatable').DataTable();

            $('#tree-container').jstree({
                core: {
                    data: treePermissions(allModules)
                },
                plugins: ["checkbox"]
            });

            $('#tree-container').on('changed.jstree', function(e, data) {
                let selectedNodes = data.selected;
                $('#selected-nodes').val(JSON.stringify(selectedNodes));
            });

            $('#newEditGroup').on('click', '', function() {
                reinitData();
                titleEditGroup.innerText = "Nuevo Grupo";
            });

            $('#editGroup').on('click', 'btn-close', function() {
                reinitData();
            });

            $('#datatable tbody').on('click', '.btn.edit', function() {
                titleEditGroup.innerText = "Editar Grupo";

                reinitData();

                const groupId = this.dataset.groupId;

                loadData(groupId);

                const modules = getModules(groupId);
                const sections = getSections(groupId);
                const subSections = getSubSections(groupId);

                checkPermissions(modules, sections, subSections);

                const tree = $(`#${treeId}`).jstree(true);

                $('#editGroup').modal('show');
            });

            $('#datatable tbody').on('dblclick', 'tr td div', function() {
                reinitData();

                const groupId = this.dataset.groupId;

                loadData(groupId);

                $('#editGroup').modal('show');
            });

            $('#datatable tbody').on('click', '.btn.delete', function() {
                const groupId = this.dataset.groupId;

                Swal.fire({
                    title: '¿Eliminar?',
                    text: "Estas seguro de eliminar este Grupo",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Eliminar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonClass: 'btn btn-success mt-2',
                    cancelButtonClass: 'btn btn-danger ms-2 mt-2',
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.value) {
                        fetch(`{{ route('DeleteGroup', '') }}/${groupId}`, {
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
                                    text: 'Ocurrio un error al intentar eliminar el grupo.',
                                    icon: 'error',
                                    confirmButtonColor: "#34c38f"
                                });
                            });
                    };
                });
            });

            $('#datatable input.switch').on('change', function(e) {
                const val = this.checked;
                const groupId = this.dataset.groupId;

                if (val) {
                    fetch(`{{ route('AllowGroup', '') }}/${groupId}`, {
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
                    fetch(`{{ route('DisallowGroup', '') }}/${groupId}`, {
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
