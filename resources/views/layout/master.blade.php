<html>
    <head>
        <title>@yield('title', "Layout")</title>

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>
        <script src="{{ asset('js/jquery/jquery-3.5.1.min.js') }}"></script>
        <script src="{{ asset('js/toastr/toastr.min.js') }}"></script>
        <script src="{{ asset('js/main/main.js') }}"></script>

        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <link href="{{ asset('css/toastr/toastr.min.css') }}" rel="stylesheet">

        <meta name="csrf-token" content="{{ csrf_token() }}">

    </head>

    <body>

        @yield('content')

        @yield('css')

        @yield('javascript')

        @yield('js_init')

        <script>
            $( document ).ready(function() {
                /*$.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });*/
            });
        </script>

    </body>

</html>
