<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zapisane Raporty - DataLens</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 40px; background: #f0f3f6; color: #333; }
        .box { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 3px 6px rgba(0,0,0,0.08); margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #007bff; color: white; }
        .alert { background: #d4edda; color: #155724; padding: 12px; border-radius: 6px; margin-bottom: 15px; }
        a.btn { background: #6c757d; color: white; padding: 10px 18px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block; }
        a.btn-eksport { background: #28a745; color: white; padding: 8px 14px; text-decoration: none; border-radius: 6px; font-size: 13px; font-weight: bold; margin-right: 5px; }
        a.btn-eksport:hover { background: #218838; }
        a.btn-pdf { background: #dc3545; color: white; padding: 8px 14px; text-decoration: none; border-radius: 6px; font-size: 13px; font-weight: bold; }
        a.btn-pdf:hover { background: #c82333; }
        .harmonogram { background: #e9ecef; padding: 20px; border-radius: 8px; margin-top: 25px; }
    </style>
</head>
<body>
    <a href="{{ route('pliki.index') }}" class="btn">&larr; Wróć do źródeł danych</a>
    
    <div class="box" style="margin-top: 20px;">
        <h1>Zapisane Raporty DataLens</h1>

        @if(session('sukces'))
            <div class="alert">{{ session('sukces') }}</div>
        @endif

        <table>
            <tr>
                <th>Tytuł raportu</th>
                <th>Źródło danych (Plik)</th>
                <th>Typ wykresu</th>
                <th>Data utworzenia</th>
                <th>Akcje</th>
            </tr>
            @forelse($raporty as $r)
            <tr>
                <td><strong>{{ $r->tytul }}</strong></td>
                <td>{{ $r->dataSource->nazwa ?? 'Brak pliku' }}</td>
                <td>{{ strtoupper($r->typ_wykresu) }}</td>
                <td>{{ $r->created_at->format('Y-m-d H:i') }}</td>
                <td>
                    <a href="{{ route('raporty.eksport', $r->id) }}" class="btn-eksport">Pobierz CSV</a>
                    <a href="{{ route('raporty.pdf', $r->id) }}" class="btn-pdf">Pobierz PDF</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5">Brak zapisanych raportów. Przejdź do kreatora wykresów dowolnego pliku i zapisz swój pierwszy raport!</td>
            </tr>
            @endforelse
        </table>

        <div class="harmonogram">
            <h3>Automatyczne generowanie raportów cyklicznych</h3>
            <p>System jest w pełni przygotowany do obsługi harmonogramu zadań (Laravel Scheduler / Cron). Raporty cykliczne mogą automatycznie odświeżać dane w tle i wysyłać podsumowania.</p>
        </div>
    </div>
</body>
</html>