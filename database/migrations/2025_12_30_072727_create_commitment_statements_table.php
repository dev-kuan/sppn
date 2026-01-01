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
        Schema::create('commitment_statements', function (Blueprint $table) {
            $table->id();
    $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
    $table->enum('jenis', ['nkri', 'narkoba']);
    $table->boolean('is_signed')->default(false);
    $table->timestamp('signed_at')->nullable();
    $table->text('catatan')->nullable();
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commitment_statements');
    }
};
