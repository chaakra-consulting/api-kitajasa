<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;
    protected $table = 'vendor';
    protected $primaryKey = 'id_vendor';

    protected $fillable = [
        'id_user',
        'nama',
        'deskripsi',
        'alamat',
        'kelurahan',
        'kecamatan',
        'kota',
        'latlon',
        'rating',
        'nik',
        'ktp_vendor',
        'logo_vendor',
        'foto_vendor',
        'poin',
        'isverified'
    ];
}
