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
        th { background: #007bff; color: white; cursor: pointer; user-select: none; }
        th:hover { background: #0056b3; }
        .btn { background: #6c757d; color: white; padding: 10px 18px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block; border: none; cursor: pointer; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #218838; }
        .btn-primary { background: #007bff; }
        .btn-primary:hover { background: #0056b3; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px; }
        input.szukaj { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-size: 14px; }
        .info-box { display: flex; gap: 20px; background: #e9ecef; padding: 15px; border-radius: 6px; margin-bottom: 15px; font-size: 14px; flex-wrap: wrap; }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px; }
        .form-group label { display: block; font-size: 12px; font-weight: bold; margin-bottom: 5px; color: #555; }
        .form-group input { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .alert-success { background: #d4edda; color: #155724; padding: 12px; border-radius: 6px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="top-bar">
        <div>
            <a href="{{ route('pliki.index') }}" class="btn">&larr; Wróć do listy</a>
            <a href="{{ route('pliki.eksport.csv', $plik->id) }}" class="btn btn-success">📥 Eksportuj do CSV</a>
        </div>
        <a href="{{ route('pliki.kreator', $plik->id) }}" class="btn btn-primary">Przejdź do kreatora wykresów &rarr;</a>
    </div>

    @if(session('sukces'))
        <div class="alert-success">{{ session('sukces') }}</div>
    @endif

    <!-- Sekcja Dodawania Nowego Rekordu -->
    <div class="box">
        <h3>➕ Dodaj nowy rekord do pliku</h3>
        <form action="/pliki/{{ $plik->id }}/rekord" method="POST">
            @csrf
            <div class="form-grid">
                @foreach($naglowki as $n)
                    <div class="form-group">
                        <label>{{ $n }}</label>
                        <input type="text" name="{{ $n }}" required placeholder="Wpisz {{ $n }}...">
                    </div>
                @endforeach
            </div>
            <button type="submit" class="btn btn-success">Zapisz nowy rekord</button>
        </form>
    </div>

    <!-- Główny Podgląd Tabeli -->
    <div class="box">
        <h1>Podgląd danych: {{ $plik->nazwa }}</h1>
        
        <div class="info-box">
            <div><strong>Całkowita liczba wierszy:</strong> {{ $plik->struktura_kolumn['calkowite_wiersze'] ?? count($wiersze) }}</div>
            <div><strong>Wykryte duplikaty:</strong> <span style="color: {{ ($plik->struktura_kolumn['liczba_duplikatow'] ?? 0) > 0 ? 'red' : 'green' }}; font-weight: bold;">{{ $plik->struktura_kolumn['liczba_duplikatow'] ?? 0 }}</span></div>
        </div>
        <!-- Pasa filtrowania per kolumna -->
        <div style="overflow-x: auto; max-height: 600px;">
            <table id="tabelaDanych">
                <thead>
                    <tr>
                        @foreach($naglowki as $index => $n)
                            <th onclick="sortTable({{ $index }})" style="cursor: pointer;">
                                {{ $n }} &#x2195;
                            </th>
                        @endforeach
                    </tr>
                    <!-- Wiersz z osobnymi filtrami dla każdej kolumny -->
                    <tr style="background: #f8f9fa;">
                        @foreach($naglowki as $index => $n)
                            <th style="padding: 6px;">
                                <input type="text" 
                                       placeholder="Filtruj {{ $n }}..." 
                                       onkeyup="filtrujKolumny()" 
                                       data-col="{{ $index }}" 
                                       style="width: 100%; padding: 6px; border: 1px solid #ccc; border-radius: 4px; font-size: 12px; box-sizing: border-box;">
                            </th>
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

    <script>
        // Funkcja niezależnego filtrowania dla każdej kolumny z osobna
        function filtrujKolumny() {
            let table = document.getElementById("tabelaDanych");
            let tr = table.getElementsByTagName("tr");
            
            // Pobieramy wszystkie pola input z wiersza filtrów (zaczynają się od wiersza indeksu 1 w thead, ale najlepiej po atrybucie data-col)
            let inputs = table.querySelectorAll("thead tr:nth-child(2) input");
            
            // Iterujemy po wierszach tabeli danych (zaczynają się od tbody, czyli wiersze od indeksu 2 w górę ogółem)
            for (let i = 2; i < tr.length; i++) {
                let td = tr[i].getElementsByTagName("td");
                let pokazWiersz = true;

                // Sprawdzamy warunek dla każdego pola filtra
                for (let j = 0; j < inputs.length; j++) {
                    let filterValue = inputs[j].value.toLowerCase();
                    if (filterValue !== "") {
                        let cellText = td[j] ? td[j].innerText.toLowerCase() : "";
                        if (cellText.indexOf(filterValue) === -1) {
                            pokazWiersz = false;
                            break;
                        }
                    }
                }

                tr[i].style.display = pokazWiersz ? "" : "none";
            }
        }

        // Funkcja sortowania kolumn po kliknięciu w nagłówek (z uwzględnieniem offsetu wiersza filtrów)
        function sortTable(n) {
            let table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
            table = document.getElementById("tabelaDanych");
            switching = true;
            dir = "asc";
            
            while (switching) {
                switching = false;
                rows = table.rows;
                // Uwaga: zaczynamy od wiersza 2 w tbody, czyli pomijamy nagłówki i wiersz z inputami filtrów (które są w thead)
                let tbodyRows = table.tBodies[0].rows;
                
                for (i = 0; i < (tbodyRows.length - 1); i++) {
                    shouldSwitch = false;
                    x = tbodyRows[i].getElementsByTagName("TD")[n];
                    y = tbodyRows[i+1].getElementsByTagName("TD")[n];
                    
                    if (!x || !y) continue;

                    let xVal = x.innerHTML.toLowerCase();
                    let yVal = y.innerHTML.toLowerCase();

                    if (dir === "asc") {
                        if (xVal > yVal) { shouldSwitch = true; break; }
                    } else if (dir === "desc") {
                        if (xVal < yVal) { shouldSwitch = true; break; }
                    }
                }
                if (shouldSwitch) {
                    tbodyRows[i].parentNode.insertBefore(tbodyRows[i + 1], tbodyRows[i]);
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