<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('integration_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('provider_name');
            $table->string('action');
            $table->string('request_status');
            $table->text('response_message')->nullable();
            $table->timestamps();

            $table->index(['provider_name', 'request_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integration_logs');
    }
};
