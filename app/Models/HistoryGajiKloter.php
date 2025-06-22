<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

// app/Models/HistoryGajiKloter.php
class HistoryGajiKloter extends Model
{
    // protected $table = 'history_gaji_kloter';

    protected $fillable = [
        'kloter_id',
        'kode', 
        'jml_karyawan', 
        'total_gaji', 
        'waktu',
        'tanggal_awal',
        'tanggal_akhir',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Jika kode belum diset secara manual
            if (empty($model->kode)) {
                $last = self::orderBy('id', 'desc')->first();
                $nextId = $last ? $last->id + 1 : 1;
                $model->kode = 'PGJ' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    public function kloter()
    {
        return $this->belongsTo(Kloter::class);
    }

    public function pengeluaranGaji()
    {
        return $this->hasOne(PengeluaranGaji::class);
    }

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class);
    }
}
