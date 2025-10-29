@extends('layouts.master')
@section('title')
    @lang('translation.Timeline')
@endsection
@section('css')
    <!-- DataTables -->
    <link href="{{ URL::asset('/assets/libs/owl-carousel/owl-carousel.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('/assets/libs/spectrum-colorpicker/spectrum-colorpicker.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    @component('common-components.breadcrumb')
        @slot('pagetitle')
            Empresa
        @endslot
        @slot('title')
            Configuracion
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-5">Mi Empresa</h4>

                    <form class="needs-validation" action="{{ route('SaveCompany') }}" method="POST"
                        enctype="multipart/form-data" novalidate>
                        @csrf
                        <input type="text" id="id" name="id" value="{{ $company->id }}"
                            style="display: none;">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label" for="enterprise-name">Empresa</label>
                                    <input type="text" class="form-control" id="enterprise-name" name="name"
                                        placeholder="Empresa" value="{{ $company->name }}" required>
                                    <div class="valid-feedback">Valido!</div>
                                    <div class="invalid-feedback">El nombre de la empresa es requerido.</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="enterprise-contact" class="form-label">Contacto</label>
                                    <input class="form-control" type="tel" value="{{ $company->contact }}"
                                        placeholder="1-(555)-555-5555" id="enterprise-contact" name="contact" required>
                                    <div class="valid-feedback">Valido!</div>
                                    <div class="invalid-feedback">El numero de contacto de la empresa es requerido.</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label" for="country">País</label>
                                    <select class="form-select" id="country" name="pais" value="{{ $company->pais }}"
                                        required>
                                        <option disabled>Seleccionar</option>
                                        <option value="Bolivia" {{ $company->pais == 'Bolivia' ? 'selected' : '' }}>Bolivia
                                        </option>
                                        <option value="Chile" {{ $company->pais == 'Chile' ? 'selected' : '' }}>Chile
                                        </option>
                                        <option value="Ecuador" {{ $company->pais == 'Ecuador' ? 'selected' : '' }}>Ecuador
                                        </option>
                                        <option value="España" {{ $company->pais == 'España' ? 'selected' : '' }}>España
                                        </option>
                                        <option value="Global" {{ $company->pais == 'Global' ? 'selected' : '' }}>Global
                                        </option>
                                        <option value="México" {{ $company->pais == 'México' ? 'selected' : '' }}>México
                                        </option>
                                        <option value="Perú" {{ $company->pais == 'Perú' ? 'selected' : '' }}>Perú
                                        </option>
                                        <option value="Venezuela" {{ $company->pais == 'Venezuela' ? 'selected' : '' }}>
                                            Venezuela</option>
                                    </select>
                                    <div class="valid-feedback">Valido!</div>
                                    <div class="invalid-feedback">El país de la empresa es requerido.</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label" for="type-attendance">Tipo de asistencia</label>
                                    <select class="form-select" id="type-attendance" name="asist_type"
                                        value="{{ $company->asist_type }}" required>
                                        <option disabled>Seleccionar</option>
                                        <option value="Login/Logout al Sistema"
                                            {{ $company->asist_type == 'Login/Logout al Sistema' ? 'selected' : '' }}>
                                            Login/Logout al Sistema</option>
                                        <option value="Huella dactilar"
                                            {{ $company->asist_type == 'Huella dactilar' ? 'selected' : '' }}>Huella
                                            dactilar</option>
                                    </select>
                                    <div class="valid-feedback">Valido!</div>
                                    <div class="invalid-feedback">El tipo de asistencia de la empresa es requerido.</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label" for="user-suffix">Sufijo de usuario</label>
                                    <input type="text" class="form-control" id="user-suffix" name="sufijo"
                                        placeholder="@dominio.com" value="{{ $company->sufijo }}" required>
                                    <div class="valid-feedback">Valido!</div>
                                    <div class="invalid-feedback">El sufijo del usuario es requerido.</div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label" for="logo">Logo de la Empresa</label>
                                    <input type="file" class="form-control" id="logo" name="logo" accept="image/*" value="{{ $company->logo }}">
                                </div>
                            </div>
                            

                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label" for="logo">Logo de la Empresa</label>
                                    <img src="{{ URL::asset('assets/photos/'. $company->logo) }}" alt="logo"
                                        style="width: 100%; height: auto;">
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary" type="submit">Guardar</button> 
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->
@endsection
@section('script')
    <script src="{{ URL::asset('/assets/libs/parsleyjs/parsleyjs.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/spectrum-colorpicker/spectrum-colorpicker.min.js') }}"></script>
    <script>
        const forms = document.getElementsByClassName('needs-validation');
        $(document).ready(function() {
            Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        });
    </script>
@endsection
