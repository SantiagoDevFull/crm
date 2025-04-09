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
        @slot('pagetitle') Colaborativo @endslot
        @slot('title') Anuncios @endslot
    @endcomponent

    <x-table :idCreateButton="'newEditAdvertisement'" :idModal="'editAdvertisement'" :textButton="'Crear Anuncio'" :headers="['TÍTULO','ESTADO','OPCIONES']">
        @foreach ($advertisements as $advertisement)
            <tr data-id="{{ $advertisement->id }}">
                <td data-field="titulo">
                    <div data-advertisement-id="{{ $advertisement->id }}">
                        {{ $advertisement->title }}
                    </div>
                </td>
                <td data-field="estado">
                    <div data-advertisement-id="{{ $advertisement->id }}">
                        <input type="checkbox" class="switch" id="switch-{{ $advertisement->id }}" switch="bool" data-advertisement-id="{{ $advertisement->id }}" {{ $advertisement->state == "1" ? 'checked' : '' }} />
                        <label for="switch-{{ $advertisement->id }}" data-on-label="On" data-off-label="Off"></label>
                    </div>
                </td>
                <td style="width: 100px">
                    <button type="button" class="btn btn-outline-info btn-sm edit" title="Edit" data-advertisement-id="{{ $advertisement->id }}" data-bs-toggle="modal" data-bs-target="#editAdvertisement">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm delete" title="Delete" data-advertisement-id="{{ $advertisement->id }}">
                        <i class="fas uil-trash-alt"></i>
                    </button>
                </td>
            </tr>
        @endforeach
    </x-table>

    <x-modal :idModal="'editAdvertisement'" :ariaLabelledby="'editAdvertisement'" :routeAction="route('SaveAdvertisement')" :idTitle="'editAdvertisementTitle'" :textTitle="'Crear Anuncio'">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="title">Título:</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                    <div class="valid-feedback">Valido!</div>
                    <div class="invalid-feedback">El titulo es requerido.</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="file">Adjunto:</label>
                    <input type="file" class="form-control" id="file" name="file">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3" style="display: flex; flex-direction: column;">
                    <label class="form-label" for="title">Lista de distribución:</label>
                    <select id="groups" class="select2 form-control select2-multiple" name="group_advertisement_ids[]" multiple="multiple" data-placeholder="Selecciona" required>
                        @foreach ($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                    <div class="valid-feedback">Valido!</div>
                    <div class="invalid-feedback">Los grupos son requeridos.</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <input id="text" name="text" style="display: none;" required>
                    <label class="form-label" for="text">Contenido:</label>
                    <div id="text-edit"></div>
                    <div class="valid-feedback">Valido!</div>
                    <div class="invalid-feedback">El contenido es requerido.</div>
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
        const advertisements = @json($advertisements);
        const groups = @json($groups);
        const groups_advertisements = @json($groups_advertisements);
        const titleEdit = $('#editAdvertisementTitle')[0];
		const forms = document.getElementsByClassName('needs-validation');

        function reinitData() {
            $('#editAdvertisement').removeClass('was-validated');
            $('#editAdvertisement').addClass('needs-validated');
            $('#id').val("");
            $('#title').val("");
            $('#file').val("");
            $('#groups').val([]);
            window.editor.setData("");
        };
        function loadData(ID) {
            $('#editAdvertisement').removeClass('was-validated');
            $('#editAdvertisement').addClass('needs-validated');
            const advertisementGroups = groups_advertisements.filter(i => i.advertisement_id == +ID);

            let advertisementData;

            for (let i = 0; i < advertisements.length; i++) {
                const advertisement = advertisements[i];

                if (advertisement.id == +ID) advertisementData = advertisement;
            };

            if (advertisementData) {
                const {
                    id,
                    title,
                    text,
                } = advertisementData;

                $('#id')[0].value = id;
                $('#title')[0].value = title;
                $('#groups').val([]);
                window.editor.setData(text);

                const groupsId = [];
                for (let i = 0; i < advertisementGroups.length; i++) {
                    const AG = advertisementGroups[i];
                    groupsId.push(AG.group_id);
                }

                $('#groups').val(groupsId).trigger('change');
            };
        };

        $(document).ready(function() {
            const table = $('#datatable').DataTable();
            $('#groups').select2({
                dropdownParent: $('#editAdvertisement')
            });
            ClassicEditor
                .create(document.querySelector('#text-edit'))
                .then(editor => {
                    window.editor = editor;

                    editor.model.document.on('change:data', () => {
                        $('#text').val(editor.getData());
                    });
                })
                .catch(error => {
                    console.error(error);
                });

            $('#newEditAdvertisement').on('click', '', function () {
                reinitData();
                titleEdit.innerText = "Nuevo Anuncio";
            });

            $('#editAdvertisement').on('click', 'btn-close', function () {
                reinitData();
            });

            $('#datatable tbody').on('click', '.btn.edit', function () {
                titleEdit.innerText = "Editar Anuncio";

                reinitData();

                const advertisementId = this.dataset.advertisementId;

                loadData(advertisementId);

                $('#editAdvertisement').modal('show');
            });

            $('#datatable tbody').on('dblclick', 'tr td div', function () {
                reinitData();

                const advertisementId = this.dataset.advertisementId;

                loadData(advertisementId);

                $('#editAdvertisement').modal('show');
            });

            $('#datatable tbody').on('click', '.btn.delete', function () {
                const advertisementId = this.dataset.advertisementId;

                Swal.fire({
                    title: '¿Eliminar?',
                    text: "Estas seguro de eliminar este Anuncio",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Eliminar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonClass: 'btn btn-success mt-2',
                    cancelButtonClass: 'btn btn-danger ms-2 mt-2',
                    buttonsStyling: false
                }).then(function (result) {
                    if (result.value) {
                        fetch(`{{ route('DeleteAdvertisement', '') }}/${advertisementId}`, {
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
                                text: 'Anuncio eliminado.',
                                icon: 'success',
                                confirmButtonColor: "#34c38f"
                            }).then(function () {
                                window.location.reload();
                            })
                        })
                        .catch(error => {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Ocurrio un error al intentar eliminar el anuncio.',
                                icon: 'error',
                                confirmButtonColor: "#34c38f"
                            });
                        });
                    };
                });
            });
        });

        $('#datatable input.switch').on('change', function (e) {
            const val = this.checked;
            const advertisementId = this.dataset.advertisementId;

            if (val) {
                fetch(`{{ route('AllowAdvertisement', '') }}/${advertisementId}`, {
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
                fetch(`{{ route('DisallowAdvertisement', '') }}/${advertisementId}`, {
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
    </script>
@endsection