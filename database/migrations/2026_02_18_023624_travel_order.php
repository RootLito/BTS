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
        Schema::create('travel_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('trip_tickets')->onDelete('cascade');
            $table->date('date');
            $table->json('personnel')->nullable(); 
            $table->string('departure')->nullable();
            $table->string('return')->nullable();
            $table->text('destination')->nullable();
            $table->text('purpose')->nullable();
            $table->json('recommended_by')->nullable();
            $table->string('approved_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_orders');
    }
};