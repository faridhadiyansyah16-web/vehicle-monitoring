<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('plate_number')->unique();
            $table->string('type')->index();
            $table->unsignedInteger('capacity')->nullable();
            $table->enum('fuel_type', ['diesel','gasoline','electric'])->nullable();
            $table->boolean('is_company_owned')->default(true);
            $table->enum('status', ['available','in_use','maintenance','inactive'])->default('available')->index();
            $table->date('next_service_date')->nullable();
            $table->unsignedInteger('odometer')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};

