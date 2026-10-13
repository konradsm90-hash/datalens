<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Podgląd Pliku - DataLens</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 30px; background: #f0f3f6; color: #333; }
        .box { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 3px 6px rgba(0,0,0,0.08); margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 14px; }
        th, td { padding: 10px 12px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #007bff; color: white; cursor: pointer; }
        th:hover { background: #0056b3; }
        a.btn { background: #6c757d; color: white; padding: 10px 18px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block; }
        a.btn-kreator { background: #28a745; color: white; padding: 10px 18px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block; float: right; }
        a.btn-kreator:hover { background: #218838; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        input.szukaj { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-size: 14px; }
        .info-box { display: flex; gap: 20px; background: #e9ecef; padding: 15px; border-radius: 6px; margin-bottom: 15px; font-size: 14px; }
    </style>
</head>
<body>
    <div class="top-bar">
        <a href="{{ route('pliki.index') }}" class="btn">&larr; Wróć do listy plików</a>
        <a href="{{ route('pliki.kreator', $plik->id) }}" class="btn-kreator">Przejdź do kreatora wykresów &rarr;</a>
    </div>

    <div class="box">
        <h1>Podgląd danych: {{ $plik->nazwa }}</h1>
        
        <div class="info-box">
            <div><strong>Całkowita liczba wierszy:</strong> {{ $plik->struktura_kolumn['calkowite_wiersze'] ?? count($wiersze) }}</div>
            <div><strong>Wykryte duplikaty:</strong> <span style="color: {{ ($plik->struktura_kolumn['liczba_duplikatow'] ?? 0) > 0 ? 'red' : 'green' }}; font-weight: bold;">{{ $plik->struktura_kolumn['liczba_duplikatow'] ?? 0 }}</span></div>
        </div>

        <!-- Pole wyszukiwania w tabeli -->
        <input type="text" id="szukajTabela" class="szukaj" placeholder="Wpisz, aby filtrować rekordy w tabeli..." onkeyup="filtrujTabelę()">

        <div style="overflow-x: auto; max-height: 500px;">
            <table id="tabelaDanych">
                <thead>
                    <tr>
                        @foreach($naglowki as $index => $n)
                            <th onclick="sortTable({{ $index }})">{{ $n }} &#x2195;</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($wiersze as $wiersz)
                        <tr>
                            @foreach($wiersz as $komorka)
                                <td>{{ $komorka }}</td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($naglowki) }}">Brak danych do wyświetlenia w tym pliku.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Funkcja filtrowania tabeli w locie
        function filtrujTabelę() {
            let input = document.getElementById("szukajTabela");
            let filter = input.value.toLowerCase();
            let table = document.getElementById("tabelaDanych");
            let tr = table.getElementsByTagName("tr");

            for (let i = 1; i < tr.length; i++) {
                let td = tr[i].getElementsByTagName("td");
                let pokaz = false;
                for (let j = 0; j < td.length; j++) {
                    if (td[j]) {
                        if (td[j].innerHTML.toLowerCase().indexOf(filter) > -1) {
                            pokaz = true;
                            break;
                        }
                    }
                }
                tr[i].style.display = pokaz ? "" : "none";
            }
        }

        // Funkcja sortowania kolumn po kliknięciu w nagłówek
        function sortTable(n) {
            let table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
            table = document.getElementById("tabelaDanych");
            switching = true;
            dir = "asc";
            
            while (switching) {
                switching = false;
                rows = table.rows;
                for (i = 1; i < (rows.length - 1); i++) {
                    shouldSwitch = false;
                    x = rows[i].getElementsByTagName("TD")[n];
                    y = rows[i+1].getElementsByTagName("TD")[n];
                    
                    let xVal = x.innerHTML.toLowerCase();
                    let yVal = y.innerHTML.toLowerCase();

                    if (dir === "asc") {
                        if (xVal > yVal) { shouldSwitch = true; break; }
                    } else if (dir === "desc") {
                        if (xVal < yVal) { shouldSwitch = true; break; }
                    }
                }
                if (shouldSwitch) {
                    rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                    switching = true;
                    switchcount++;
                } else {
                    if (switchcount === 0 && dir === "asc") {
                        dir = "desc";
                        switching = true;
                    }
                }
            }
        }
    </script>
</body>
</html>