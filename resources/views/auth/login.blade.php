<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Abalo – Anmeldung</title>
    <style>
        body {font-family: Arial,sans-serif;background:#f4f6f8;margin:0;padding:48px 16px}
        main {max-width:420px;margin:auto;background:white;padding:28px;border-radius:12px;box-shadow:0 6px 18px #0001}
        label {display:block;margin:18px 0 6px} input {box-sizing:border-box;width:100%;padding:11px}
        button {padding:11px 18px;margin-top:20px;cursor:pointer}
        .error {color:#9b1c1c} .hint {color:#555;font-size:14px}
    </style>
</head>
<body>
<main>
    <h1>Abalo – Anmeldung</h1>
    <p class="hint">Demo-Anmeldung mit einem Benutzer aus der lokalen Testdatenbank.</p>
    @if ($errors->any())
        <p class="error" role="alert">{{ $errors->first() }}</p>
    @endif
    <form method="POST" action="{{ route('login.submit') }}">
        @csrf
        <label for="username">Benutzername</label>
        <input id="username" name="username" value="{{ old('username') }}" autocomplete="username" required>
        <label for="password">Passwort</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required>
        <button type="submit">Anmelden</button>
    </form>
    <p class="hint">Die Beispiel-Accounts stehen in den lokalen Seed-Daten. Nicht mit echten Passwörtern deployen.</p>
</main>
</body>
</html>
