<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Konsumen extends Model
{
    use HasFactory;

    protected $fillable = ['supplier_id', 'kode'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
