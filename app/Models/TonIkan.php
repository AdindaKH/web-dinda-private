<?php

// app/Models/TonIkan.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TonIkan extends Model
{
    protected $fillable = [
        // 'tanggal',
        'kloter_id',
        'jumlah_ton', 
        'harga_ikan_per_ton'
    ];

    public function kloter()
    {
        return $this->belongsTo(Kloter::class);
    }

}
