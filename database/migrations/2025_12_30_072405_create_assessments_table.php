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
        Schema::create('assessment_variabels', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50);
            $table->timestamps();
        });

        Schema::create('assessment_aspects', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50);

            $table->foreignId('assessment_variabel_id')
                  ->nullable()
                  ->constrained('assessment_variabels')
                  ->nullOnDelete();

            $table->timestamps();
        });

        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inmate_id')->constrained('inmates')->cascadeOnDelete();
            $table->date('tanggal_penilaian');
            $table->integer('bulan');
            $table->integer('tahun');
            $table->unique(['inmate_id', 'bulan', 'tahun']);
            $table->decimal('skor_kepribadian', 10, 4)->nullable();
            $table->decimal('skor_kemandirian', 10, 4)->nullable();
            $table->decimal('skor_sikap', 10, 4)->nullable();
            $table->decimal('skor_mental', 10, 4)->nullable();
            $table->decimal('skor_total', 10, 4)->nullable();
            $table->enum('status', ['draf', 'disubmit', 'diterima', 'ditolak'])->default('draf');
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });


         Schema::create('assessment_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('variabel_id')->constrained('assessment_variabels');
            $table->foreignId('aspect_id')->constrained('assessment_aspects');
            $table->decimal('skor', 10, 4);
            $table->decimal('bobot', 10, 4);
            $table->decimal('skor_terbobot', 10, 4);
            $table->string('kategori')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_variabels');
        Schema::dropIfExists('assessment_aspects');
        Schema::dropIfExists('assessments');
        Schema::dropIfExists('assessment_scores');
    }
};
