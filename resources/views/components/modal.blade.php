<div class="modal fade" id="{{ $idModal }}" tabindex="-1" role="dialog" aria-labelledby="{{ $ariaLabelledby }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
        <form class="modal-content needs-validation" action="{{ $routeAction }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf
            <input id="id" name="id" style="display: none;">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $idTitle }}">{{ $textTitle }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                {{ $slot }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>