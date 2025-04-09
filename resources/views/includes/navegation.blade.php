<!-- <nav id="navbar-header" class="navbar navbar-dark bg-dark"> -->
<nav id="navbar-header" class="navbar">
    <div class="container-fluid">
        <button id="button-open-menu" class="navbar-toggler d-flex flex-column px-2 gap-1 d-none" style="padding-top: 8px; padding-bottom: 8px;" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasDarkNavbar" aria-controls="offcanvasDarkNavbar">
            <div class="lines-btn-menu rounded-2" style="width: 25px; height: 3px; background-color: #FFF;"></div>
            <div class="lines-btn-menu rounded-2" style="width: 25px; height: 3px; background-color: #FFF;"></div>
            <div class="lines-btn-menu rounded-2" style="width: 25px; height: 3px; background-color: #FFF;"></div>
        </button>
        <a id="principal-link" class="navbar-brand" href="/system">Zicacenter</a>
        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasDarkNavbar" aria-labelledby="offcanvasDarkNavbarLabel">

            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasDarkNavbarLabel">Zicacenter</h5>
                <button type="button" class="position-relative btn" style="padding: 1rem; margin: -.5rem -.5rem -.5rem auto;" data-bs-dismiss="offcanvas" aria-label="Close">
                    <div class="close-menu-btn" style="
                        position: absolute;
                        width: 100%;
                        height: 3px;
                        top: 50%;
                        left: 0;
                        transform-origin: center;
                        border-radius: 5px;
                        transition: all .3s ease;
                        transform: rotate(45deg);
                    "></div>
                    <div class="close-menu-btn" style="
                        position: absolute;
                        width: 100%;
                        height: 3px;
                        top: 50%;
                        left: 0;
                        transform-origin: center;
                        border-radius: 5px;
                        transition: all .3s ease;
                        transform: rotate(-45deg);
                    "></div>
                </button>
            </div>

            <div class="container-fluid text-center">
                <hr>
                Bienvenido (a) <br>{{ Auth::user()->name }}<br><br>
                <i class="fa-solid fa-user-shield" style="color: #ffffff; font-size: 600%"></i><br><br>
                <a href="{{ route('logout') }}" class="btn btn-danger">Cerrar sesión</a>
                <hr>
            </div>

            <div class="offcanvas-body">
                <ul id="menu-navigation" class="navbar-nav justify-content-end flex-grow-1 pe-3"></ul>
            </div>
        </div>
    </div>
</nav>