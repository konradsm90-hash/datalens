<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel Administratora - DataLens</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background: #f4f6f9; color: #333; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); text-align: center; }
        .stat-card h2 { margin: 0; color: #007bff; font-size: 32px; }
        .box { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #343a40; color: white; }
        .btn { background: #6c757d; color: white; padding: 8px 14px; text-decoration: none; border-radius: 4px; font-weight: bold; display: inline-block; }
        .btn-danger { background: #dc3545; border: none; cursor: pointer; }
        .alert { background: #d4edda; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="top-bar">
        <h1>DataLens — Panel Administratora</h1>
        <a href="{{ route('pliki.index') }}" class="btn">&larr; Wróć do aplikacji</a>
    </div>

    @if(session('sukces'))
        <div class="alert">{{ session('sukces') }}</div>
    @endif
    @if(session('blad'))
        <div class="alert" style="background: #f8d7da; color: #721c24;">{{ session('blad') }}</div>
    @endif

    <div class="stats-grid">
        <div class="stat-card">
            <h2>{{ $statystyki['uzytkownicy'] }}</h2>
            <p>Użytkownicy</p>
        </div>
        <div class="stat-card">
            <h2>{{ $statystyki['przestrzenie'] }}</h2>
            <p>Przestrzenie robocze</p>
        </div>
        <div class="stat-card">
            <h2>{{ $statystyki['pliki'] }}</h2>
            <p>Załadowane pliki</p>
        </div>
        <div class="stat-card">
            <h2>{{ $statystyki['raporty'] }}</h2>
            <p>Zapisane raporty</p>
        </div>
    </div>

    <div class="box">
        <h3>Zarządzanie Użytkownikami Systemu</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Imię i nazwisko</th>
                <th>E-mail</th>
                <th>Liczba przestrzeni</th>
                <th>Rola w systemie</th>
                <th>Data rejestracji</th>
                <th>Akcje</th>
            </tr>
            @foreach($uzytkownicy as $u)
            <tr>
                <td>{{ $u->id }}</td>
                <td><strong>{{ $u->name }}</strong></td>
                <td>{{ $u->email }}</td>
                <td>{{ $u->workspaces_count }}</td>
                <td>
                    @if($u->is_admin)
                        <span style="color: #28a745; font-weight: bold;">Administrator</span>
                    @else
                        <span>Użytkownik</span>
                    @endif
                </td>
                <td>{{ $u->created_at->format('Y-m-d H:i') }}</td>
                <td>
                    @if($u->id !== auth()->id())
                        <form action="{{ route('admin.uzytkownicy.usun', $u->id) }}" method="POST" onsubmit="return confirm('Czy na pewno chcesz usunąć tego użytkownika?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" style="padding: 6px 10px; font-size: 12px;">Usuń</button>
                        </form>
                    @else
                        <span style="color: #888; font-size: 12px;">(Twoje konto)</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </table>
    </div>
        <div class="box" style="margin-top: 30px;">
        <h3>📜 Ostatnia historia modyfikacji danych</h3>
        <table>
            <thead>
                <tr>
                    <th>Użytkownik</th>
                    <th>Plik / Źródło</th>
                    <th>Akcja</th>
                    <th>Szczegóły</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                @foreach($historia as $wpis)
                    <tr>
                        <td>{{ $wpis->user->name ?? 'Nieznany' }}</td>
                        <td>{{ $wpis->dataSource->nazwa ?? 'Usunięte źródło' }}</td>
                        <td><span style="background: #e2e8f0; padding: 3px 8px; border-radius: 4px; font-size: 12px;">{{ $wpis->akcja }}</span></td>
                        <td><small>{{ $wpis->szczegoly }}</small></td>
                        <td>{{ $wpis->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>