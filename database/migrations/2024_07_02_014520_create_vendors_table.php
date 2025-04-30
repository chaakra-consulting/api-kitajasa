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
        Schema::create('vendor', function (Blueprint $table) {
            $table->integer('id_vendor')->autoIncrement();
            $table->integer('id_user');
            $table->string('nama', 100);
            $table->string('deskripsi');
            $table->string('alamat');
            $table->string('kelurahan', 50);
            $table->string('kecamatan', 50);
            $table->string('kota', 50);
            $table->string('latlon', 100);
            $table->integer('rating');
            $table->bigInteger('nik');
            $table->string('ktp_vendor');
            $table->string('logo_vendor');
            $table->string('foto_vendor');
            $table->string('poin', 100);
            $table->boolean('isverified');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor');
    }
};
