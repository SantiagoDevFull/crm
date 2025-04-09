<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sicacenter</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">-->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.3/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="min-h-screen position-relative">
    <div class="position-fixed w-100 h-screen d-flex justify-content-center align-items-center" style="z-index: -10; top: 50%; left: 50%; transform: translateX(-50%) translateY(-50%);">
        <img id="root-body" class src="" alt="">
    </div>

    @include('includes.navegation')

    <div class="container mt-5 bg-white p-4 border rounded">
        <span class="badge bg-dark fs-4 text-white">@yield('title')</span>
        <span>:::</span>
        <span class="badge bg-warning fs-4">@yield('subtitle')</span>
        
    </div>

    <main id="app">
        @yield('content')
    </main>

    <script src="{{ asset('js/app.js') }}"></script>
    <script src="https://cdn.datatables.net/2.1.3/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.1.3/js/dataTables.bootstrap5.js"></script>

    <script>

        new DataTable('#example');

        const bodyRoot = document.getElementById('root-body');
        const header = document.getElementById('navbar-header');
        const menu = document.getElementById('offcanvasDarkNavbar');
        const principalLink = document.getElementById('principal-link');
        const closesMenuButton = [...document.querySelectorAll('.close-menu-btn')];
        const linesMenuButton = [...document.querySelectorAll('.lines-btn-menu')];
        const navLinks = [...document.querySelectorAll('.nav-link')];
        let menuNavigation = document.getElementById('menu-navigation');

        let bg = undefined;
        let color = undefined;

        async function getDataInit() {

            try {
                const res = await axios.get('/empresa/paleta-de-colores');
                const { data } = res;

                if (data) {
                    bg = data.menu_color;
                    color = data.text_color;

                    window.configurationColors = {
                        bg,
                        color
                    }

                    header.style.backgroundColor = data.menu_color;
                    header.style.color = data.text_color;
                    menu.style.backgroundColor = data.menu_color;
                    menu.style.color = data.text_color;
                    principalLink.style.color = data.text_color;

                    for (let i = 0; i < linesMenuButton.length; i++) {
                        const LMB = linesMenuButton[i];
                        LMB.style.backgroundColor = data.text_color;
                    };

                    for (let i = 0; i < closesMenuButton.length; i++) {
                        const LMB = closesMenuButton[i];
                        LMB.style.backgroundColor = data.text_color;
                    };

                    for (let i = 0; i < navLinks.length; i++) {
                        const LMB = navLinks[i];
                        LMB.style.color = data.text_color;
                    };
                };
            } catch (error) {
                console.log(error);
            };

            axios.get('/empresa/ver-logo')
                .then(function (response) {
                    const { data } = response;

                    if (data) {
                        const fileName = data.path.replace("uploads/","");

                        bodyRoot.src = `/empresa/logo/${fileName}`;
                    };
                })
                .catch(function (error) {
                    console.log(error);
                });

            let path = window.location.pathname;

            if (path.includes("sales") || path.includes("enterprise")) {
                const buttonOpenMenu = document.getElementById("button-open-menu");

                buttonOpenMenu.classList.remove("d-none");
                axios.get('/grupo-usuarios/grupo')
                    .then(function (response) {
                        const { data } = response;

                        if (data) {
                            const { group } = data;

                            const { permissions } = group;
                            const permissionsParse = JSON.parse(permissions);

                            if (path.includes("sales")) {
                                console.log("sales")
                                let li = document.createElement('li');
                                let a = document.createElement('a');

                                li.className = 'nav-item dropdown';

                                a.className = 'nav-link';
                                a.href = "{{ route('dashboard.home.sales') }}";
                                a.textContent = "Inicio"

                                if (color) a.style.color = color;

                                li.appendChild(a);
                                menuNavigation.appendChild(li);
                            } else if (path.includes("enterprise")) {
                                let li = document.createElement('li');
                                let a = document.createElement('a');

                                li.className = 'nav-item dropdown';

                                a.className = 'nav-link';
                                a.href = "{{ route('dashboard.home.enterprise') }}";
                                a.textContent = "Inicio";

                                if (color) a.style.color = color;

                                li.appendChild(a);
                                menuNavigation.appendChild(li);
                            };

                            const { enterprise_configuration, presence_configuration, administration_configuration, collaborative, campaign_configuration } = permissionsParse;

                            if (enterprise_configuration.length && path.includes("enterprise")) {
                                let li = document.createElement('li');
                                let a = document.createElement('a');
                                let ul = document.createElement('ul');
                                let ul_li = document.createElement('li');
                                let ul_li_a = document.createElement('a');

                                li.className = 'nav-item dropdown';

                                a.className = 'nav-link dropdown-toggle';
                                a.href = '#';
                                a.textContent = "Configuración de Empresa"
                                a.setAttribute("role", 'button');
                                a.setAttribute("data-bs-toggle", 'dropdown');
                                a.setAttribute("aria-expanded", 'false');

                                if (color) a.style.color = color;

                                ul.className = 'dropdown-menu dropdown-menu-dark';

                                ul_li_a.className = 'dropdown-item';
                                ul_li_a.href = `{{ route('dashboard.company.index') }}`;
                                ul_li_a.textContent = "Mi Empresa";

                                if (color) ul_li_a.style.color = color;

                                ul_li.appendChild(ul_li_a);
                                ul.appendChild(ul_li);
                                li.appendChild(a);
                                li.appendChild(ul);

                                menuNavigation.appendChild(li);
                            };

                            if (presence_configuration.length && path.includes("enterprise")) {
                                let li = document.createElement('li');
                                let a = document.createElement('a');
                                let ul = document.createElement('ul');

                                li.className = 'nav-item dropdown';

                                a.className = 'nav-link dropdown-toggle';
                                a.href = '#';
                                a.textContent = "Configuración de Asistencia"
                                a.setAttribute("role", 'button');
                                a.setAttribute("data-bs-toggle", 'dropdown');
                                a.setAttribute("aria-expanded", 'false');

                                if (color) a.style.color = color;

                                ul.className = 'dropdown-menu dropdown-menu-dark';

                                for (let i = 0; i < presence_configuration.length; i++) {
                                    const element = presence_configuration[i];
    
                                    let ul_li = document.createElement('li');
                                    let ul_li_a = document.createElement('a');
    
                                    if (color) ul_li_a.style.color = color;
    
                                    if (element == "hours") {
    
                                        ul_li_a.className = 'dropdown-item';
                                        ul_li_a.href = `{{ route('dashboard.horario.index') }}`;
                                        ul_li_a.textContent = "Horarios";
    
                                        ul_li.appendChild(ul_li_a);
                                        ul.appendChild(ul_li);
                                    } else if (element == "disconnection") {
    
                                        ul_li_a.className = 'dropdown-item';
                                        ul_li_a.href = `{{ route('dashboard.company.index') }}`;
                                        ul_li_a.textContent = "Tipos de Desconexión";
    
                                        ul_li.appendChild(ul_li_a);
                                        ul.appendChild(ul_li);
                                    } else if (element == "sedes") {
    
                                        ul_li_a.className = 'dropdown-item';
                                        ul_li_a.href = `{{ route('dashboard.company.index') }}`;
                                        ul_li_a.textContent = "Sedes";
    
                                        ul_li.appendChild(ul_li_a);
                                        ul.appendChild(ul_li);
                                    };
                                };

                                li.appendChild(a);
                                li.appendChild(ul);

                                menuNavigation.appendChild(li);
                            };

                            if (administration_configuration.length && path.includes("enterprise")) {
                                let li = document.createElement('li');
                                let a = document.createElement('a');
                                let ul = document.createElement('ul');
        
                                li.className = 'nav-item dropdown';
        
                                a.className = 'nav-link dropdown-toggle';
                                a.href = '#';
                                a.textContent = "Administración de Usuarios"
                                a.setAttribute("role", 'button');
                                a.setAttribute("data-bs-toggle", 'dropdown');
                                a.setAttribute("aria-expanded", 'false');
        
                                if (color) a.style.color = color;
    
                                ul.className = 'dropdown-menu dropdown-menu-dark';
        
                                for (let i = 0; i < administration_configuration.length; i++) {
                                    const element = administration_configuration[i];
        
                                    let ul_li = document.createElement('li');
                                    let ul_li_a = document.createElement('a');
        
                                    if (color) ul_li_a.style.color = color;
    
                                    if (element == "user_groups") {
        
                                        ul_li_a.className = 'dropdown-item';
                                        ul_li_a.href = `{{ route('dashboard.group.user.index') }}`;
                                        ul_li_a.textContent = "Grupos de Usuarios";
        
                                        ul_li.appendChild(ul_li_a);
                                        ul.appendChild(ul_li);
                                    } else if (element == "users") {
        
                                        ul_li_a.className = 'dropdown-item';
                                        ul_li_a.href = `{{ route('dashboard.user.index') }}`;
                                        ul_li_a.textContent = "Usuarios";
        
                                        ul_li.appendChild(ul_li_a);
                                        ul.appendChild(ul_li);
                                    };
                                };
        
                                li.appendChild(a);
                                li.appendChild(ul);
        
                                menuNavigation.appendChild(li);
                            };

                            if (collaborative.length && path.includes("enterprise")) {
                                let li = document.createElement('li');
                                let a = document.createElement('a');
                                let ul = document.createElement('ul');
        
                                li.className = 'nav-item dropdown';
        
                                a.className = 'nav-link dropdown-toggle';
                                a.href = '#';
                                a.textContent = "Colaborativo"
                                a.setAttribute("role", 'button');
                                a.setAttribute("data-bs-toggle", 'dropdown');
                                a.setAttribute("aria-expanded", 'false');
        
                                if (color) a.style.color = color;
    
                                ul.className = 'dropdown-menu dropdown-menu-dark';
        
                                for (let i = 0; i < collaborative.length; i++) {
                                    const element = collaborative[i];
        
                                    let ul_li = document.createElement('li');
                                    let ul_li_a = document.createElement('a');
        
                                    if (color) ul_li_a.style.color = color;
    
                                    if (element == "advertisements") {
        
                                        ul_li_a.className = 'dropdown-item';
                                        ul_li_a.href = `{{ route('dashboard.advertisement.index') }}`;
                                        ul_li_a.textContent = "Anuncios";
        
                                        ul_li.appendChild(ul_li_a);
                                        ul.appendChild(ul_li);
                                    } else if (element == "popups_welcome") {
        
                                        ul_li_a.className = 'dropdown-item';
                                        ul_li_a.href = `{{ route('dashboard.company.index') }}`;
                                        ul_li_a.textContent = "Popups de Bienvenida";
        
                                        ul_li.appendChild(ul_li_a);
                                        ul.appendChild(ul_li);
                                    };
                                };
        
                                li.appendChild(a);
                                li.appendChild(ul);
        
                                menuNavigation.appendChild(li);
                            };

                            if (campaign_configuration.length && path.includes("sales")) {
                                let li = document.createElement('li');
                                let a = document.createElement('a');
                                let ul = document.createElement('ul');
        
                                li.className = 'nav-item dropdown';
        
                                a.className = 'nav-link dropdown-toggle';
                                a.href = '#';
                                a.textContent = "Configuración de Campañas"
                                a.setAttribute("role", 'button');
                                a.setAttribute("data-bs-toggle", 'dropdown');
                                a.setAttribute("aria-expanded", 'false');
        
                                if (color) a.style.color = color;
    
                                ul.className = 'dropdown-menu dropdown-menu-dark';
        
                                for (let i = 0; i < campaign_configuration.length; i++) {
                                    const element = campaign_configuration[i];
        
                                    let ul_li = document.createElement('li');
                                    let ul_li_a = document.createElement('a');
        
                                    if (color) ul_li_a.style.color = color;
    
                                    if (element == "campaign") {
        
                                        ul_li_a.className = 'dropdown-item';
                                        ul_li_a.href = `{{ route('dashboard.campain.index') }}`;
                                        ul_li_a.textContent = "Campañas";
        
                                        ul_li.appendChild(ul_li_a);
                                        ul.appendChild(ul_li);
                                    } else if (element == "tab_states") {
        
                                        ul_li_a.className = 'dropdown-item';
                                        ul_li_a.href = `{{ route('dashboard.tab_state.index') }}`;
                                        ul_li_a.textContent = "Pestañas de Estado";
        
                                        ul_li.appendChild(ul_li_a);
                                        ul.appendChild(ul_li);
                                    } else if (element == "states") {
        
                                        ul_li_a.className = 'dropdown-item';
                                        ul_li_a.href = `{{ route('dashboard.state.index') }}`;
                                        ul_li_a.textContent = "Estados";
        
                                        ul_li.appendChild(ul_li_a);
                                        ul.appendChild(ul_li);
                                    } else if (element == "blocks") {
        
                                        ul_li_a.className = 'dropdown-item';
                                        ul_li_a.href = `{{ route('dashboard.block.index') }}`;
                                        ul_li_a.textContent = "Bloques de Campos";
        
                                        ul_li.appendChild(ul_li_a);
                                        ul.appendChild(ul_li);
                                    } else if (element == "fields") {
        
                                        ul_li_a.className = 'dropdown-item';
                                        ul_li_a.href = `{{ route('dashboard.field.index') }}`;
                                        ul_li_a.textContent = "Campos";
        
                                        ul_li.appendChild(ul_li_a);
                                        ul.appendChild(ul_li);
                                    };
                                };
        
                                li.appendChild(a);
                                li.appendChild(ul);
        
                                menuNavigation.appendChild(li);
                            };

                            if (path.includes("enterprise")) {
                                let li = document.createElement('li');
                                let a = document.createElement('a');
                                let ul = document.createElement('ul');
                                let ul_li = document.createElement('li');
                                let ul_li_a = document.createElement('a');

                                li.className = 'nav-item dropdown';

                                a.className = 'nav-link dropdown-toggle';
                                a.href = '#';
                                a.textContent = "Reportes"
                                a.setAttribute("role", 'button');
                                a.setAttribute("data-bs-toggle", 'dropdown');
                                a.setAttribute("aria-expanded", 'false');

                                if (color) a.style.color = color;

                                ul.className = 'dropdown-menu dropdown-menu-dark';

                                ul_li_a.className = 'dropdown-item';
                                ul_li_a.href = `{{ route('dashboard.user.index') }}`;
                                ul_li_a.textContent = "En construcción";

                                if (color) ul_li_a.style.color = color;

                                ul_li.appendChild(ul_li_a);
                                ul.appendChild(ul_li);
                                li.appendChild(a);
                                li.appendChild(ul);

                                menuNavigation.appendChild(li);
                            };
                        };
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

                if (path.includes("sales")) {
                    axios.post('/listar-campanias')
                        .then(function (response) {
            
                            let campanias = response.data;
            
                            let li = document.createElement('li');
                            let a = document.createElement('a');
                            let ul = document.createElement('ul');
            
                            li.className = 'nav-item dropdown';
            
                            a.className = 'nav-link dropdown-toggle';
                            a.href = '#';
                            a.textContent = "Venta"
                            a.setAttribute("role", 'button');
                            a.setAttribute("data-bs-toggle", 'dropdown');
                            a.setAttribute("aria-expanded", 'false');
            
                            if (color) a.style.color = color;
        
                            ul.className = 'dropdown-menu dropdown-menu-dark';
            
                            campanias.forEach(function(campania) {
                                let listItem = document.createElement('li');
                                let link = document.createElement('a');
        
                                link.className = 'dropdown-item';
                                link.href = `/sales/solds/${campania.id}`;
                                link.textContent = campania.name;
        
                                if (color) link.style.color = color;
        
                                listItem.appendChild(link);
                                ul.appendChild(listItem);
                            });
            
                            li.appendChild(a);
                            li.appendChild(ul);
            
                            menuNavigation.appendChild(li);
                        })
                        .catch(function (error) {
                            console.log(error);
                        });
                };
            } else {
                axios.get('/grupo-usuarios/grupo')
                    .then(function (response) {
                        const { data } = response;

                        if (data) {
                            const { group, horario } = data;
                            const { inicio, final } = horario;

                            const times_init = inicio.split(":");
                            const hour_init = +times_init[0];
                            const minute_init = +times_init[1];
                            const second_init = +times_init[2];

                            const times_end = final.split(":");
                            const hour_end = +times_end[0];
                            const minute_end = +times_end[1];
                            const second_end = +times_end[2];

                            let init = new Date();
                            let end = new Date();

                            init.setHours(hour_init);
                            init.setMinutes(minute_init);
                            init.setSeconds(second_init);
                            init = init.getTime();

                            end.setHours(hour_end);
                            end.setMinutes(minute_end);
                            end.setSeconds(second_end);
                            end = end.getTime();

                            const today = new Date().getTime();

                            if (today < init || today > end) {
                                window.Swal.fire({
                                    title: "Fuera de Horario",
                                    text: "Usted se encuentra fuera de su horario laborable",
                                    icon: "warning",
                                    heightAuto: false,
                                });
                            };
                        };
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            };

        }

        getDataInit()
    </script>

</body>

</html>