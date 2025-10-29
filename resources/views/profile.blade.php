@extends('layouts.master')
@section('title')
@lang('translation.Profile')
@endsection

@section('content')
@component('common-components.breadcrumb')
@slot('pagetitle') Perfil @endslot
@slot('title') Perfil @endslot
@endcomponent

<div class="row mb-4">
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-center">
                    <!-- <div class="dropdown float-end">
                        <a class="text-body dropdown-toggle font-size-18" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true">
                            <i class="uil uil-ellipsis-v"></i>
                        </a>

                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="#">Edit</a>
                            <a class="dropdown-item" href="#">Action</a>
                            <a class="dropdown-item" href="#">Remove</a>
                        </div>
                    </div> -->
                    <div class="clearfix"></div>
                    <div id="image-profile-button" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target=".image-profile">
                        @if ($user->foto_perfil)
                            <img src="{{ URL::asset('assets/photos/'.$user->foto_perfil) }}" alt="{{ $user->name }}" class="avatar-lg rounded-circle img-thumbnail">
                        @else
                            <div class="avatar-lg mx-auto mb-4">
                                <div class="avatar-title bg-primary-subtle rounded-circle text-primary">
                                    <i class="mdi mdi-account-circle display-4 m-0 text-primary"></i>
                                </div>
                            </div>
                        @endif
                    </div>
                    <h5 class="mt-3 mb-1">{{ $user->name }}</h5>
                    <!-- <p class="text-muted">UI/UX Designer</p> -->

                    <!-- <div class="mt-4">
                        <button type="button" class="btn btn-light btn-sm"><i class="uil uil-envelope-alt me-2"></i>
                            Message</button>
                    </div> -->
                </div>

                <hr class="my-4">

                <div class="text-muted">
                    <div class="table-responsive mt-4">
                        <div>
                            <p class="mb-1">Nombre :</p>
                            <h5 class="font-size-16">{{ $user->name }}</h5>
                        </div>
                        <div class="mt-4">
                            <p class="mb-1">Correo :</p>
                            <h5 class="font-size-16">{{ $user->email }}</h5>
                        </div>
                        <div class="mt-4">
                            <p class="mb-1">Grupo :</p>
                            <h5 class="font-size-16">{{ $group->name }}</h5>
                        </div>
                        <div class="mt-4">
                            <p class="mb-1">Mobil :</p>
                            <h5 class="font-size-16">{{ $user->telefono }}</h5>
                        </div>
                        <div class="mt-4">
                            <p class="mb-1">Genero :</p>
                            <h5 class="font-size-16">{{ $user->genero }}</h5>
                        </div>
                        <div class="mt-4">
                            <p class="mb-1">Fecha de Nacimiento :</p>
                            <h5 class="font-size-16">{{ $user->fecha_naci }}</h5>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-8">
        <div class="card mb-0">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#attendance" role="tab">
                        <i class="uil uil-calender font-size-20"></i>
                        <span class="d-none d-sm-block">Asistencia</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#advertisement" role="tab">
                        <i class="uil uil-bell font-size-20"></i>
                        <span class="d-none d-sm-block">Anuncios</span>
                    </a>
                </li>
            </ul>
            <!-- Tab content -->
            <div class="tab-content p-4">
                <div class="tab-pane active" id="attendance" role="tabpanel">
                    <div>
                        <div>
                            <h5 class="font-size-16 mb-4">Asistencia</h5>

                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="advertisement" role="tabpanel">
                    <div>
                        <div>
                            <h5 class="font-size-16 mb-4">Anuncios</h5>

                            <div>
                                <ul class="verti-timeline list-unstyled">
                                    @foreach($advertisements as $advertisement)
                                        <li class="event-list">
                                            <div class="event-date text-primar">{{ $advertisement->created_at->format('d M') }}</div>
                                            <h5>{{ $advertisement->title }}</h5>
                                            <p class="text-muted">{!! $advertisement->text !!}</p>
                                            @if ($advertisement->file_name)
                                                <div>
                                                    <a href="/storage/uploads/{{ $advertisement->file_name }}" target="_blank" class="btn btn-success waves-effect waves-light mb-3">Descargar Adjunto</a>
                                                </div>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade image-profile" id="image-profile" tabindex="-1" role="dialog" aria-labelledby="image-profile" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
        <form class="modal-content" action="{{ route('SaveImageProfile') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input id="id" name="id" style="display: none;" value="{{ $user->id }}">
            <div class="modal-header">
                <h5 class="modal-title">Imagen de Perfil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label" for="foto_perfil">Foto de Perfil</label>
                            <input type="file" class="form-control" id="foto_perfil" name="foto_perfil" accept="image/*" required>
                        </div>
                        <img id="preview-profile" src="{{ url('/storage/uploads', $user->foto_perfil) }}" alt="{{ $user->foto_perfil }}" style="width: 100%; height: auto;">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>
<!-- end row -->
@endsection
@section('script')
    <!-- fullcalendar -->
    <script src="{{ URL::asset('/assets/libs/fullcalendar/fullcalendar.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: @json($events),
                eventContent: function(arg) {
                    const event = arg.event.extendedProps;

                    let iconHtml = '';

                    if (event.icon) {
                        iconHtml = `<i class="uil ${event.icon} font-size-15"></i>`;
                    };

                    let customContent = document.createElement('div');

                    customContent.style.backgroundColor = event.color;
                    customContent.className = "text-start";
                    customContent.innerHTML = iconHtml + `<span>${arg.event.title}</span>`;

                    return { domNodes: [customContent] };
                }
            });
            calendar.render();

            $('#foto_perfil').on('change', function() {
                const file = this.files[0];

                if (file) {
                    const reader = new FileReader();

                    reader.onload = function (e) {
                        $('#preview-profile').attr('src', e.target.result).show();
                    };

                    reader.readAsDataURL(file);
                };
            })
        });
    </script>
@endsection
