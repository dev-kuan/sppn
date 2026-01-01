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
        Schema::create('commitment_recommendations', function (Blueprint $table) {
            $table->id();
    $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
    $table->text('deskripsi');
    $table->boolean('layak_dapat_hak')->default(false);
    $table->foreignId('recommended_by')->constrained('users');
    $table->foreignId('approved_by')->nullable()->constrained('users');
    $table->timestamp('approved_at')->nullable();
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commitment_recommendations');
    }
};
