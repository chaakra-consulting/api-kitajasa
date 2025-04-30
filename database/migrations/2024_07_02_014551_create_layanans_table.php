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
        Schema::create('layanan', function (Blueprint $table) {
            $table->integer('id_layanan')->autoIncrement();
            $table->integer('id_vendor');
            $table->integer('id_category');
            $table->string('nama_layanan', 100);
            $table->text('deskripsi');
            $table->bigInteger('harga_layanan');
            $table->integer('rating_layanan');
            $table->string('foto_layanan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('layanan');
    }
};
