<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $fillable = [
        'barang_id',
        'supplier_id',
        'pengeluaran_id',
        'pemasukan_id',
        'history_gaji_kloter_id',
        'qtyHistori',
        'satuan',
        'jumlahRp',
        'waktu_transaksi',
    ];

    protected static function booted()
    {
        static::creating(function ($transaksi) {
            if (!($transaksi->pemasukan_id xor $transaksi->pengeluaran_id)) {
                throw new \Exception('Transaksi harus punya salah satu: pemasukan atau pengeluaran.');
            }
        });
    }

    public function getKategoriAttribute()
    {
        return $this->pemasukan_id ? 'pemasukan' : 'pengeluaran';
    }


    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function pengeluaran()
    {
        return $this->belongsTo(Pengeluaran::class);
    }

    public function pemasukan()
    {
        return $this->belongsTo(Pemasukan::class);
    }

    public function historyGajiKloter()
    {
        return $this->belongsTo(HistoryGajiKloter::class);
    }

    public function getKodeGajiAttribute()
    {
        return $this->historyGajiKloter?->kode;
    }
}
