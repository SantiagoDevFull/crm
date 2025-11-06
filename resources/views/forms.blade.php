@extends('layouts.master')
@section('title')
    @lang('translation.Editable_Table')
@endsection
@section('css')
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

    <style>
        .invalid-feedback {
            display: block;
        }
    </style>
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

    <form action="{{ route('SaveSold') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <input type="text" class="form-control" id="id" name="id" style="display: none;"
            value="{{ $form_id }}">
        <input type="text" class="form-control" id="campaign_id" name="campaign_id" style="display: none;"
            value="{{ $id }}">
        <input type="text" class="form-control" id="tab_state_id" name="tab_state_id" style="display: none;"
            value="{{ $tab_state_id }}">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <h4 class="card-title">Cabecera de la Venta</h4>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="state_id">Estado:</label>
                                    <select class="form-select" id="state_id" name="state_id" required>
                                        <option value="0" selected disabled>Seleccionar</option>
                                        @foreach ($states as $state)
                                            <option value="{{ $state->id }}">{{ $state->id }} - {{ $state->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        @foreach ($blocks as $block)
            <x-block-section :title="$block->name">
                @if (isset($fields[$block->id]))
                    @foreach ($fields[$block->id] as $f)
                        <div class="col-md-{{ $f['width_col'] }}">
                            <div class="mb-3">

                                @error('field_'.$f['id'])
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                            <li>{{ $message }}</li>
                                    </ul>
                                </div>
                                @enderror
                                
                                @php
                                $readonly = $f['can_edit'] == 0 ? 'readonly' : '';
                                $color_readonly = $f['can_edit'] == 0 ? '#e9ecef' : '';
                                $hidden = $f['can_view'] == 0 ? 'hidden' : '';
                                $unique = $f['unique'] == 1 ? 'Unico' : '';
                                @endphp

                                
                                @if($unique === 'Unico') 
                                    <span class="badge bg-danger" {{ $hidden }}>Único</span>
                                @endif
                                
                                <label {{ $hidden }} class="form-label" for="{{ $f['id'] }}">{{ $f['name'] }}:</label>

                                @if ($f['type_field_id'] == 1)
                                    <input type="text" class="form-control" id="{{ $f['id'] }}" style="background-color: {{ $color_readonly }}"
                                        name="{{ $block->id }}[{{ $f['id'] }}]" {{ $readonly }} {{ $hidden }}>
                                @elseif($f['type_field_id'] == 2)
                                    <input type="text" class="form-control" id="{{ $f['id'] }}" style="background-color: {{ $color_readonly }}"
                                        name="{{ $block->id }}[{{ $f['id'] }}]" {{ $readonly }} {{ $hidden }}>
                                @elseif($f['type_field_id'] == 3)
                                    <select class="form-select"
                                        id="{{ $f['id'] }}"
                                        name="{{ $block->id }}[{{ $f['id'] }}]"
                                        data-placeholder="Selecciona"
                                        @if($f['can_edit'] == 0) data-readonly="1" @endif
                                        {{ $hidden }}>
                                        @if (!empty($f['options']))
                                            @foreach (explode("\r\n", $f['options']) as $opt)
                                                <option value="{{ $opt }}">{{ $opt }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                @elseif($f['type_field_id'] == 4)
                                    <select class="select2 form-control select2-multiple"
                                        id="{{ $f['id'] }}"
                                        name="{{ $block->id }}[{{ $f['id'] }}][]"
                                        multiple="multiple"
                                        data-placeholder="Selecciona"
                                        @if($f['can_edit'] == 0) data-readonly="1" @endif
                                        {{ $hidden }}>
                                        @if (!empty($f['options']))
                                            @foreach (explode("\r\n", $f['options']) as $opt)
                                                <option value="{{ $opt }}">{{ $opt }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                @elseif($f['type_field_id'] == 5)
                                    <input class="form-control" type="date" id="{{ $f['id'] }}" style="background-color: {{ $color_readonly }}"
                                        name="{{ $block->id }}[{{ $f['id'] }}]" {{ $readonly }} {{ $hidden }}>
                                @elseif($f['type_field_id'] == 6)
                                    <input class="form-control" type="datetime-local" id="{{ $f['id'] }}" style="background-color: {{ $color_readonly }}"
                                        name="{{ $block->id }}[{{ $f['id'] }}]" {{ $readonly }} {{ $hidden }}>
                                @elseif($f['type_field_id'] == 7)

                                    @php
                                    $range = $f['range']?? 8;
                                    @endphp
                                    <input style="background-color: {{ $color_readonly }}"
                                        class="form-control" 
                                        type="number" 
                                        id="{{ $f['id'] }}" 
                                        name="{{ $block->id }}[{{ $f['id'] }}]" 
                                        {{ $readonly }} 
                                        {{ $hidden }}
                                        oninput="if(this.value.length > {{ $range }}) this.value = this.value.slice(0, {{ $range }});">
                                        
                                @elseif($f['type_field_id'] == 8)
                                    <input type="checkbox" switch="bool" id="{{ $f['id'] }}" style="background-color: {{ $color_readonly }}"
                                        name="{{ $block->id }}[{{ $f['id'] }}]" {{ $readonly }} {{ $hidden }} />
                                    <label for="{{ $f['id'] }}" class="mb-0" data-on-label="On"
                                        data-off-label="Off"></label>
                                    {{-- <div class="form-check form-switch form-switch-md">
                                    <input class="form-check-input" type="checkbox" id="{{ $f['id'] }}" name="{{ $block->id }}[{{ $f['id'] }}]">
                                </div> --}}
                                @elseif($f['type_field_id'] == 9)
                                    <input class="form-control" type="file" id="{{ $f['id'] }}" style="background-color: {{ $color_readonly }}"
                                        name="{{ $block->id }}[{{ $f['id'] }}]" {{ $readonly }} {{ $hidden }}>
                                @elseif($f['type_field_id'] == 10)
                                    <input class="form-control" type="file" multiple id="{{ $f['id'] }}" style="background-color: {{ $color_readonly }}"
                                        name="{{ $block->id }}[{{ $f['id'] }}]" {{ $readonly }} {{ $hidden }}>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </x-block-section>
        @endforeach

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" id="save-form">Guardar</button>
        </div>
    </form>


@endsection
@section('script')
    <script src="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/ckeditor/ckeditor.min.js') }}"></script>
    <script>
        const id = @json($id);
        const tab_state_id = @json($tab_state_id);
        const campaign = @json($campaign);
        const fields = @json($fields);
        const blocks = @json($blocks);
        const form = @json($form);



        $(document).ready(function() {



            $('.select2').select2();

            $('[data-readonly="1"]').each(function() {
                const $el = $(this);

                if ($el.hasClass('select2')) {
                    $el.on('select2:opening select2:unselecting select2:selecting', function(e) {
                        e.preventDefault();
                    });
                } else {
                    $el.on('mousedown focus', function(e) {
                        e.preventDefault();
                        $(this).blur();
                    });
                    $el.on('keydown', function(e) {
                        const blocked = [32, 33, 34, 35, 36, 37, 38, 39, 40];
                        if (blocked.includes(e.which)) e.preventDefault();
                    });
                }

                $el.css({
                    'background-color': '#e9ecef',
                    'pointer-events': 'auto',
                    'cursor': 'not-allowed'
                });
            });

            if (form) {

                const states = @json($states);
                if (states.length === 0) {
                    const select = document.getElementById('state_id');

                } else {
                    $('#state_id').val(form.state_id).trigger('change');

                    const dataParse = JSON.parse(form.data);

                    for (const k in dataParse) {
                        const sect = dataParse[k];

                        for (const key in sect) {
                            const val = sect[key];

                            $(`#${key}`).val(val).trigger('change');
                            $(`#${key}`).prop("checked", val);
                        };
                    };
                }


            };

            $('form').on('submit', function(e) {
                let isValid = true;
                let firstInvalid = null;

                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();

                $(this).find('input, select, textarea').each(function() {
                    const el = $(this);
                    const val = el.val();
                    const isMultiple = el.prop('multiple');
                    const type = el.attr('type');
                    const required = el.prop('required') || el.attr('required');

                    if (!el.is(':visible')) return;

                    let showError = false;

                    if (required) {
                        if ((type === 'checkbox' && !el.is(':checked')) ||
                            (type === 'file' && el.get(0).files.length === 0) ||
                            (isMultiple && (!val || val.length === 0)) ||
                            (!isMultiple && (!val || val.trim() === ''))) {
                            showError = true;
                        }
                    }

                    if (showError) {
                        isValid = false;
                        firstInvalid = firstInvalid || el;

                        el.addClass('is-invalid');

                        if (el.closest('.mb-3').find('.invalid-feedback').length === 0) {
                            el.closest('.mb-3').append(
                                '<div class="invalid-feedback">Este campo es obligatorio.</div>'
                            );
                        }
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    if (firstInvalid) firstInvalid.focus();

                    Swal.fire({
                        title: "Advertencia!",
                        text: "Por favor, completa todos los campos obligatorios.",
                        icon: "warning"
                    });
                }
            });


        });
    </script>
@endsection
