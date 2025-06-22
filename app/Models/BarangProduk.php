<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangProduk extends Model
{
    use HasFactory;

    protected $fillable = [
        'barang_id',
        'kode',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
