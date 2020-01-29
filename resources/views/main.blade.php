<html lang="en-US">
    <head>
        <title> @yield('title') </title>
        <meta charset="utf-8">
        <meta name="author" content="Filip Janko">
        <meta name="description" content="Scientific data analysis for Ceitec">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <link rel="shortcut icon" href="{{{ asset('favico.ico') }}}">
        <link href="{{ asset('css/app.css') }}" rel="stylesheet" type="text/css">
        <script type="text/javascript" src="{{ asset('js/plotly.js') }}"></script>
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    </head>
    <body>
        <div class="app">
            @include('components.nav')
            <main class="py-4">
                @yield('content')
            </main>         
        </div>
        <script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
    </body>
</html>