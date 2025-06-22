<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KloterKaryawan extends Model
{
    protected $fillable = ['kloter_id', 'karyawan_id'];

    public function kloter()
    {
        return $this->belongsTo(Kloter::class);
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}

