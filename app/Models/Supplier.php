<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = 
        [
            'nama', 
            'alamat', 
            'no_tlp',
            'no_rekening',
        ];

    public function pemasok()
    {
        return $this->hasOne(Pemasok::class);
    }

    public function konsumen()
    {
        return $this->hasOne(Konsumen::class);
    }

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class);
    }
}
