@extends('includes.app')

@section('title','Configuración de Asistencia')
@section('subtitle','Horarios')

@section('content')
    <horario-table
    :horarios = "{{ $horarios }}"
    :url_delete = "'{{ route('dashboard.horario.delete') }}'"
    >
    </horario-table>

    <horario-modal
    :url="' {{ route('dashboard.horario.store') }} '"
    :url_get_day="' {{ route('dashboard.horario.get.day') }} '"
    ></horario-modal>


    <loading></loading>

@endsection
