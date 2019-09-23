<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Stream</title>

        <!-- Styles -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.5/css/bulma.min.css" integrity="sha256-vK3UTo/8wHbaUn+dTQD0X6dzidqc5l7gczvH+Bnowwk=" crossorigin="anonymous" />
        <link rel="stylesheet" href="{{ asset('css\app.css') }}">

        <!-- Scripts -->
        <script src="{{ asset('js\app.js') }}" defer></script>
    </head>
    <body>
        <div id="app">
            @include('components.hero')
            <section class="section">
                <router-view></router-view>
            </section>

        </div>
    </body>
</html>
