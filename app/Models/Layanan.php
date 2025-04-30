<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    use HasFactory;
    protected $table = 'layanan';
    protected $primaryKey = 'id_layanan';

    protected $fillable = [
        'id_vendor',
        'id_category',
        'nama_layanan',
        'deskripsi',
        'harga_layanan',
        'rating_layanan',
        'foto_layanan'
    ];
}
