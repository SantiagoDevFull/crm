@extends('includes.app')

@section('title','Administración de Usuarios')
@section('subtitle','Usuarios')

@section('content')
    <user-table
    :users = "{{ $users }}"
    :url_delete = "'{{ route('dashboard.user.delete') }}'"
    :url_list_group = "'{{ route('dashboard.user.list.group') }}'"
    >
    </user-table>

    <user-modal
    :company="{{ $company }}"
    :url="' {{ route('dashboard.user.store') }} '"
    ></user-modal>

    <user-modal-group
    :groups_general="{{ $groups_general }}"
    :url_delete_group="'{{ route('dashboard.user.delete.group') }}'"
    :url="'{{ route('dashboard.user.add.group') }}'"
    ></user-modal-group>

    <loading></loading>

@endsection