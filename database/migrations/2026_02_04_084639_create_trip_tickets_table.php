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
        Schema::create('trip_tickets', function (Blueprint $table) {
            $table->id();
            $table->text('purpose');
            $table->string('destination');
            $table->date('start_date');
            $table->date('end_date');
            $table->json('passengers'); 
            $table->foreignId('driver_id')->constrained('drivers');
            $table->foreignId('vehicle_id')->constrained('vehicles');
            $table->string('status')->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_tickets');
    }
};
