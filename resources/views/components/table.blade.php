<link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <button type="button" class="btn btn-success waves-effect waves-light mb-3" id="{{ $idCreateButton }}" title="Edit" data-bs-toggle="modal" data-bs-target="#{{ $idModal }}">{{ $textButton }}</button>

                <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            @foreach ($headers as $head)
                                <th>{{ $head }}</th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody>
                        {{ $slot }}
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

<script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>