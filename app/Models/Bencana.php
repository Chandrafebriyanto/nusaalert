<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bencana extends Model
{
    use HasFactory;

    protected $table = 'bencana';

    protected $fillable = [
        'user_id',
        'event_id',
        'jenis_bencana',
        'magnitude',
        'kedalaman_km',
        'latitude',
        'longitude',
        'wilayah',
        'sumber_api',
        'raw_data',
        'terjadi_pada',
    ];

    protected function casts(): array
    {
        return [
            'magnitude' => 'decimal:1',
            'kedalaman_km' => 'decimal:1',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'raw_data' => 'array',
            'terjadi_pada' => 'datetime',
        ];
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
