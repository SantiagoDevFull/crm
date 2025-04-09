<div class="row">
    <div class="col-12">
        <div class="card">
            <form class="card-body" id="{{ $idForm }}">
                <label class="form-label" for="{{ $idSelect }}">{{ $nameLabel }}:</label>
                <select class="form-select" id="{{ $idSelect }}" name="{{ $idSelect }}" value="0" required>
                    <option value="0" disabled selected>Seleccionar</option>
                    @foreach ($options as $option)
                        <option value="{{ $option->id }}">{{ $option->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-success waves-effect waves-light my-3">Buscar</button>
            </form>
        </div>
    </div>
</div>