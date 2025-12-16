<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->index('start_time');
            $table->index('end_time');
        });
        Schema::table('fuel_logs', function (Blueprint $table) {
            $table->index('date');
        });
        Schema::table('service_logs', function (Blueprint $table) {
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['start_time']);
            $table->dropIndex(['end_time']);
        });
        Schema::table('fuel_logs', function (Blueprint $table) {
            $table->dropIndex(['date']);
        });
        Schema::table('service_logs', function (Blueprint $table) {
            $table->dropIndex(['date']);
        });
    }
};

