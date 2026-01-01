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
        Schema::create('observation_items', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->foreignId('variabel_id')->constrained('assessment_variabels');
            $table->foreignId('aspect_id')->constrained('assessment_aspects');
            $table->string('nama_item');
            $table->decimal('bobot_item', 10, 2)->default(1);
            $table->decimal('bobot', 10, 2)->default(1.00);
            $table->boolean('is_conditional_weight')->default(false);
            $table->integer('frekuensi_bulan');
            $table->foreignId('frequency_rule_id')
                ->nullable()
                ->constrained('frequency_rules')
                ->nullOnDelete();
            $table->boolean('use_dinamyc_frequency')->default(false);
            $table->integer('sort_order')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('observation_items');
    }
};
