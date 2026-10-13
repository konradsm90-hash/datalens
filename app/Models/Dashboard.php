<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dashboard extends Model
{
    use HasFactory;

    protected $fillable = ['tytul', 'opis'];

    public function reports()
    {
        return $this->belongsToMany(Report::class, 'dashboard_report');
    }
}