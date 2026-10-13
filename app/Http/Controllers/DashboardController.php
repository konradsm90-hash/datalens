<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dashboard;
use App\Models\Report;

class DashboardController extends Controller
{
    public function index()
    {
        $dashboardy = Dashboard::withCount('reports')->latest()->get();
        return view('dashboardy.index', compact('dashboardy'));
    }

    public function create()
    {
        $raporty = Report::all();
        return view('dashboardy.create', compact('raporty'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tytul' => 'required|string|max:255',
            'raporty' => 'array|exists:reports,id',
        ]);

        $dashboard = Dashboard::create([
            'tytul' => $request->tytul,
            'opis' => $request->opis,
        ]);

        if ($request->has('raporty')) {
            $dashboard->reports()->sync($request->raporty);
        }

        return redirect()->route('dashboardy.index')->with('sukces', 'Dashboard został utworzony!');
    }

    public function show($id)
    {
        $dashboard = Dashboard::with(['reports.dataSource'])->findOrFail($id);
        
        // Przygotowujemy dane do wykresów tak samo jak w kreatorze/raportach
        $daneWykresow = [];

        foreach ($dashboard->reports as $raport) {
            $plik = $raport->dataSource;
            if (!$plik) continue;

            $sciezkaAbsolutna = storage_path('app/public/' . $plik->sciezka_do_pliku);
            $naglowki = [];
            $wiersze = [];

            if (file_exists($sciezkaAbsolutna)) {
                if ($plik->typ_pliku === 'xlsx') {
                    if (class_exists(\Shuchkin\SimpleXLSX::class) && $xlsx = \Shuchkin\SimpleXLSX::parse($sciezkaAbsolutna)) {
                        $rows = $xlsx->rows();
                        if (!empty($rows)) {
                            $naglowki = array_map('trim', array_shift($rows));
                            $wiersze = $rows;
                        }
                    }
                } else {
                    $separator = $plik->struktura_kolumn['separator'] ?? ',';
                    if (($handle = fopen($sciezkaAbsolutna, 'r')) !== FALSE) {
                        $naglowki = fgetcsv($handle, 1000, $separator);
                        while (($data = fgetcsv($handle, 1000, $separator)) !== FALSE) {
                            $wiersze[] = $data;
                        }
                        fclose($handle);
                    }
                }
            }

            $osXIndex = array_search($raport->ustawienia_wykresu['os_x'] ?? '', $naglowki);
            $osYIndex = array_search($raport->ustawienia_wykresu['os_y'] ?? '', $naglowki);

            $etykiety = [];
            $wartosci = [];

            if ($osXIndex !== false && $osYIndex !== false) {
                foreach ($wiersze as $wiersz) {
                    $etykiety[] = $wiersz[$osXIndex] ?? '';
                    $wartosci[] = is_numeric(str_replace(',', '.', $wiersz[$osYIndex] ?? 0)) ? floatval(str_replace(',', '.', $wiersz[$osYIndex])) : 0;
                }
            }

            $daneWykresow[$raport->id] = [
                'etykiety' => $etykiety,
                'wartosci' => $wartosci,
            ];
        }

        return view('dashboardy.show', compact('dashboard', 'daneWykresow'));
    }
}