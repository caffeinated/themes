<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>DummyName</title>

        <link rel="stylesheet" href="{{ mix(config('themes.paths.absolute').'/css/theme.css') }}">
    </head>
    <body>
        <div id="app">
            @yield('content')
        </div>

        <script src="{{ mix(config('themes.paths.absolute').'/js/theme.js') }}"></script>
    </body>
</html>
