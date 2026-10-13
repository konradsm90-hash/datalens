<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Nowy Dashboard - DataLens</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f4f6f9; color: #333; }
        .box { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 600px; margin: auto; }
        input[type="text"], textarea { width: 100%; padding: 10px; margin-top: 5px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .checkbox-group { max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .checkbox-item { margin-bottom: 8px; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Utwórz nowy Dashboard</h2>
        <form action="{{ route('dashboardy.store') }}" method="POST">
            @csrf
            <label>Tytuł Dashboardu:</label>
            <input type="text" name="tytul" required placeholder="np. Podsumowanie Sprzedaży Q1">

            <label>Opis (opcjonalnie):</label>
            <textarea name="opis" rows="3" placeholder="Krótki opis pulpitu..."></textarea>

            <label>Wybierz raporty do umieszczenia na dashboardzie:</label>
            <div class="checkbox-group">
                @forelse($raporty as $raport)
                    <div class="checkbox-item">
                        <label>
                            <input type="checkbox" name="raporty[]" value="{{ $raport->id }}">
                            {{ $raport->tytul }} <span style="color: #666; font-size: 12px;">(Typ: {{ $raport->typ_wykresu }})</span>
                        </label>
                    </div>
                @empty
                    <p>Brak zapisanych raportów. Najpierw utwórz i zapisz raporty w kreatorze.</p>
                @endforelse
            </div>

            <button type="submit">Zapisz Dashboard</button>
            <a href="{{ route('dashboardy.index') }}" style="margin-left: 15px; color: #666; text-decoration: none;">Anuluj</a>
        </form>
    </div>
</body>
</html>