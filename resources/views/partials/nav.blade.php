<nav style="background: #343a40; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="font-size: 18px; font-weight: bold;">
        <a href="{{ route('pliki.index') }}" style="color: white; text-decoration: none;">📊 DataLens</a>
    </div>
    <div style="display: flex; gap: 20px; align-items: center;">
        <a href="{{ route('pliki.index') }}" style="color: #adb5bd; text-decoration: none;">📁 Pliki</a>
        <a href="{{ route('raporty.index') }}" style="color: #adb5bd; text-decoration: none;">📈 Raporty</a>
        <a href="{{ route('dashboardy.index') }}" style="color: #adb5bd; text-decoration: none;">🖥️ Dashboardy</a>
        <a href="{{ route('workspaces.index') }}" style="color: #adb5bd; text-decoration: none;">🏢 Zespół</a>
        @if(auth()->check() && auth()->user()->is_admin)
            <a href="{{ route('admin.index') }}" style="color: #ffc107; text-decoration: none; font-weight: bold;">🛠️ Admin</a>
        @endif
    </div>
    <div>
        @auth
            <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" style="background: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 12px;">Wyloguj</button>
            </form>
        @endauth
    </div>
</nav>