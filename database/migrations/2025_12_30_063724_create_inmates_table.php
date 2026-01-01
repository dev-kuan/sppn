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
        Schema::create('inmates', function (Blueprint $table) {
             $table->id();
    $table->string('no_registrasi')->unique();
    $table->string('nama');
    $table->string('tempat_lahir');
    $table->date('tanggal_lahir');
    $table->enum('jenis_kelamin', ['laki-laki', 'perempuan']);
    $table->string('agama', 100);
    $table->string('tingkat_pendidikan', 100)->nullable();
    $table->string('pekerjaan_terakhir', 100)->nullable();
    $table->integer('lama_pidana_bulan');
    $table->integer('sisa_pidana_bulan');
    $table->integer('jumlah_residivisme')->default(0);
    $table->text('catatan_kesehatan')->nullable();
    $table->string('pelatihan')->nullable();
    $table->string('program_kerja')->nullable();
    $table->foreignId('crime_type_id')
          ->constrained('crime_types')
          ->cascadeOnDelete();
    $table->enum('status', ['aktif', 'dirilis', 'dipindahkan'])->default('aktif');
    $table->date('tanggal_masuk');
    $table->date('tanggal_bebas')->nullable();
    $table->timestamps();
    $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inmates');
    }
};
