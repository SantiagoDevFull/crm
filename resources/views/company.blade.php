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
            Campañas
        @endslot
    @endcomponent

    <x-table :idCreateButton="'newEditCompany'" :idModal="'editCompany'" :textButton="'Crear Empresa'" :headers="[
        'EMPRESA',
        'NOMBRE CORTO',
        'N° DOC.',
        'PAIS',
        'CONTACTO',
        'TIPO DE ASISTENCIA',
        'SUFIJO',
        'LOGO',
        'COLOR DEL MENU',
        'COLOR DEL TEXTO',
        'ADMINISTRADOR',
        'ACCIONES',
    ]">
        @foreach ($companies as $company)
            <tr data-id="{{ $company->id }}">
                <td data-field="empresa">
                    <div data-company-id="{{ $company->id }}">
                        {{ $company->name }}
                    </div>
                </td>
                <td data-field="nombre corto">
                    <div data-company-id="{{ $company->id }}">
                        {{ $company->short_name }}
                    </div>
                </td>
                <td data-field="n° doc.">
                    <div data-company-id="{{ $company->id }}">
                        {{ $company->document }}
                    </div>
                </td>
                <td data-field="pais">
                    <div data-company-id="{{ $company->id }}">
                        {{ $company->pais }}
                    </div>
                </td>
                <td data-field="contacto">
                    <div data-company-id="{{ $company->id }}">
                        {{ $company->contact }}
                    </div>
                </td>
                <td data-field="tipo de asistencia">
                    <div data-company-id="{{ $company->id }}">
                        {{ $company->asist_type }}
                    </div>
                </td>
                <td data-field="sufijo">
                    <div data-company-id="{{ $company->id }}">
                        {{ $company->sufijo }}
                    </div>
                </td>
                <td data-field="logo">
                    <div data-company-id="{{ $company->id }}">
                        {{ $company->logo }}
                    </div>
                </td>
                <td data-field="color del menu">
                    <div data-company-id="{{ $company->id }}">
                        {{ $company->menu_color }}
                    </div>
                </td>
                <td data-field="color del texto">
                    <div data-company-id="{{ $company->id }}">
                        {{ $company->text_color }}
                    </div>
                </td>
                <td data-field="admin">
                    <div data-company-id="{{ $company->id }}">
                        {{ $company->admin }}
                    </div>
                </td>
                <td style="width: 100px">
                    <button type="button" class="btn btn-outline-info btn-sm edit" title="Editar"
                        data-company-id="{{ $company->id }}" data-bs-toggle="modal" data-bs-target="#editCompany">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm delete" title="Eliminar"
                        data-company-id="{{ $company->id }}">
                        <i class="fas uil-trash-alt"></i>
                    </button>

                    <button type="button" class="btn btn-outline-dark btn-sm deleteAdmin" title="Eliminar Administrador"
                        data-company-id="{{ $company->id }}">
                        <i class="fas uil-trash-alt"></i>
                    </button>
                </td>
            </tr>
        @endforeach
    </x-table>

    <x-modal :idModal="'editCompany'" :ariaLabelledby="'editCompany'" :routeAction="route('SaveCompany')" :idTitle="'editCompanyTitle'" :textTitle="'Crear Empresa'">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="name">Empresa:</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                    <div class="valid-feedback">Valido!</div>
                    <div class="invalid-feedback">El nombre de la Empresa es requerido.</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="short_name">Nombre corto:</label>
                    <input type="text" class="form-control" id="short_name" name="short_name" required>
                    <div class="valid-feedback">Valido!</div>
                    <div class="invalid-feedback">El nombre corto de la Empresa es requerido.</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="document">N° Doc.:</label>
                    <input type="text" class="form-control" id="document" name="document" required>
                    <div class="valid-feedback">Valido!</div>
                    <div class="invalid-feedback">El nombre corto de la Empresa es requerido.</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="pais">País:</label>
                    <select class="form-select" name="pais" id="pais" required>
                        <option value="" selected disabled>Seleccionar</option>
                        <option value="Bolivia">Bolivia</option>
                        <option value="Chile">Chile</option>
                        <option value="Ecuador">Ecuador</option>
                        <option value="España">España</option>
                        <option value="Global">Global</option>
                        <option value="México">México</option>
                        <option value="Perú">Perú</option>
                        <option value="Venezuela">Venezuela</option>
                    </select>
                    <div class="valid-feedback">Valido!</div>
                    <div class="invalid-feedback">El país es requerido.</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="contact">Contacto:</label>
                    <input type="text" class="form-control" id="contact" name="contact" required>
                    <div class="valid-feedback">Valido!</div>
                    <div class="invalid-feedback">El contacto de la Empresa es requerido.</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="asist_type">Tipo de asistencia:</label>
                    <select class="form-select" name="asist_type" id="asist_type" required>
                        <option value="" selected disabled>Seleccionar</option>
                        <option value="Login/Logout al Sistema">Login/Logout al Sistema</option>
                        <option value="Huella dactilar">Huella dactilar</option>
                    </select>
                    <div class="valid-feedback">Valido!</div>
                    <div class="invalid-feedback">El tipo de asistencia es requerido.</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="sufijo">Sufijo:</label>
                    <input type="text" class="form-control" id="sufijo" name="sufijo" required>
                    <div class="valid-feedback">Valido!</div>
                    <div class="invalid-feedback">El sufijo de la Empresa es requerido.</div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="menu_color">Color del menu:</label>
                    <input type="color" class="form-control" id="menu_color" name="menu_color" required>
                    <div class="valid-feedback">Valido!</div>
                    <div class="invalid-feedback">El color del menu de la Empresa es requerido.</div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="text_color">Color del texto:</label>
                    <input type="color" class="form-control" id="text_color" name="text_color" required>
                    <div class="valid-feedback">Valido!</div>
                    <div class="invalid-feedback">El color del texto de la Empresa es requerido.</div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="logo">Logo:</label>
                    <input type="file" class="form-control" name="logo" id="logo" accept="image/*" required>
                    <div class="valid-feedback">Valido!</div>
                    <div class="invalid-feedback">El logo de la Empresa es requerido.</div>
                </div>
                <div class="mb-3">
                    <div id="logoPreviewContainer" class="position-relative mb-3 d-none" style="width: 150px;">
                        <img id="logoPreview" src="#" alt="Vista previa" class="img-thumbnail rounded">

                        <button type="button" id="removeImage"
                            class="btn btn-danger btn-sm position-absolute top-0 end-0 translate-middle rounded-circle"
                            style="z-index: 2;">Quitar Logo
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="mb-3">
                <label class="form-label" for="user_group_id">Asignar Administrador:</label>
                <select class="form-select" name="user_group_id" id="user_group_id" required>
                    <option value="0" selected disabled>Seleccionar</option>
                    @foreach ($userGroups as $user_group)
                        <option value="{{ $user_group->user_group_id }}">
                            {{  $user_group->user_group_id }} - 
                            {{ $user_group->group_name }} -
                            {{ $user_group->user_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </x-modal>

    <loading></loading>
@endsection

@section('script')
    <script src="{{ URL::asset('/assets/libs/parsleyjs/parsleyjs.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script>
        const logoInput = document.getElementById("logo");
        const logoPreview = document.getElementById("logoPreview");
        const logoPreviewContainer = document.getElementById("logoPreviewContainer");
        const removeImageBtn = document.getElementById("removeImage");

        logoInput.addEventListener("change", function() {
            const file = this.files[0];

            if (file && file.type.startsWith("image/")) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    logoPreview.src = e.target.result;
                    logoPreviewContainer.classList.remove("d-none");
                };
                reader.readAsDataURL(file);
            } else {
                clearImage();
            }
        });

        removeImageBtn.addEventListener("click", function() {
            clearImage();
        });

        function clearImage() {
            logoPreview.src = "#";
            logoInput.value = "";
            logoPreviewContainer.classList.add("d-none");
        }




        const titleEdit = $('#editCompanyTitle')[0];
        const companies = @json($companies);
        const forms = document.getElementsByClassName('needs-validation');

        function reinitData() {
            $('#id')[0].value = "";
            $('#name')[0].value = "";
            $('#short_name')[0].value = "";
            $('#document')[0].value = "";
            $('#pais')[0].value = "";
            $('#contact')[0].value = "";
            $('#asist_type')[0].value = "";
            $('#sufijo')[0].value = "";
            $('#menu_color')[0].value = "";
            $('#text_color')[0].value = "";
            $('#user_group_id')[0].value = "0";
            $('#logo')[0].value = "";

        };

        function loadData(ID) {
            console.log("pasee");
            $('#editCompany').removeClass('was-validated');
            $('#editCompany').addClass('needs-validated');

            let userData;

            for (let i = 0; i < companies.length; i++) {
                const company = companies[i];

                if (company.id == +ID) companyData = company;
            };

            if (companyData) {
                const {
                    id,
                    name,
                    short_name,
                    document,
                    pais,
                    contact,
                    asist_type,
                    sufijo,
                    menu_color,
                    text_color
                } = companyData;

                $('#id')[0].value = id;
                $('#name')[0].value = name;
                $('#short_name')[0].value = short_name;
                $('#document')[0].value = document;
                $('#pais')[0].value = pais;
                $('#contact')[0].value = contact;
                $('#asist_type')[0].value = asist_type;
                $('#sufijo')[0].value = sufijo;
                $('#menu_color')[0].value = menu_color;
                $('#text_color')[0].value = text_color;
            };
        };

        $(document).ready(function() {

            const table = $('#datatable').DataTable();

            $('#newEditCompany').on('click', '', function() {
                reinitData();
                titleEdit.innerText = "Nueva Empresa";
            });

            $('#datatable tbody').on('click', '.btn.delete', function() {
                const companyId = this.dataset.companyId;

                Swal.fire({
                    title: '¿Eliminar?',
                    text: "Estas seguro de eliminar esta Empresa ?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Eliminar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonClass: 'btn btn-success mt-2',
                    cancelButtonClass: 'btn btn-danger ms-2 mt-2',
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.value) {
                        fetch(`{{ route('DeleteCompany', '') }}/${companyId}`, {
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
                                    text: 'Compañía eliminado.',
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

            $('#datatable tbody').on('click', '.btn.deleteAdmin', function() {
                const companyId = this.dataset.companyId;

                Swal.fire({
                    title: '¿Eliminar?',
                    text: "Estas seguro de eliminar el Administrador ?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Eliminar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonClass: 'btn btn-success mt-2',
                    cancelButtonClass: 'btn btn-danger ms-2 mt-2',
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.value) {
                        fetch(`{{ route('DeleteAdmin', '') }}/${companyId}`, {
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
                                    text: 'Administrador eliminado.',
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



            $('#datatable tbody').on('click', '.btn.edit', function() {

                titleEdit.innerText = "Editar Empresa";
                const companyId = this.dataset.companyId;
                reinitData();
                loadData(companyId);

                $('#editCompany').modal('show');
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
