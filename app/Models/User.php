<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected static function booted()
    {
        static::created(function ($user) {
            $workspace = Workspace::create([
                'nazwa' => 'Przestrzeń użytkownika ' . $user->name,
                'user_id' => $user->id,
            ]);

            // Automatyczne przypisanie nowego użytkownika jako właściciela przestrzeni
            $user->workspaces()->attach($workspace->id, ['rola' => 'owner']);
        });
    }

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function workspaces()
    {
        return $this->belongsToMany(Workspace::class, 'workspace_user')->withPivot('rola')->withTimestamps();
    }
}