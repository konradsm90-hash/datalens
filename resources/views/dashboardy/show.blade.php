<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>{{ $dashboard->tytul }} - DataLens</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background: #f0f3f6; color: #333; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap: 20px; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .btn { background: #6c757d; color: white; padding: 10px 18px; text-decoration: none; border-radius: 6px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="top-bar">
        <div>
            <h1>{{ $dashboard->tytul }}</h1>
            <p style="color: #666; margin: 5px 0 0 0;">{{ $dashboard->opis }}</p>
        </div>
        <a href="{{ route('dashboardy.index') }}" class="btn">&larr; Wróć do listy dashboardów</a>
    </div>

    <div class="grid">
        @forelse($dashboard->reports as $raport)
            @php
                $dane = $daneWykresow[$raport->id] ?? ['etykiety' => [], 'wartosci' => []];
            @endphp
            <div class="card">
                <h3>{{ $raport->tytul }}</h3>
                <div style="position: relative; height: 300px; width: 100%;">
                    <canvas id="chart_{{ $raport->id }}"></canvas>
                </div>
            </div>
            
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const ctx = document.getElementById('chart_{{ $raport->id }}').getContext('2d');
                    new Chart(ctx, {
                        type: '{{ $raport->typ_wykresu }}',
                        data: {
                            labels: @json($dane['etykiety']),
                            datasets: [{
                                label: '{{ $raport->ustawienia_wykresu['os_y'] ?? 'Wartość' }}',
                                data: @json($dane['wartosci']),
                                backgroundColor: [
                                    'rgba(0, 123, 255, 0.6)',
                                    'rgba(40, 167, 69, 0.6)',
                                    'rgba(255, 193, 7, 0.6)',
                                    'rgba(220, 53, 69, 0.6)',
                                    'rgba(23, 162, 184, 0.6)'
                                ],
                                borderColor: 'rgba(0, 123, 255, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false
                        }
                    });
                });
            </script>
        @empty
            <p>Brak raportów przypisanych do tego dashboardu.</p>
        @endforelse
    </div>
</body>
</html>