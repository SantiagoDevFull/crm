<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .divider:after,
        .divider:before {
            content: "";
            flex: 1;
            height: 1px;
            background: #eee;
        }

        .h-custom {
            height: calc(100% - 73px);
        }

        @media (max-width: 450px) {
            .h-custom {
                height: 100%;
            }
        }
    </style>
</head>

<body>

    <section class="vh-100">
        <div class="container-fluid h-custom">
            <div class="row d-flex flex-row justify-content-center align-items-center h-100">
                <div class="col-md-6 col-lg-5 col-xl-4">
                    <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/draw2.webp"
                        class="img-fluid" alt="Sample image">
                </div>
                <div class="col-md-6 col-lg-6 col-xl-4 offset-xl-1">
                    <form onsubmit="login(event)">
                        <div
                            class="d-flex flex-row align-items-center justify-content-center justify-content-lg-center">
                            <p class="lead fw-normal mb-0 me-3">Iniciar sesión</p>
                        </div>

                        <div class="divider d-flex align-items-center my-4">
                            <p class="text-center fw-bold mx-3 mb-0"></p>
                        </div>

                        <!-- Email input -->
                        <div data-mdb-input-init class="form-outline mb-4">
                            <input type="text" id="user" name="user" class="form-control form-control-lg"
                                placeholder="Introducir un Usuario" />
                            <label class="form-label" for="form3Example3">Usuario:</label>
                        </div>

                        <!-- Password input -->
                        <div data-mdb-input-init class="form-outline mb-3">
                            <input type="password" id="password" name="password" class="form-control form-control-lg"
                                placeholder="Introducir contraseña" />
                            <label class="form-label" for="form3Example4">Contraseña</label>
                        </div>


                        <div class="text-center text-lg-start mt-4 pt-2">
                            <button type="submit" data-mdb-button-init data-mdb-ripple-init
                                class="btn btn-primary btn-lg"
                                style="padding-left: 2.5rem; padding-right: 2.5rem;">Ingresar</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        <div
            class="d-flex flex-column flex-md-row text-center text-md-start justify-content-between py-4 px-4 px-xl-5 bg-primary">
            <div class="text-white mb-3 mb-md-0">
                Copyright © 2024. Sicacenter.
            </div>
        </div>
    </section>

    <script src="{{ asset('js/app.js') }}"></script>

    <script>
        function login(event) {
            event.preventDefault();

            const user = document.getElementById('user').value;
            const password = document.getElementById('password').value;

            axios.post('/autenticacion', {
                    user: user,
                    password: password
                })
                .then(function(response) {
                    console.log(response);

                    if (response.data.type == 3) {
                        Swal.fire({
                            title: response.data.title,
                            text: response.data.msg,
                            icon: "success",
                            timer: 2000,
                            heightAuto: false
                        }).then((confirmed) => {
                            window.location = response.data.url;
                        })
                    } else if (response.data.type == 2) {
                        Swal.fire({
                            title: response.data.title,
                            text: response.data.msg,
                            icon: "error",
                            heightAuto: false,
                        })
                    }

                })
                .catch(function(error) {
                    console.log(error);
                });
        }
    </script>

</body>

</html>
