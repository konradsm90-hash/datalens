<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Kreator Wykresów - DataLens</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 30px; background: #f0f3f6; color: #333; display: flex; flex-direction: column; gap: 20px; }
        .box { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 3px 6px rgba(0,0,0,0.08); }
        .kreator-uklad { display: grid; grid-template-columns: 320px 1fr; gap: 25px; align-items: start; }
        .flex-kolumna { display: flex; flex-direction: column; gap: 15px; }
        h1, h3 { margin-top: 0; color: #1a1a1a; }
        a.btn-wstecz { background: #6c757d; color: white; padding: 10px 18px; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 14px; display: inline-block; }
        select, button.btn-generuj, input[type="text"] { width: 100%; padding: 12px; border-radius: 6px; border: 1px solid #ccd0d5; font-size: 15px; box-sizing: border-box; }
        select { background-color: white; }
        label { font-weight: bold; font-size: 14px; color: #444; }
        button.btn-generuj { background: #28a745; color: white; border: none; font-weight: bold; cursor: pointer; text-transform: uppercase; letter-spacing: 1px; }
        button.btn-zapisz { background: #007bff; color: white; border: none; font-weight: bold; cursor: pointer; padding: 12px; border-radius: 6px; }
        button.btn-zapisz:hover { background: #0056b3; }
        
        .podglad-container { 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            max-width: 100%;
            overflow: hidden;
        }
        #mojWykres {
            max-height: 450px !important;
            width: 100% !important;
            height: auto !important;
        }
    </style>
</head>
<body>
    <div>
        <a href="{{ route('pliki.show', $plik->id) }}" class="btn-wstecz">&larr; Wróć do podglądu danych</a>
        <h1>Kreator Wykresów dla: {{ $plik->nazwa }}</h1>
    </div>

    <div class="kreator-uklad">
        <!-- Panel konfiguracyjny -->
        <div class="box flex-kolumna">
            <h3>Konfiguracja wizualizacji</h3>
            
            <div class="flex-kolumna">
                <label>Typ wykresu</label>
                <select id="typWykresu">
                    <option value="bar">Słupkowy</option>
                    <option value="line">Liniowy</option>
                    <option value="pie">Kołowy</option>
                </select>
            </div>

            <div class="flex-kolumna">
                <label>Oś X (Etykiety - tekst)</label>
                <select id="osX">
                    @foreach($naglowki as $n)
                        <option value="{{ $loop->index }}" {{ strtolower($n) == 'marka' ? 'selected' : '' }}>{{ $n }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex-kolumna">
                <label>Oś Y (Wartości - liczby)</label>
                <select id="osY">
                    @foreach($naglowki as $n)
                        <option value="{{ $loop->index }}" {{ in_array(strtolower($n), ['cena', 'przebieg', 'rok']) ? 'selected' : '' }}>{{ $n }}</option>
                    @endforeach
                </select>
            </div>

            <button class="btn-generuj" onclick="generujWykres()">Generuj interaktywny wykres</button>

            <hr style="border:0; border-top:1px solid #ddd; margin: 5px 0;">

            <form action="{{ route('raporty.zapisz') }}" method="POST" class="flex-kolumna" onsubmit="przygotujFormularzRaportu(event)">
                @csrf
                <input type="hidden" name="data_source_id" value="{{ $plik->id }}">
                <input type="hidden" name="typ_wykresu" id="formTypWykresu">
                <input type="hidden" name="os_x" id="formOsX">
                <input type="hidden" name="os_y" id="formOsY">

                <label>Nazwa raportu</label>
                <input type="text" name="tytul" placeholder="np. Analiza cen" required>

                <button type="submit" class="btn-zapisz">Zapisz ten raport</button>
            </form>
        </div>

        <!-- Panel podglądu -->
        <div class="box podglad-container">
            <canvas id="mojWykres"></canvas>
        </div>
    </div>

    <script>
        let myChart = null; 

        const naglowki = @json($naglowki);
        const wierszeRaw = @json($wiersze);

        function generujWykres() {
            const typ = document.getElementById('typWykresu').value;
            const indexX = document.getElementById('osX').value;
            const indexY = document.getElementById('osY').value;

            const etykietyX = wierszeRaw.map(row => row[indexX]);
            
            // Pobieramy wartości osi Y. Jeśli to liczby, parsujemy. Jeśli tekst (np. paliwo/model), zamieniamy na 1 (zliczamy wystąpienia lub traktujemy jako błąd i dajemy 0)
            const suroweY = wierszeRaw.map(row => row[indexY]);
            const wartosciY = suroweY.map(val => {
                let parsed = parseFloat(String(val).replace(',', '.').replace(' ', ''));
                return isNaN(parsed) ? 1 : parsed; // Jeśli użytkownik wybrał tekst na oś Y, zliczamy go jako 1 wystąpienie
            });

            if (myChart) {
                myChart.destroy();
            }

            const czyKolowy = (typ === 'pie');

            const ctx = document.getElementById('mojWykres').getContext('2d');
            myChart = new Chart(ctx, {
                type: typ,
                data: {
                    labels: etykietyX,
                    datasets: [{
                        label: naglowki[indexY], 
                        data: wartosciY,
                        backgroundColor: (czyKolowy ? [
                            '#ff6384cc', '#36a2ebcc', '#cc65fecc', '#ffce56cc', '#4bc0c0cc', '#f67019cc'
                        ] : '#007bffcc'), 
                        borderColor: (czyKolowy ? 'white' : '#007bff'),
                        borderWidth: 1,
                        radius: czyKolowy ? '55%' : '100%' 
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { 
                            position: 'bottom',
                            display: czyKolowy 
                        }
                    },
                    scales: (!czyKolowy ? { 
                        y: { beginAtZero: true }
                    } : {})
                }
            });
        }

        function przygotujFormularzRaportu(e) {
            document.getElementById('formTypWykresu').value = document.getElementById('typWykresu').value;
            document.getElementById('formOsX').value = document.getElementById('osX').value;
            document.getElementById('formOsY').value = document.getElementById('osY').value;
        }

        // Automatyczne generowanie wykresu po załadowaniu strony
        generujWykres();
    </script>
</body>
</html>