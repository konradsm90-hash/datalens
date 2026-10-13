<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\DataSource;

class PrzetworzPlikDanych implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $dataSource;

    public function __construct(DataSource $dataSource)
    {
        $this->dataSource = $dataSource;
    }

    public function handle(): void
    {
        $sciezkaAbsolutna = storage_path('app/public/' . $this->dataSource->sciezka_do_pliku);
        
        if (!file_exists($sciezkaAbsolutna)) {
            return;
        }

        $naglowki = [];
        $liczbaWierszy = 0;

        if ($this->dataSource->typ_pliku === 'xlsx') {
            if (class_exists(\Shuchkin\SimpleXLSX::class) && $xlsx = \Shuchkin\SimpleXLSX::parse($sciezkaAbsolutna)) {
                $rows = $xlsx->rows();
                if (!empty($rows)) {
                    $naglowki = array_map('trim', array_shift($rows));
                    $liczbaWierszy = count($rows);
                }
            }
        } else {
            $separator = $this->dataSource->struktura_kolumn['separator'] ?? ',';
            if (($handle = fopen($sciezkaAbsolutna, 'r')) !== FALSE) {
                $naglowki = fgetcsv($handle, 1000, $separator);
                while (($data = fgetcsv($handle, 1000, $separator)) !== FALSE) {
                    $liczbaWierszy++;
                }
                fclose($handle);
            }
        }

        // Zaktualizowanie struktury kolumn oraz metadanych w bazie po zakończeniu pracy w tle
        $this->dataSource->update([
            'struktura_kolumn' => [
                'naglowki' => $naglowki,
                'separator' => $this->dataSource->struktura_kolumn['separator'] ?? ',',
                'liczba_wierszy' => $liczbaWierszy,
                'status' => 'gotowy'
            ]
        ]);
    }
}