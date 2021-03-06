<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Restaurante</title>
        @yield('extrastyles') 
        <!-- Latest compiled and minified CSS & JS -->
        <link rel="stylesheet" href="{{ URL::asset('css/app.css') }}">
        
        <link href='https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900|Material+Icons' rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/vuetify/dist/vuetify.min.css" rel="stylesheet">

        </head> 
    <body>
        <div  id="app">
            <layout-user></layout-user>   
        </div>

        <script src="{{ asset('js/app.js') }}"></script>      
    </body>
</html>

