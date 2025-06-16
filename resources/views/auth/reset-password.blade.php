<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réinitialisation du mot de passe</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: sans-serif;
            background-color: #f9f9f9;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .card {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        h1 {
            color: #fd5312;
            margin-bottom: 1rem;
            font-size: 22px;
        }
        input, button {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
        }
        button {
            background-color: #fd5312;
            color: white;
            border: none;
            font-weight: bold;
            cursor: pointer;
        }
        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Réinitialiser le mot de passe</h1>

        @if (session('status'))
            <div class="success">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="error">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ url('/reset-password') }}">
            @csrf

            <input type="hidden" name="token" value="{{ request()->get('token') }}">
            <input type="hidden" name="use_email" value="{{ request()->get('email') }}">

            <input type="password" name="password" placeholder="Nouveau mot de passe" required>
            <input type="password" name="password_confirmation" placeholder="Confirmer le mot de passe" required>

            <button type="submit">Réinitialiser</button>
        </form>
    </div>
</body>
</html>
