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
        @slot('title') Bloques de Campos @endslot
    @endcomponent

    <x-select-section :idForm="'form_campaign'" :idSelect="'id_campaign'" :nameLabel="'Seleccionar Campaña'" :options="$campaigns"></x-select-section>

    @if ($id)
        <x-table :idCreateButton="'newEditBlock'" :idModal="'editBlock'" :textButton="'Crear Bloque de Campos'" :headers="['CAMPAÑA','NOMBRE','ORDEN','ESTADO','OPCIONES']">
            @foreach ($blocks as $block)
                <tr data-id="{{ $block->id }}">
                    <td data-field="campaña">
                        <div data-block-id="{{ $block->id }}">
                            {{ $block->campaign_name }}
                        </div>
                    </td>
                    <td data-field="nombre">
                        <div data-block-id="{{ $block->id }}">
                            {{ $block->name }}
                        </div>
                    </td>
                    <td data-field="orden">
                        <div data-block-id="{{ $block->id }}">
                            {{ $block->order }}
                        </div>
                    </td>
                    <td data-field="estado">
                        <div data-block-id="{{ $block->id }}">
                            <input type="checkbox" class="switch" id="switch-{{ $block->id }}" switch="bool" data-block-id="{{ $block->id }}" {{ $block->state == "1" ? 'checked' : '' }} />
                            <label for="switch-{{ $block->id }}" data-on-label="On" data-off-label="Off"></label>
                        </div>
                    </td>
                    <td style="width: 100px" data-field="opciones">
                        <button type="button" class="btn btn-outline-info btn-sm edit" title="Edit" data-block-id="{{ $block->id }}" data-bs-toggle="modal" data-bs-target="#editBlock">
                            <i class="fas fa-pencil-alt"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm delete" title="Delete" data-block-id="{{ $block->id }}">
                            <i class="fas uil-trash-alt"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        </x-table>
    @endif

    <x-modal :idModal="'editBlock'" :ariaLabelledby="'editBlock'" :routeAction="route('SaveBlock')" :idTitle="'editBlockTitle'" :textTitle="'Crear Bloque de Campo'">
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
        const blocks = @json($blocks);
        const titleEdit = $('#editBlockTitle')[0];
		const forms = document.getElementsByClassName('needs-validation');

        if (id) {
            $('#id_campaign').val(id);
            $('#campaign_id').val(id);
        };

        function reinitData() {
            $('#editBlock').removeClass('was-validated');
            $('#editBlock').addClass('needs-validated');
            $('#id').val("");
            $('#name').val("");
            $('#order').val("");
        };
        function loadData(ID) {
            $('#editBlock').removeClass('was-validated');
            $('#editBlock').addClass('needs-validated');
            let blockData;

            for (let i = 0; i < blocks.length; i++) {
                const block = blocks[i];

                if (block.id == +ID) blockData = block;
            };

            if (blockData) {
                const {
                    id,
                    name,
                    order,
                } = blockData;


                $('#id').val(id);
                $('#name').val(name);
                $('#order').val(order);
            };
        };

        $(document).ready(function() {

            const table = $('#datatable').DataTable();

            $('#newEditBlock').on('click', '', function () {
                reinitData();
                titleEdit.innerText = "Nuevo Bloque de Campos";
            });

            $('#editBlock').on('click', 'btn-close', function () {
                reinitData();
            });

            $('#datatable tbody').on('click', '.btn.edit', function () {
                titleEdit.innerText = "Editar Bloque de Campos";

                reinitData();

                const blockId = this.dataset.blockId;

                loadData(blockId);

                $('#editBlock').modal('show');
            });

            $('#datatable tbody').on('dblclick', 'tr td div', function () {
                reinitData();

                const blockId = this.dataset.blockId;

                loadData(blockId);

                $('#editBlock').modal('show');
            });

            $('#datatable tbody').on('click', '.btn.delete', function () {
                const blockId = this.dataset.blockId;

                Swal.fire({
                    title: '¿Eliminar?',
                    text: "Estas seguro de eliminar este Bloque de Campo",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Eliminar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonClass: 'btn btn-success mt-2',
                    cancelButtonClass: 'btn btn-danger ms-2 mt-2',
                    buttonsStyling: false
                }).then(function (result) {
                    if (result.value) {
                        fetch(`{{ route('DeleteBlock', '') }}/${blockId}`, {
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
                                text: 'Ocurrio un error al intentar eliminar el Bloque de Campo.',
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

                window.location.assign(`{{ url('sales/blocks/${campaignId}') }}`);
            });

            $('#datatable input.switch').on('change', function (e) {
                const val = this.checked;
                const blockId = this.dataset.blockId;

                if (val) {
                    fetch(`{{ route('AllowBlock', '') }}/${blockId}`, {
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
                    fetch(`{{ route('DisallowBlock', '') }}/${blockId}`, {
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