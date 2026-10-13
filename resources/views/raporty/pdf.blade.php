<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Raport: {{ $raport->tytul }}</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; color: #333; background: #fff; }
        .header { border-bottom: 2px solid #007bff; padding-bottom: 10px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
        .chart-container { width: 100%; max-width: 700px; height: 350px; margin: 20px auto; }
        .no-print { margin-top: 30px; text-align: center; }
        .btn { background: #28a745; color: white; padding: 12px 25px; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; font-weight: bold; text-decoration: none; display: inline-block; }
        .btn-secondary { background: #6c757d; margin-left: 10px; }
        @media print {
            .no-print { display: none; }
            body { margin: 10px; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>DataLens — Raport Analityczny</h2>
        <span>Data wygenerowania: {{ date('Y-m-d H:i') }}</span>
    </div>

    <h1>{{ $raport->tytul }}</h1>
    <p><strong>Źródło danych:</strong> {{ $plik->nazwa }}</p>
    <p><strong>Typ wykresu:</strong> {{ ucfirst($raport->typ_wykresu) }}</p>

    <div class="chart-container">
        <canvas id="pdfChart"></canvas>
    </div>

    <div class="no-print">
        <button onclick="window.print()" class="btn">🖨️ Zapisz jako PDF / Drukuj</button>
        <a href="{{ route('raporty.index') }}" class="btn btn-secondary">Wróć do listy raportów</a>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('pdfChart').getContext('2d');
            new Chart(ctx, {
                type: '{{ $raport->typ_wykresu }}',
                data: {
                    labels: @json($etykiety),
                    datasets: [{
                        label: '{{ $raport->ustawienia_wykresu['os_y'] ?? 'Wartość' }}',
                        data: @json($wartosci),
                        backgroundColor: 'rgba(0, 123, 255, 0.6)',
                        borderColor: 'rgba(0, 123, 255, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false
                }
            });
        });
    </script>
</body>
</html>