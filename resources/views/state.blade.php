@extends('includes.app')

@section('title','Configuración de Campañas')
@section('subtitle','Estados')

@section('content')

    <state-form
        :campain_id = "{{ $campain_id }}"
        :campains = "{{ $campains }}"
        :url = "'{{ route('dashboard.state.list') }}'"
    >
    </state-form>

    <state-table
        :url = "'{{ route('dashboard.state.list') }}'"
        :url_get_state = "'{{ route('dashboard.state.get_state') }}'"
        :url_delete = "'{{ route('dashboard.state.delete') }}'"
        :url_deshabilitar = "'{{ route('dashboard.state.deshabilitar') }}'"
        :url_habilitar = "'{{ route('dashboard.state.habilitar') }}'"
    ></state-table>

    <state-modal
        :url = "'{{ route('dashboard.state.store') }}'"
        :url_get_tab_states = "'{{ route('dashboard.state.get_tab_state') }}'"
    ></state-modal>

    <loading></loading>

@endsection