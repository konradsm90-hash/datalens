<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'data_source_id',
        'tytul',
        'typ_wykresu',
        'ustawienia_wykresu',
    ];

    protected $casts = [
        'ustawienia_wykresu' => 'array',
    ];

    public function dataSource()
    {
        return $this->belongsTo(DataSource::class, 'data_source_id');
    }

    public function dashboards()
    {
        return $this->belongsToMany(Dashboard::class, 'dashboard_report');
    }
}