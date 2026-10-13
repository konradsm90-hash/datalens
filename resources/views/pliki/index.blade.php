<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>DataLens - Analiza Źródeł Danych</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f4f6f9; color: #333; }
        .box { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #007bff; color: white; }
        .alert { background: #d4edda; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 15px; }
        button { background: #007bff; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer; }
        .badge { background: #e2e8f0; padding: 3px 8px; border-radius: 4px; font-size: 12px; margin-right: 4px; display: inline-block; margin-bottom: 4px;}
        a.link-pliku { color: #007bff; text-decoration: none; font-weight: bold; }
        a.link-pliku:hover { text-decoration: underline; }
        .top-menu { margin-bottom: 20px; }
        .btn-raporty { background: #17a2b8; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; font-weight: bold; display: inline-block; }
    </style>
</head>
<body>
    <div class="top-menu" style="display: flex; justify-content: space-between; align-items: center;">
        <h1>DataLens — Moduł Analizy Danych</h1>
        <a href="{{ route('raporty.index') }}" class="btn-raporty">Zobacz zapisane raporty &rarr;</a>
    </div>

    @if(session('sukces'))
        <div class="alert">{{ session('sukces') }}</div>
    @endif

    <div class="box">
        <h3>Prześlij plik CSV lub Excel (XLSX) do analizy</h3>
        <form action="{{ route('pliki.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" name="plik" accept=".csv,.txt,.xlsx" required>
            <button type="submit">Importuj i analizuj strukturę</button>
        </form>
    </div>

    <div class="box">
        <h3>Twoje zasoby danych</h3>
        <table>
            <tr>
                <th>Nazwa pliku</th>
                <th>Wierszy</th>
                <th>Rozmiar</th>
                <th>Analiza kolumn i typów</th>
                <th>Data wgrania</th>
            </tr>
            @forelse($pliki as $p)
            <tr>
                <td>
                    <a href="{{ route('pliki.show', $p->id) }}" class="link-pliku" title="Kliknij, aby podejrzeć dane">
                        {{ $p->nazwa }}
                    </a>
                </td>
                <td>{{ $p->struktura_kolumn['calkowite_wiersze'] ?? 0 }}</td>
                <td>{{ $p->rozmiar_kb }} KB</td>
                <td>
                    @if(isset($p->struktura_kolumn['analiza']))
                        @foreach($p->struktura_kolumn['analiza'] as $nazwaKolumny => $info)
                            <span class="badge">
                                <strong>{{ $nazwaKolumny }}</strong> 
                                ({{ $info['typ'] }}) 
                                @if($info['brakujace'] > 0)
                                    <span style="color: red;">[Braki: {{ $info['brakujace'] }}]</span>
                                @endif
                            </span>
                        @endforeach
                    @else
                        Brak szczegółów
                    @endif
                </td>
                <td>{{ $p->created_at->format('Y-m-d H:i') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5">Brak wgranych plików.</td>
            </tr>
            @endforelse
        </table>
    </div>
</body>
</html>