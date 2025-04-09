@extends('includes.app')

@section('title','Colaborativo')
@section('subtitle','Anuncios')

@section('content')

    <advertisement-table
        :url_get_advertisement = "'{{ route('dashboard.advertisement.getAdvertisement') }}'"
        :url_delete = "'{{ route('dashboard.advertisement.delete') }}'"
        :url_deshabilitar = "'{{ route('dashboard.advertisement.deshabilitar') }}'"
        :url_habilitar = "'{{ route('dashboard.advertisement.habilitar') }}'"
        :advertisements = "{{ $advertisements }}"
    ></advertisement-table>

    <advertisement-modal
        :url = "'{{ route('dashboard.advertisement.store') }}'"
        :url_upload = "'{{ route('dashboard.sold.upload') }}'"
        :groups = "{{ $groups }}"
    ></advertisement-modal>
    <loading></loading>

@endsection