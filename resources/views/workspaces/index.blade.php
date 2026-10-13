<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Przestrzenie Robocze - DataLens</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f4f6f9; color: #333; }
        .box { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .btn { background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; font-weight: bold; display: inline-block; }
        .alert { background: #d4edda; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 15px; }
        input, select { padding: 8px; margin-right: 10px; border: 1px solid #ccc; border-radius: 4px; }
    </style>
</head>
<body>
    <a href="{{ route('pliki.index') }}" class="btn" style="background: #6c757d; margin-bottom: 20px;">&larr; Wróć do aplikacji</a>

    <div class="box">
        <h1>Twoje Przestrzenie Robocze i Zespoły</h1>

        @if(session('sukces'))
            <div class="alert">{{ session('sukces') }}</div>
        @endif
        @if(session('blad'))
            <div class="alert" style="background: #f8d7da; color: #721c24;">{{ session('blad') }}</div>
        @endif

        @foreach(auth()->user()->workspaces as $ws)
            <div style="border: 1px solid #ddd; padding: 15px; border-radius: 6px; margin-top: 15px;">
                <h3>{{ $ws->nazwa }} (Twoja rola: <strong>{{ $ws->pivot->rola }}</strong>)</h3>
                <p>Członkowie zespołu:</p>
                <ul>
                    @foreach($ws->users as $czlonek)
                        <li>{{ $czlonek->name }} ({{ $czlonek->email }}) - <em>{{ $czlonek->pivot->rola }}</em></li>
                    @endforeach
                </ul>

                <h4 style="margin-top: 15px;">Zapraszasz użytkownika do tej przestrzeni:</h4>
                <form action="{{ route('workspaces.zapros', $ws->id) }}" method="POST">
                    @csrf
                    <input type="email" name="email" required placeholder="Adres e-mail użytkownika">
                    <select name="rola">
                        <option value="editor">Edytor</option>
                        <option value="viewer">Przeglądający</option>
                    </select>
                    <button type="submit" class="btn" style="background: #28a745; border: none; cursor: pointer;">Dodaj do zespołu</button>
                </form>
            </div>
        @endforeach
    </div>
</body>
</html>