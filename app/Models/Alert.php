<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Alert extends Model
{
    use HasFactory;

    protected $table = 'alerts';

    protected $fillable = [
        'user_id',
        'bencana_id',
        'lokasi_id',
        'jarak_km',
        'status',
        'sent_at',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'jarak_km' => 'decimal:2',
            'sent_at' => 'datetime',
            'read_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bencana()
    {
        return $this->belongsTo(Bencana::class);
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class);
    }
}
