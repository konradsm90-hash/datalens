<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataSource extends Model
{
    protected $fillable = [
        'user_id',
        'nazwa',
        'sciezka_do_pliku',
        'typ_pliku',
        'struktura_kolumn',
        'rozmiar_kb',
    ];

    protected $casts = [
        'struktura_kolumn' => 'array',
    ];
}