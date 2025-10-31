@extends('layouts.master')
@section('title')
    @lang('translation.Editable_Table')
@endsection
@section('css')
    <!-- DataTables -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('common-components.breadcrumb')
        @slot('pagetitle')
            Administración de Usuarios
        @endslot
        @slot('title')
            Usuarios
        @endslot
    @endcomponent

    <x-table :idCreateButton="'newEditUser'" :idModal="'editUser'" :textButton="'Crear Usuario'" :headers="['NOMBRE', 'EMAIL', 'TELEFONO', 'GENERO', 'NACIMIENTO', 'OBSERVACIONES', 'OPCIONES']">
        @foreach ($users as $user)
            <tr data-id="{{ $user->id }}">
                <td data-field="nombre">
                    <div data-user-id="{{ $user->id }}">
                        {{ $user->name }}
                    </div>
                </td>
                <td data-field="email">
                    <div data-user-id="{{ $user->id }}">
                        {{ $user->email }}
                    </div>
                </td>
                <td data-field="telefono">
                    <div data-user-id="{{ $user->id }}">
                        {{ $user->telefono }}
                    </div>
                </td>
                <td data-field="genero">
                    <div data-user-id="{{ $user->id }}">
                        {{ $user->genero }}
                    </div>
                </td>
                <td data-field="nacimiento">
                    <div data-user-id="{{ $user->id }}">
                        {{ $user->fecha_naci }}
                    </div>
                </td>
                <td data-field="observaciones">
                    <div data-user-id="{{ $user->id }}">
                        {{ $user->obs }}
                    </div>
                </td>
                <td style="width: 100px">
                    <button type="button" class="btn btn-outline-info btn-sm edit" title="Editar"
                        data-user-id="{{ $user->id }}" data-bs-toggle="modal" data-bs-target="#editUser">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm delete" title="Eliminar"
                        data-user-id="{{ $user->id }}">
                        <i class="fas uil-trash-alt"></i>
                    </button>
                    <button type="button" class="btn btn-outline-success btn-sm add-group" title="Asignar Grupos"
                        data-user-id="{{ $user->id }}">
                        <i class="fas uil-users-alt"></i>
                    </button>
                </td>
            </tr>
        @endforeach
    </x-table>

    <x-modal :idModal="'editUser'" :ariaLabelledby="'editUser'" :routeAction="route('SaveUser')" :idTitle="'editUserTitle'" :textTitle="'Crear Usuario'">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="name">Nombre Completo:</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                    <div class="valid-feedback">Valido!</div>
                    <div class="invalid-feedback">El nombre es requerido.</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="email">Email:</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="email" name="email" placeholder="Email"
                            required>
                        <div class="input-group-text">{{ $company->sufijo }}</div>
                        <div class="valid-feedback">Valido!</div>
                        <div class="invalid-feedback">El email es requerido.</div>
                    </div>
                    {{-- <input type="text" class="form-control" id="email" name="email" required> --}}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="password">Contraseña:</label>
                    <input type="password" class="form-control" id="password" name="password">
                    <div class="valid-feedback">Valido!</div>
                    <div class="invalid-feedback">La contraseña es requerida.</div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="telefono">Teléfono:</label>
                    <input type="number" class="form-control" id="telefono" name="telefono">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="genero">Género:</label>
                    <select class="form-select" id="genero" name="genero" value="0" required>
                        <option value="0" disabled selected>Seleccionar</option>
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                    </select>
                    <div class="valid-feedback">Valido!</div>
                    <div class="invalid-feedback">El genero es requerido.</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="fecha_naci">Fecha de nacimiento:</label>
                    <input type="date" class="form-control" id="fecha_naci" name="fecha_naci"
                        max="{{ now()->subYears(18)->format('Y-m-d') }}" required>
                    <div class="valid-feedback">Valido!</div>
                    <div class="invalid-feedback">La fecha de nacimiento es requerida.</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="obs">Observaciones:</label>
                    <textarea class="form-control" id="obs" name="obs"></textarea>
                </div>
            </div>
        </div>
    </x-modal>

    <div class="modal fade" id="addGroup" tabindex="-1" role="dialog" aria-labelledby="addGroup"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Grupos del Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <form class="row" action="{{ route('AddGroup') }}" method="POST">
                        @csrf
                        <input id="user_id" name="user_id" style="display: none;">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <select class="form-select" id="group_id" name="group_id" value="0" required>
                                    <option value="0" selected>Seleccionar</option>
                                    @foreach ($groups_general as $group_general)
                                        <option value="{{ $group_general->id }}">{{ $group_general->name }}</option>
                                    @endforeach
                                </select>
                                <div class="valid-feedback">Valido!</div>
                                <div class="invalid-feedback">El grupo es requerido.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <button type="submit" class="btn btn-success">Añadir</button>
                            </div>
                        </div>
                    </form>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">

                                    <table id="datatable-group" class="table table-bordered dt-responsive nowrap"
                                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>GRUPO</th>
                                                <th>OPCIONES</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <tr>
                                                <td>
                                                    GRUPO
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-outline-danger btn-sm delete"
                                                        title="Delete" data-group-id="0">
                                                        <i class="fas uil-trash-alt"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('/assets/libs/parsleyjs/parsleyjs.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script>
        const company = @json($company);
        const groups_general = @json($groups_general);
        const users = @json($users);
        const groups = @json($groups);
        const titleEdit = $('#editUserTitle')[0];
        const forms = document.getElementsByClassName('needs-validation');

        function reinitData() {
            $('#editUser').removeClass('was-validated');
            $('#editUser').addClass('needs-validated');
            $('#id')[0].value = "";
            $('#name')[0].value = "";
            $('#email')[0].value = "";
            $('#password')[0].value = "";
            $('#telefono')[0].value = "";
            $('#genero')[0].value = "";
            $('#fecha_naci')[0].value = "";
            $('#obs')[0].value = "";

            $('#user_id')[0].value = "";
            $('#datatable-group tbody')[0].innerHTML = "";
        };

        function loadData(ID) {
            $('#editUser').removeClass('was-validated');
            $('#editUser').addClass('needs-validated');
            const userGroups = groups.filter(i => i.user_id == +ID);

            let userData;

            for (let i = 0; i < users.length; i++) {
                const user = users[i];

                if (user.id == +ID) userData = user;
            };

            if (userData) {
                const {
                    id,
                    name,
                    email,
                    telefono,
                    genero,
                    fecha_naci,
                    obs
                } = userData;
                const HTMLgroups = [];

                const userName = email.split('@');

                $('#id')[0].value = id;
                $('#name')[0].value = name;
                $('#email')[0].value = userName[0];
                $('#telefono')[0].value = telefono;
                $('#genero')[0].value = genero;
                $('#fecha_naci')[0].value = fecha_naci;
                $('#obs')[0].value = obs;

                $('#user_id')[0].value = id;
                for (let i = 0; i < userGroups.length; i++) {
                    const UG = userGroups[i];

                    HTMLgroups.push(`<tr>
                                        <td>
                                            ${UG.group_name}
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-outline-danger btn-sm delete" title="Delete" data-group-id="${UG.id}">
                                                <i class="fas uil-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>`);
                };

                $('#datatable-group tbody')[0].innerHTML = HTMLgroups.join('');
            };
        };

        $(document).ready(function() {
            const table = $('#datatable').DataTable();

            $('#newEditUser').on('click', '', function() {
                $('#password').attr('required', true);
                reinitData();
                titleEdit.innerText = "Nuevo Usuario";
            });

            $('#editUser').on('click', 'btn-close', function() {
                reinitData();
            });

            $('#datatable tbody').on('click', '.btn.edit', function() {
                titleEdit.innerText = "Editar Usuario";

                $('#password').attr('required', false);

                reinitData();

                const userId = this.dataset.userId;

                loadData(userId);

                $('#editUser').modal('show');
            });

            $('#datatable tbody').on('click', '.btn.add-group', function() {
                titleEdit.innerText = "Editar Usuario";

                reinitData();

                const userId = this.dataset.userId;

                loadData(userId);

                $('#addGroup').modal('show');
            });

            $('#datatable tbody').on('dblclick', 'tr td div', function() {
                reinitData();

                const userId = this.dataset.userId;

                loadData(userId);

                $('#editUser').modal('show');
            });

            $('#datatable tbody').on('click', '.btn.delete', function() {
                const userId = this.dataset.userId;

                Swal.fire({
                    title: '¿Eliminar?',
                    text: "Estas seguro de eliminar este Usuario",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Eliminar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonClass: 'btn btn-success mt-2',
                    cancelButtonClass: 'btn btn-danger ms-2 mt-2',
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.value) {
                        fetch(`{{ route('DeleteUser', '') }}/${userId}`, {
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
                                    text: 'Usuario eliminado.',
                                    icon: 'success',
                                    confirmButtonColor: "#34c38f"
                                }).then(function() {
                                    window.location.reload();
                                })
                            })
                            .catch(error => {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Ocurrio un error al intentar eliminar el usuario.',
                                    icon: 'error',
                                    confirmButtonColor: "#34c38f"
                                });
                            });
                    };
                });
            });

            $('#datatable-group tbody').on('click', '.btn.delete', function() {
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
                        fetch(`{{ route('DeleteUserGroup', '') }}/${groupId}`, {
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
                                    text: 'Grupo eliminado.',
                                    icon: 'success',
                                    confirmButtonColor: "#34c38f"
                                }).then(function() {
                                    window.location.reload();
                                })
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
