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
        Schema::create('albums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('remote_id')->constrained();
            $table->foreignId('venue_id')->constrained();
            $table->enum('status', ['live', 'longterm'])->default('live');
            $table->timestamp('date_add')->useCurrent();
            $table->timestamp('date_upd')->nullable();
            $table->timestamp('date_over')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('albums');
    }
};
