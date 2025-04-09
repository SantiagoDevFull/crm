@extends('includes.app')

@section('title','Configuración de Campañas')
@section('subtitle','Pestañas de Estado')

@section('content')

    <tab-state-form
        :campain_id = "{{ $campain_id }}"
        :campains = "{{ $campains }}"
        :url = "'{{ route('dashboard.tab_state.list') }}'"
    >
    </tab-state-form>

    <tab-state-table
        :url = "'{{ route('dashboard.tab_state.list') }}'"
        :url_get_tab_state = "'{{ route('dashboard.tab_state.get_tab_state') }}'"
        :url_delete = "'{{ route('dashboard.tab_state.delete') }}'"
        :url_deshabilitar = "'{{ route('dashboard.tab_state.deshabilitar') }}'"
        :url_habilitar = "'{{ route('dashboard.tab_state.habilitar') }}'"
    ></tab-state-table>

    <tab-state-modal
        :url = "'{{ route('dashboard.tab_state.store') }}'"
    ></tab-state-modal>

    <loading></loading>

@endsection