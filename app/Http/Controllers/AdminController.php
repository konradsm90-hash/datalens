<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Workspace;
use App\Models\DataSource;
use App\Models\Report;
use App\Models\DataHistory;

class AdminController extends Controller
{
    public function index()
    {
        $statystyki = [
            'uzytkownicy' => User::count(),
            'przestrzenie' => Workspace::count(),
            'pliki' => DataSource::count(),
            'raporty' => Report::count(),
        ];

        $uzytkownicy = User::withCount('workspaces')->latest()->get();
        
        // Pobieramy historię zmian wraz z powiązanymi relacjami (użytkownik i plik/źródło)
        $historia = DataHistory::with(['user', 'dataSource'])->latest()->get();

        return view('admin.index', compact('statystyki', 'uzytkownicy', 'historia'));
    }

    public function usunUzytkownika($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('blad', 'Nie możesz usunąć samego siebie!');
        }

        $user->delete();
        return redirect()->back()->with('sukces', 'Użytkownik został usunięty z systemu.');
    }
}