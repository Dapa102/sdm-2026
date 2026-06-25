<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_locations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('location_name');
            $table->string('client_name')->nullable();
            $table->text('address');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->unsignedInteger('radius_tolerance')->default(300);
            $table->string('status')->default('active');
            $table->timestamps();

            $table->index(['status', 'location_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_locations');
    }
};
