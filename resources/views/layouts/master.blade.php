<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.title-meta')
    @include('layouts.head')
</head>

@section('body')

    <body>
    @show

    <!-- Begin page -->
    <div id="layout-wrapper">
        @include('layouts.topbar')
        @include('layouts.sidebar')
        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @yield('content')
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->
            @include('layouts.footer')
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->

    <!-- Right Sidebar -->
    @include('layouts.right-sidebar')
    <!-- /Right-bar -->

    <!-- JAVASCRIPT -->
    @include('layouts.vendor-scripts')
</body>

<script>
    function saveBodyAttributes() {
        const body = document.body;
        const attributes = {};
        Array.from(body.attributes).forEach(attr => {
            if (attr.name.startsWith('data-')) {
                attributes[attr.name] = attr.value;
            }
        });
        localStorage.setItem('bodyAttributes', JSON.stringify(attributes));
    }

    function restoreBodyAttributes() {
        const body = document.body;
        const attributes = JSON.parse(localStorage.getItem('bodyAttributes') || '{}');
        Object.entries(attributes).forEach(([key, value]) => {
            if (document.querySelector(`.${key}-${value}`)) document.querySelector(`.${key}-${value}`).checked = true;
            body.setAttribute(key, value);
        });
    }

    document.addEventListener('DOMContentLoaded', restoreBodyAttributes);

    document.querySelectorAll('.form-check-inline .form-check-input').forEach(button => {
        button.addEventListener('click', saveBodyAttributes);
    });
    document.querySelectorAll('.sidebar-setting .form-check-input').forEach(button => {
        button.addEventListener('click', saveBodyAttributes);
    });

    $('#data-light').on('click', function(e) {
        document.body.setAttribute('data-bs-theme', 'light');
        document.body.setAttribute('data-topbar', 'light');
        document.body.setAttribute('data-sidebar', 'light');
    });
    $('#data-dark').on('click', function(e) {
        document.body.setAttribute('data-bs-theme', 'dark');
        document.body.setAttribute('data-topbar', 'dark');
        document.body.setAttribute('data-sidebar', 'dark');
    });
    $('#data-size-lg').on('click', function(e) {
        document.body.setAttribute('data-sidebar-size', 'lg');
    });
    $('#data-size-small').on('click', function(e) {
        document.body.setAttribute('data-sidebar-size', 'small');
    });
    $('#data-size-sm').on('click', function(e) {
        document.body.setAttribute('data-sidebar-size', 'sm');
    });
</script>

</html>
