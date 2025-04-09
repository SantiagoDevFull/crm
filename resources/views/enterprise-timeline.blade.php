@extends('layouts.master')
@section('title') @lang('translation.Dashboard') @endsection
@section('content')
@component('common-components.breadcrumb')
@slot('pagetitle') Empresa @endslot
@slot('title') Empresa @endslot
@endcomponent

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <ul class="verti-timeline list-unstyled">
                    @foreach ($logins as $login)
                        <li class="event-list">
                            <div class="event-date text-primar">
                                {{ \Carbon\Carbon::parse($login->created_at)->format('d/m H:i') }}
                            </div>
                            <h5>{{ $login->created_at_user }} | {{ $login->login ? 'Inició Sesión' : 'Cerró Sesión' }}</h5>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection
@section('script')
@endsection
