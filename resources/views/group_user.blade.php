@extends('includes.app')

@section('title','Administración de Usuarios')
@section('subtitle','Grupo de Usuarios')

@section('content')
    
    <group-user-table
        :groups ="{{ $groups }}"
        :url_delete ="'{{ route('dashboard.group.user.delete') }}'"
    ></group-user-table>

    <group-user-modal
        :companies = "{{ $companies }}"
        :hours ="{{ $hours }}"
        :url="'{{ route('dashboard.group.user.store') }}'"
    ></group-user-modal>

    <loading></loading>

@endsection