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
                Ventas
            @endslot
            @slot('title')
                {{ $campaign->name }}
            @endslot
        @endcomponent

        <div class="row">

            <div class="col-md-12">
                <div class="card">
                    <div class="card-body p-0">

                        <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                            @foreach ($tab_states as $tab_state)
                                <li class="nav-item">
                                    <a class="nav-link {{ $loop->first ? 'active' : '' }}" data-bs-toggle="tab"
                                        href="#{{ $tab_state->id }}" role="tab" data-tab-id="{{ $tab_state->id }}">
                                        <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                        <span class="d-none d-sm-block">{{ $tab_state->name }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                        <div class="tab-content p-3 text-muted">
                            @foreach ($tab_states as $tab_state)
                                <div class="tab-pane {{ $loop->first ? 'active' : '' }}" id="{{ $tab_state->id }}"
                                    role="tabpanel">
                                    <button type="button" class="btn btn-success waves-effect waves-light mb-3 modal-sold"
                                        title="Crear Venta" data-tab-id="{{ $tab_state->id }}">
                                        Crear Venta
                                    </button>

                                    <button type="button" class="btn btn-success waves-effect waves-light mb-3 modal-sold"
                                        title="Exportar Venta" data-tab-id="{{ $tab_state->id }}" style="display: none;">
                                        Exportar Venta
                                    </button>

                                    <table class="datatable table table-bordered dt-responsive nowrap"
                                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                
                                                <th>Cliente</th>
                                                <th>CIF/NIF</th>
                                                <th>AGENTE</th>
                                                <th>ESTADO</th>
                                                <th>OPCIONES</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @php
                                                $data = $forms[$tab_state->id] ?? [];
                                            @endphp

                                            @foreach ($data as $d)
                                                @php
                                                    $state_name = collect($states)->firstWhere('id', $d['state_id']);
                                                    $row_color = $state_name['color'] ?? 'transparent';

                                                    $decodedData = json_decode($d['data'], true);

                                                    $values = collect($decodedData)
                                                        ->flatMap(fn($item) => is_array($item) ? $item : [$item]);

                                                    $nombre = $values
                                                        ->filter(function ($v) {
                                                            if (empty($v)) return false;
                                                            if (!preg_match('/[a-zA-ZÁÉÍÓÚáéíóúÑñ]/u', $v)) return false; 
                                                            if (filter_var($v, FILTER_VALIDATE_EMAIL)) return false;       
                                                            if (preg_match('/^\d+$/', $v)) return false;                   
                                                            if (preg_match('/^\d{7,}$/', $v)) return false;               
                                                            return true;
                                                        })
                                                        ->sortByDesc(fn($v) => str_word_count($v)) 
                                                        ->first() ?? '—';
                                                        
                                                    $documento = $values
                                                        ->filter(function ($v) {
                                                            if (empty($v)) return false;
                                                            if (!preg_match('/^\d+$/', $v)) return false;
                                                            $len = strlen($v);
                                                            return $len >= 7 && $len <= 11;
                                                        })
                                                        ->first() ?? '—';
                                                @endphp
                                                <tr data-id="{{ $d['id'] }}"
                                                    style="background-color: {{ $row_color }}">
                                                    
                                                    <td>{{ $nombre   }}</td>
                                                    </td>
                                                    <td>{{ $documento   }}
                                                    </td>
                                                    <td>{{ $d['created_at_user'] }}</td>
                                                    <td>{{ $state_name['name'] ?? '' }}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-outline-info btn-sm edit"
                                                            title="Editar" data-form-id="{{ $d['id'] }}"
                                                            data-tab-id="{{ $tab_state->id }}">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger btn-sm delete"
                                                            title="Eliminar" data-form-id="{{ $d['id'] }}">
                                                            <i class="fas uil-trash-alt"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>
            </div>

        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
        <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
        <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
        <script src="{{ URL::asset('/assets/libs/ckeditor/ckeditor.min.js') }}"></script>
        <script>
            const id = @json($id);
            const campaigns = @json($campaigns);
            const tab_states = @json($tab_states);
            const fields = @json($fields);
            const cont = @json($cont);

            $(document).ready(function() {

                if (cont > 0) {
                    $('.modal-sold[title="Exportar Venta"]').show();
                } else {
                    $('.modal-sold[title="Exportar Venta"]').hide();
                }

                const filterFields = fields[tab_states[0].id];
                console.log(filterFields)

                $('.datatable').DataTable();

                $('a.nav-link').on('click', function() {
                    const tabId = this.dataset.tabId;
                    const filterFields = fields[tabId];

                    console.log(filterFields)
                });

                $('.modal-sold[title="Crear Venta"]').on('click', '', function() {
                    const tabId = this.dataset.tabId;

                    window.location.assign(`{{ url('sales/solds/${id}/${tabId}') }}`);
                });

                $('.modal-sold[title="Exportar Venta"]').on('click', '', function() {
                    const tabId = this.dataset.tabId;

                    $.ajax({
                        url: `/sales/solds-export/${id}/${tabId}`,
                        type: 'GET',
                        xhrFields: {
                            responseType: 'blob'
                        },
                        success: function(response) {
                            let blob = new Blob([response], {
                                type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                            });
                            let link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob);
                            link.download = 'reporte-ventas.xlsx';
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);

                            Swal.fire({
                                title: 'Éxito',
                                text: 'El archivo se ha descargado correctamente.',
                                icon: 'success'
                            });
                        },
                        error: function(xhr) {
                            console.log('Error:', xhr.responseJSON?.message || 'Ocurrió un error');
                            Swal.fire({
                                title: 'Error',
                                text: xhr.responseJSON?.message ||
                                    'No se pudo completar la exportación',
                                icon: 'error'
                            });
                        }
                    });

                });

                $('.datatable tbody').on('click', '.btn.edit', function() {
                    
                    const tabId = this.dataset.tabId;
                    const formId = this.dataset.formId;

                    window.location.assign(`{{ url('sales/solds/${id}/${tabId}/${formId}') }}`);
                });

                $('.datatable tbody').on('dblclick', 'tr td div', function() {
                    const formId = this.dataset.formId;
                    console.log(formId)
                });

                $('.datatable tbody').on('click', '.btn.delete', function() {
                    const formId = this.dataset.formId;

                    Swal.fire({
                        title: '¿Eliminar?',
                        text: "Estas seguro de eliminar esta Venta",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Eliminar',
                        cancelButtonText: 'Cancelar',
                        confirmButtonClass: 'btn btn-success mt-2',
                        cancelButtonClass: 'btn btn-danger ms-2 mt-2',
                        buttonsStyling: false
                    }).then(function(result) {
                        if (result.value) {
                            fetch(`{{ route('DeleteSold', '') }}/${formId}`, {
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
                                        text: 'Venta eliminado.',
                                        icon: 'success',
                                        confirmButtonColor: "#34c38f"
                                    }).then(function() {
                                        window.location.reload();
                                    })
                                })
                                .catch(error => {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: 'Ocurrio un error al intentar eliminar la Venta.',
                                        icon: 'error',
                                        confirmButtonColor: "#34c38f"
                                    });
                                });
                        };
                    });
                });

                $('.datatable input.switch').on('change', function(e) {
                    const val = this.checked;
                    const formId = this.dataset.formId;

                    if (val) {
                        fetch(`{{ route('AllowSold', '') }}/${formId}`, {
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
                        fetch(`{{ route('DisallowSold', '') }}/${formId}`, {
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
            });
        </script>
    @endsection
