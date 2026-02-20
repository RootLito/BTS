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
            $table->foreignId('driver_id')
                ->nullable()
                ->constrained('drivers');
            $table->foreignId('vehicle_id')
                ->nullable()
                ->constrained('vehicles');
            $table->string('status')->default('Pending');
            $table->foreignId('client_id')
                ->nullable()
                ->constrained('clients')
                ->onDelete('cascade');
            $table->string('office')->nullable();
            $table->string('supervisor')->nullable();
            $table->string('passengers2')->nullable();
            $table->string('purpose2')->nullable();
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
