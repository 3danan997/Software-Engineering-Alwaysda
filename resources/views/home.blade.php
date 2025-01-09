<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
		      <link rel="shortcut icon" href="{{ asset('images/official/favicon.svg') }}">
        <title>{{config('app.name')}}</title>

        <!-- Fonts -->
        <link href="https://cdn.jsdelivr.net/npm/@mdi/font@latest/css/materialdesignicons.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Material+Icons">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap">
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    </head>
    <body>
      <div id="app">
        <home></home>
      </div>
      <script src="{{ asset('js/app.js')}}"></script>
    </body>
</html>
