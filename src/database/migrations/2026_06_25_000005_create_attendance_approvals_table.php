<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_approvals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('attendance_log_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('approved_by')->constrained('users')->restrictOnDelete();
            $table->string('approval_status');
            $table->text('approval_note')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index(['attendance_log_id', 'approval_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_approvals');
    }
};
