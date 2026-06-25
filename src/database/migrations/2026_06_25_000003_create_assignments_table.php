<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('employee_id')->constrained()->restrictOnDelete();
            $table->foreignUuid('work_location_id')->constrained()->restrictOnDelete();
            $table->foreignUuid('supervisor_user_id')->constrained('users')->restrictOnDelete();
            $table->date('assignment_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('assignment_status')->default('terjadwal');
            $table->timestamps();

            $table->index(['assignment_date', 'assignment_status']);
            $table->index(['employee_id', 'assignment_date']);
            $table->index(['work_location_id', 'assignment_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
