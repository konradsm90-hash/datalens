<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>@yield('tytul', 'DataLens - BI Platform')</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f4f6f9; color: #333; }
        .container { max-width: 1200px; margin: 0 auto; }
        .box { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .btn { background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; font-weight: bold; display: inline-block; border: none; cursor: pointer; }
        .btn-danger { background: #dc3545; }
        .alert { background: #d4edda; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #343a40; color: white; }
        input, select { padding: 8px; margin-right: 10px; border: 1px solid #ccc; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Pasek nawigacyjny dostępny na każdej stronie -->
        <nav style="background: #343a40; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <div style="font-size: 20px; font-weight: bold;">
                <a href="{{ route('pliki.index') }}" style="color: white; text-decoration: none;">📊 DataLens Enterprise</a>
            </div>
            
            <div style="display: flex; gap: 20px; align-items: center;">
                <a href="{{ route('pliki.index') }}" style="color: #adb5bd; text-decoration: none; font-weight: 500;">📁 Pliki</a>
                <a href="{{ route('raporty.index') }}" style="color: #adb5bd; text-decoration: none; font-weight: 500;">📈 Raporty</a>
                <a href="{{ route('dashboardy.index') }}" style="color: #adb5bd; text-decoration: none; font-weight: 500;">🖥️ Dashboardy</a>
                <a href="{{ route('workspaces.index') }}" style="color: #adb5bd; text-decoration: none; font-weight: 500;">🏢 Zespół</a>
                
                @if(auth()->check() && auth()->user()->is_admin)
                    <a href="{{ route('admin.index') }}" style="color: #ffc107; text-decoration: none; font-weight: bold;">🛠️ Panel Admina</a>
                @endif
            </div>

            <div style="display: flex; align-items: center; gap: 15px;">
                @auth
                    <span style="font-size: 13px; color: #ced4da;">{{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" style="background: #dc3545; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: bold;">Wyloguj</button>
                    </form>
                @endauth
            </div>
        </nav>

        <!-- Komunikaty flash -->
        @if(session('sukces'))
            <div class="alert">{{ session('sukces') }}</div>
        @endif
        @if(session('blad'))
            <div class="alert" style="background: #f8d7da; color: #721c24;">{{ session('blad') }}</div>
        @endif

        <!-- Dynamiczna treść widoków -->
        @yield('content')
    </div>
</body>
</html>