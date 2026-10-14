<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataHistory extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'data_source_id', 'akcja', 'szczegoly'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dataSource()
    {
        return $this->belongsTo(DataSource::class);
    }
}