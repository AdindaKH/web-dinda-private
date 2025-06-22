<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kloter extends Model
{
    protected $fillable = ['nama_kloter'];

    public function presensis()
    {
        return $this->hasMany(Presensi::class);
    }

    public function tonIkan()
    {
        return $this->hasOne(TonIkan::class);
    }

    public function kloterKaryawans()
    {
        return $this->hasMany(KloterKaryawan::class);
    }

    public function getTanggalMulaiAttribute()
    {
        return $this->presensis()->orderBy('tanggal', 'asc')->value('tanggal');
    }

    public function getTanggalAkhirAttribute()
    {
        return $this->presensis()->orderBy('tanggal', 'desc')->value('tanggal');
    }

    public function getJumlahTonAttribute()
    {
        return $this->tonIkan()->value('jumlah_ton') ?? 0;
    }

}
