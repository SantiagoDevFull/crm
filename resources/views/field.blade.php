@extends('includes.app')

@section('title','Configuración de Campañas')
@section('subtitle','Campos')

@section('content')

    <field-form
        :campain_id = "{{ $campain_id }}"
        :campains = "{{ $campains }}"
        :url = "'{{ route('dashboard.field.list') }}'"
    ></field-form>

    <field-table
        :url = "'{{ route('dashboard.field.list') }}'"
        
        :url_get_field = "'{{ route('dashboard.field.get_field') }}'"
        :url_get_blocks = "'{{ route('dashboard.field.get_block') }}'"
        :url_get_type_fields = "'{{ route('dashboard.field.get_type_field') }}'"
        :url_get_widths = "'{{ route('dashboard.field.get_width') }}'"
        :url_get_user_groups = "'{{ route('dashboard.field.get_user_group') }}'"
        :url_get_states = "'{{ route('dashboard.field.get_state') }}'"
        :url_delete = "'{{ route('dashboard.field.delete') }}'"
        :url_deshabilitar = "'{{ route('dashboard.field.deshabilitar') }}'"
        :url_habilitar = "'{{ route('dashboard.field.habilitar') }}'"
    ></field-table>

    <field-modal
        :url = "'{{ route('dashboard.field.store') }}'"
    ></field-modal>

    <loading></loading>

@endsection