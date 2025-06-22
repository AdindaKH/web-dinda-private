<?php
// app/Models/Karyawan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'jenis_kelamin',
        'no_telepon',
    ];

    public function presensis()
    {
        return $this->hasMany(Presensi::class);
    }
    
    public function kloterKaryawans()
    {
        return $this->hasMany(KloterKaryawan::class);
    }
}
?>