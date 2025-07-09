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
        Schema::create('smart_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('platform_id')->constrained('smart_home_platforms')->onDelete('cascade');
            $table->string('name');
            $table->string('device_id');
            $table->string('device_type');
            $table->string('room')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('capabilities')->nullable();
            $table->json('last_state')->nullable();
            $table->timestamp('last_updated')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('smart_devices');
    }
};
