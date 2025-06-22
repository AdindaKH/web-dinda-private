<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_barang',
        'qty',
        'exp',
        'harga',
    ];
    
    public function produk()
    {
        return $this->hasOne(BarangProduk::class);
    }

    public function pendukung()
    {
        return $this->hasOne(BarangPendukung::class);
    }

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class);
    }

    public function kardus()
    {
        return $this->hasMany(BarangPendukung::class)->whereHas('barang', function ($query) {
            $query->where('nama_barang', 'like', '%kardus%');
        });
    }

    public static function uangMakanHarian()
    {
        return self::where('nama_barang', 'like', '%makan%')->first();
    }
}
