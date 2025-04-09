@extends('includes.app')

@section('title','Configuración de Campañas')
@section('subtitle','Bloques de Campos')

@section('content')

    <block-form
        :campain_id = "{{ $campain_id }}"
        :campains = "{{ $campains }}"
        :url = "'{{ route('dashboard.block.list') }}'"
    ></block-form>

    <block-table
        :url = "'{{ route('dashboard.block.list') }}'"
        
        :url_get_block = "'{{ route('dashboard.block.get_block') }}'"
        :url_delete = "'{{ route('dashboard.block.delete') }}'"
        :url_deshabilitar = "'{{ route('dashboard.block.deshabilitar') }}'"
        :url_habilitar = "'{{ route('dashboard.block.habilitar') }}'"
    ></block-table>

    <block-modal
        :url = "'{{ route('dashboard.block.store') }}'"
    ></block-modal>

    <loading></loading>

@endsection