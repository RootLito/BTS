<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('travel_order_counters', function (Blueprint $table) {
            // 1. Drop the foreign key relationship constraint first
            $table->dropForeign(['trip_ticket_id']);

            // 2. Safely drop the composite unique constraint index now
            $table->dropUnique(['trip_ticket_id', 'period_key']);
        });

        Schema::table('travel_order_counters', function (Blueprint $table) {
            // 3. Re-add the column as nullable and wire back the raw foreign key definition
            $table->foreignId('trip_ticket_id')
                ->nullable()
                ->change()
                ->constrained('trip_tickets')
                ->onDelete('cascade');

            // 4. Create your new national_to_id field right next to it
            $table->foreignId('national_to_id')
                ->nullable()
                ->after('trip_ticket_id')
                ->constrained('national_to')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('travel_order_counters', function (Blueprint $table) {
            $table->dropForeign(['national_to_id']);
            $table->dropColumn('national_to_id');

            $table->dropForeign(['trip_ticket_id']);
        });

        Schema::table('travel_order_counters', function (Blueprint $table) {
            $table->foreignId('trip_ticket_id')
                ->nullable(false)
                ->change()
                ->constrained('trip_tickets')
                ->onDelete('cascade');

            $table->unique(['trip_ticket_id', 'period_key']);
        });
    }
};