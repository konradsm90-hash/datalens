<?php

use App\Http\Controllers\DataSourceController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\AdminController;

// Strona główna przekierowuje na listę plików (lub logowanie)
Route::get('/', function () {
    return redirect()->route('pliki.index');
});
Route::get('/dashboard', function () {
    return redirect()->route('pliki.index');
})->middleware(['auth', 'verified'])->name('dashboard');

// Wszystkie trasy aplikacji wymagające zalogowania
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Źródła danych i pliki
    Route::get('/pliki', [DataSourceController::class, 'index'])->name('pliki.index');
    Route::post('/pliki', [DataSourceController::class, 'store'])->name('pliki.store');
    Route::get('/pliki/{id}', [DataSourceController::class, 'show'])->name('pliki.show');
    Route::get('/pliki/{id}/kreator', [DataSourceController::class, 'kreator'])->name('pliki.kreator');
    
    // Przeniesione tutaj z panelu admina (prawidłowe miejsce):
    Route::post('/pliki/{id}/rekord', [DataSourceController::class, 'dodajRekord'])->name('pliki.rekord.dodaj');
    Route::get('/pliki/{id}/eksport-csv', [DataSourceController::class, 'eksportCsv'])->name('pliki.eksport.csv');

    // Raporty
    Route::post('/raporty/zapisz', [DataSourceController::class, 'zapiszRaport'])->name('raporty.zapisz');
    Route::get('/raporty', [DataSourceController::class, 'pokazRaporty'])->name('raporty.index');
    Route::get('/raporty/{id}/eksport', [DataSourceController::class, 'eksport'])->name('raporty.eksport');
    Route::get('/raporty/{id}/pdf', [DataSourceController::class, 'eksportPdf'])->name('raporty.pdf');

    // Dashboardy (pulpity analityczne)
    Route::resource('dashboardy', DashboardController::class);
    Route::get('/workspaces', [WorkspaceController::class, 'index'])->name('workspaces.index');
    Route::post('/workspaces/{id}/zapros', [WorkspaceController::class, 'zapros'])->name('workspaces.zapros');

    // Panel administratora (tylko dla adminów)
    Route::middleware(['auth'])->prefix('admin')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('admin.index');
        Route::delete('/uzytkownicy/{id}', [AdminController::class, 'usunUzytkownika'])->name('admin.uzytkownicy.usun');
    });

});

// Dołączenie tras uwierzytelniania Laravel Breeze (logowanie, rejestracja itp.)
require __DIR__.'/auth.php';