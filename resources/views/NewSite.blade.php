<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>New Site</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/navigation.css') }}">
    @vite(['resources/js/app.js'])
</head>
<body>
    <div id="newsite-app"></div>
</body>
</html>

<!-- @extends('navigation')
@section('title', 'Abalo')
@section('content')
<div id="newsite-app"></div>
@endsection -->