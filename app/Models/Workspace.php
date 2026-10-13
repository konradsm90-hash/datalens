<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    use HasFactory;

    protected $fillable = ['nazwa', 'user_id'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'workspace_user')->withPivot('rola')->withTimestamps();
    }

    public function dataSources()
    {
        return $this->hasMany(DataSource::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function dashboards()
    {
        return $this->hasMany(Dashboard::class);
    }
}