@extends('includes.app')

@section('title', $campain["name"])
@section('subtitle', 'Crear')

@section('content')

    <sold-form
        :campain = "{{ $campain }}"
        :tab_state = "{{ $tab_state }}"
        :states = "{{ $states }}"
        :blocks = "{{ $blocks }}"
        :fields = "{{ $fields }}"
        :model = "{{ json_encode($model) }}"
        :url = "'{{ route('dashboard.sold.store') }}'"
        :url_list = "'{{ route('dashboard.sold.index') }}'"
        :url_upload = "'{{ route('dashboard.sold.upload') }}'"
        :id = "{{ $id }}"
        :state_id = "{{ $state_id }}"
    ></sold-form>

    <loading></loading>

@endsection