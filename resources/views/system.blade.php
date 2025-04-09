@extends('includes.app')

@section('title','Bienvenidos a Sicacenter')

@section('content')

<system
    :rutas = "{{ json_encode($rutas) }}"
></system>

@endsection