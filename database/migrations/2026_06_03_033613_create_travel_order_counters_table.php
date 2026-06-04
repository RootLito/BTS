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
        Schema::create('travel_order_counters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_ticket_id')
                  ->constrained('trip_tickets')
                  ->onDelete('cascade');
            $table->string('period_key'); 
            $table->integer('current_value')->default(0);
            $table->timestamps();
            $table->unique(['trip_ticket_id', 'period_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_order_counters');
    }
};