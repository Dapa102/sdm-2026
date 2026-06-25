<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_corrections', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('attendance_log_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('corrected_by')->constrained('users')->restrictOnDelete();
            $table->string('correction_type');
            $table->json('old_value')->nullable();
            $table->json('new_value')->nullable();
            $table->text('correction_reason');
            $table->timestamps();

            $table->index(['attendance_log_id', 'correction_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_corrections');
    }
};
