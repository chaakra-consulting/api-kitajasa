<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customer', function (Blueprint $table) {
            $table->integer('id_customer')->autoIncrement();
            $table->integer('id_user');
            $table->string('nama_lengkap', 100);
            $table->bigInteger('nomor_hp');
            $table->string('jenis_kelamin', 50);
            $table->bigInteger('nomor_telepon');
            $table->string('kota', 100);
            $table->string('kabupaten', 100);
            $table->string('kecamatan', 100);
            $table->string('desa_kel', 100);
            $table->string('detail_alamat');
            $table->string('latlon', 100);
            $table->string('photo_profil');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer');
    }
};
