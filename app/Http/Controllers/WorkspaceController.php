<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Workspace;
use App\Models\User;

class WorkspaceController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $workspaces = $user->workspaces;
        return view('workspaces.index', compact('workspaces'));
    }

    public function zapros(Request $request, $id)
    {
        $workspace = Workspace::findOrFail($id);
        
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'rola' => 'required|in:editor,viewer',
        ]);

        $zaproszonyUser = User::where('email', $request->email)->first();

        if ($workspace->users()->where('user_id', $zaproszonyUser->id)->exists()) {
            return redirect()->back()->with('blad', 'Ten użytkownik jest już członkiem tej przestrzeni.');
        }

        $workspace->users()->attach($zaproszonyUser->id, ['rola' => $request->rola]);

        return redirect()->back()->with('sukces', 'Użytkownik został pomyślnie zaproszony do przestrzeni!');
    }
}