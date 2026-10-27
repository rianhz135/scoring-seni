<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('status_kontrol_juri_tanding')) {
            Schema::create('status_kontrol_juri_tanding', function (Blueprint $table) {
                $table->id();
                $table->foreignId('id_partai')->constrained('partai_tanding')->cascadeOnDelete();
                $table->integer('no_juri'); // 1, 2, 3
                $table->boolean('is_active')->default(true); // true = Active, false = Paused
                $table->enum('status', ['Active', 'Paused'])->default('Active');
                $table->timestamps();

                $table->unique(['id_partai', 'no_juri']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_kontrol_juri_tanding');
    }
};
