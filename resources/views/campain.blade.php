@extends('includes.app')

@section('title','Configuración de Campañas')
@section('subtitle','Campañas')

@section('content')

    <campain-table
        :campains = "{{ $campains }}"
        :url_get_campain = "'{{ route('dashboard.campain.getCampain') }}'"
        :url_delete = "'{{ route('dashboard.campain.delete') }}'"
        :url_deshabilitar = "'{{ route('dashboard.campain.deshabilitar') }}'"
        :url_habilitar = "'{{ route('dashboard.campain.habilitar') }}'"
    ></campain-table>

    <campain-modal
        :url = "'{{ route('dashboard.campain.store') }}'"
        :countries = "{{ $countries }}"
        :range_dates = "{{ $range_dates }}"
        :groups = "{{ $groups }}"
    ></campain-modal>

    <loading></loading>

@endsection