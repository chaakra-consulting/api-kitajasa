<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_customer';
    protected $table = 'customer';

    protected $fillable = [
        'id_user',
        'nama_lengkap',
        'nomor_hp',
        'jenis_kelamin',
        'nomor_telepon',
        'kota',
        'kabupaten',
        'kecamatan',
        'desa_kel',
        'detail_alamat',
        'latlon',
        'photo_profil'
    ];
}
