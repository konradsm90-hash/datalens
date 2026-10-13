<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Dashboardy - DataLens</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f4f6f9; color: #333; }
        .box { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #007bff; color: white; }
        .alert { background: #d4edda; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 15px; }
        .btn { background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; font-weight: bold; display: inline-block; }
        .btn-success { background: #28a745; }
    </style>
</head>
<body>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>DataLens — Panel Dashboardów</h1>
        <div>
            <a href="{{ route('pliki.index') }}" class="btn" style="background: #6c757d;">&larr; Wróć do plików</a>
            <a href="{{ route('dashboardy.create') }}" class="btn btn-success">+ Utwórz nowy dashboard</a>
        </div>
    </div>

    @if(session('sukces'))
        <div class="alert">{{ session('sukces') }}</div>
    @endif

    <div class="box">
        <h3>Twoje pulpity analityczne</h3>
        <table>
            <tr>
                <th>Nazwa dashboardu</th>
                <th>Opis</th>
                <th>Liczba raportów</th>
                <th>Data utworzenia</th>
                <th>Akcje</th>
            </tr>
            @forelse($dashboardy as $d)
            <tr>
                <td><strong>{{ $d->tytul }}</strong></td>
                <td>{{ $d->opis ?: 'Brak opisu' }}</td>
                <td>{{ $d->reports_count }}</td>
                <td>{{ $d->created_at->format('Y-m-d H:i') }}</td>
                <td>
                    <a href="{{ route('dashboardy.show', $d->id) }}" class="btn" style="padding: 6px 12px; font-size: 13px;">Otwórz podgląd</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5">Brak utworzonych dashboardów.</td>
            </tr>
            @endforelse
        </table>
    </div>
</body>
</html>