<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- csrf Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Laravel Shop') - Laravel 电商教程</title>
    {{-- 载入 css --}}
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
</head>
<body>
    <div id="app" class="{{ routeToClass() }}-page">
        @include('layouts._header')
        <div class="container">
            @yield('content')
        </div>
        @include('layouts._footer')
    </div>
    {{-- 载入 js --}}
    <script src="{{ mix('js/app.js') }}"></script>
</body>
</html>