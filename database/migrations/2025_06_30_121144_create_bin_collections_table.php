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
        Schema::create('bin_collections', function (Blueprint $table) {
            $table->id();
            $table->date('collection_date');
            $table->string('bin_type'); // e.g., 'recycling', 'general', 'garden'
            $table->string('color')->nullable(); // e.g., 'green', 'black', 'brown'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bin_collections');
    }
};
