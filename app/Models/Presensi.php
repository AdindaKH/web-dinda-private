<?php
// app/Models/Presensi.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'karyawan_id',
        'kloter_id',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
    
    public function kloter()
    {
        return $this->belongsTo(Kloter::class);
    }

}
