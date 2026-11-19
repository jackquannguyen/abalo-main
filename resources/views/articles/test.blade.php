<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>Vue Test</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <h1>Test Blade funktioniert</h1>

    <div id="vue-app">
        Vor Vue: @{{ message }}
    </div>
</body>

</html>
