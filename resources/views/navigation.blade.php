<!-- <!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/navigation.css') }}">
</head>

<body>
    <nav id="navigation"></nav>
    @yield('content')
    <footer>
        <SiteFooter />
</footer>
    @vite(['resources/js/app.js'])
</body>

</html> -->

<!-- resources/views/navigation.blade.php -->
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Abalo')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/navigation.css') }}">
    @vite(['resources/js/app.js'])
</head>

<body>
@yield('nav')
@yield('content')
</body>
</html>
