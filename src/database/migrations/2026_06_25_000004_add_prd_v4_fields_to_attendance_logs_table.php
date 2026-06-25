<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->foreignUuid('employee_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->foreignUuid('assignment_id')->nullable()->after('employee_id')->constrained()->nullOnDelete();
            $table->unsignedInteger('check_in_distance_meters')->nullable()->after('check_in_lng');
            $table->unsignedInteger('check_out_distance_meters')->nullable()->after('check_out_lng');
            $table->string('attendance_status')->default('hadir')->after('location_status');
            $table->string('verification_status')->default('valid')->after('attendance_status');
            $table->text('notes')->nullable()->after('rejection_reason');

            $table->index('employee_id');
            $table->index('assignment_id');
            $table->index('attendance_status');
            $table->index('verification_status');
        });

        DB::table('attendance_logs')
            ->where('location_status', 'OUT_OF_RANGE')
            ->update(['verification_status' => 'di_luar_lokasi']);

        DB::table('attendance_logs')
            ->where('approval_status', 'REJECTED')
            ->update(['verification_status' => 'ditolak']);
    }

    public function down(): void
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->dropIndex(['employee_id']);
            $table->dropIndex(['assignment_id']);
            $table->dropIndex(['attendance_status']);
            $table->dropIndex(['verification_status']);
            $table->dropConstrainedForeignId('employee_id');
            $table->dropConstrainedForeignId('assignment_id');
            $table->dropColumn([
                'check_in_distance_meters',
                'check_out_distance_meters',
                'attendance_status',
                'verification_status',
                'notes',
            ]);
        });
    }
};
