@extends('layouts.master')
@section('title') @lang('translation.Dashboard') @endsection
@section('content')
@component('common-components.breadcrumb')
@slot('pagetitle') Ventas @endslot
@slot('title') Ventas @endslot
@endcomponent

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <ul class="verti-timeline list-unstyled">
                    @foreach ($forms as $form)
                        <li class="event-list">
                            <a href="{{ url('sales/solds', [$form->campain_id, $form->tab_state_id, $form->id]) }}">
                                <div class="event-date text-primar">
                                    {{ \Carbon\Carbon::parse($form->created_at)->format('d/m H:i') }}
                                </div>
                                <h5>{{ $form->created_at_user }} | Ficha creada</h5>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection
@section('script')
    <script>
        const forms = @json($forms);
        console.log(forms)
    </script>
@endsection
