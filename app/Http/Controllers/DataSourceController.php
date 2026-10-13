<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataSource;
use App\Models\Report;
use App\Jobs\PrzetworzPlikDanych;

class DataSourceController extends Controller
{
    public function index()
    {
        $pliki = DataSource::latest()->get();
        return view('pliki.index', compact('pliki'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'plik' => 'required|file|mimes:csv,txt,xlsx|max:10240',
        ]);

        $file = $request->file('plik');
        $nazwaOryginalna = $file->getClientOriginalName();
        $rozmiarKb = round($file->getSize() / 1024);
        $rozszerzenie = strtolower($file->getClientOriginalExtension());

        $sciezka = $file->store('pliki', 'public');

        // Wstępny szybki zapis źródła danych do bazy
        $dataSource = DataSource::create([
            'workspace_id' => auth()->user()->workspaces()->first()->id ?? null,
            'nazwa' => $nazwaOryginalna,
            'sciezka_do_pliku' => $sciezka,
            'typ_pliku' => $rozszerzenie,
            'struktura_kolumn' => [
                'status' => 'przetwarzanie w tle...'
            ],
            'rozmiar_kb' => $rozmiarKb,
        ]);

        // Uruchomienie parsowania i analizy w tle przez system kolejek (Queues)
        PrzetworzPlikDanych::dispatch($dataSource);

        return redirect()->route('pliki.index')->with('sukces', 'Plik został wgrany i jest przetwarzany w tle przez kolejkę zadań!');
    }

    public function show($id)
    {
        $plik = DataSource::findOrFail($id);
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

        return view('pliki.show', compact('plik', 'naglowki', 'wiersze'));
    }

    public function kreator($id)
    {
        $plik = DataSource::findOrFail($id);
        $sciezkaAbsolutna = storage_path('app/public/' . $plik->sciezka_do_pliku);

        $naglowki = [];
        $wiersze = [];

        if (file_exists($sciezkaAbsolutna)) {
            if ($plik->typ_pliku === 'xlsx') {
                if (class_exists(\Shuchkin\SimpleXLSX::class) && $xlsx = \Shuchkin\SimpleXLSX::parse($sciezkaAbsolutna)) {
                    $rows = $xlsx->rows();
                    if (!empty($rows)) {
                        $naglowki = array_map('trim', array_shift($rows));
                        $wiersze = array_slice($rows, 0, 500);
                    }
                }
            } else {
                $separator = $plik->struktura_kolumn['separator'] ?? ';';
                if (($handle = fopen($sciezkaAbsolutna, 'r')) !== FALSE) {
                    $naglowki = fgetcsv($handle, 1000, $separator);
                    if (!$naglowki || count($naglowki) <= 1) {
                        rewind($handle);
                        $naglowki = fgetcsv($handle, 1000, ';');
                    }
                    $naglowki = array_map('trim', $naglowki ?: []);

                    $licznik = 0;
                    while (($data = fgetcsv($handle, 1000, $separator)) !== FALSE && $licznik < 500) {
                        if (count($data) <= 1) {
                            $data = str_getcsv($data[0] ?? '', ';');
                        }
                        $wiersze[] = array_map('trim', $data);
                        $licznik++;
                    }
                    fclose($handle);
                }
            }
        }

        return view('pliki.kreator', compact('plik', 'naglowki', 'wiersze'));
    }

    public function zapiszRaport(Request $request)
    {
        $request->validate([
            'data_source_id' => 'required|exists:data_sources,id',
            'tytul' => 'required|string|max:255',
            'typ_wykresu' => 'required|string',
            'os_x' => 'required|string',
            'os_y' => 'required|string',
        ]);

        Report::create([
            'data_source_id' => $request->data_source_id,
            'tytul' => $request->tytul,
            'typ_wykresu' => $request->typ_wykresu,
            'ustawienia_wykresu' => [
                'os_x' => $request->os_x,
                'os_y' => $request->os_y,
            ],
        ]);

        return redirect()->route('raporty.index')->with('sukces', 'Raport został pomyślnie zapisany!');
    }

    public function pokazRaporty()
    {
        $raporty = Report::with('dataSource')->latest()->get();
        return view('raporty.index', compact('raporty'));
    }

    public function eksport($id)
    {
        $raport = Report::with('dataSource')->findOrFail($id);
        $plik = $raport->dataSource;

        if (!$plik) {
            return redirect()->back()->with('blad', 'Brak przypisanego pliku do tego raportu.');
        }

        $sciezkaAbsolutna = storage_path('app/public/' . $plik->sciezka_do_pliku);

        if (!file_exists($sciezkaAbsolutna)) {
            return redirect()->back()->with('blad', 'Plik źródłowy nie istnieje na serwerze.');
        }

        $nazwaWyjsciowa = 'raport_' . strtolower(str_replace(' ', '_', $raport->tytul)) . '.csv';

        return response()->download($sciezkaAbsolutna, $nazwaWyjsciowa, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function eksportPdf($id)
    {
        $raport = Report::with('dataSource')->findOrFail($id);
        $plik = $raport->dataSource;

        if (!$plik) {
            return redirect()->back()->with('blad', 'Brak przypisanego pliku do tego raportu.');
        }

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

        return view('raporty.pdf', compact('raport', 'plik', 'etykiety', 'wartosci'));
    }
}