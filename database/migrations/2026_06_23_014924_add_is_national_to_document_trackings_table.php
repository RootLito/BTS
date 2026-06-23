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
        Schema::table('document_trackings', function (Blueprint $table) {
            $table->foreignId('trip_ticket_id')
                ->nullable()
                ->change();
            $table->foreignId('national_to_id')
                ->nullable()
                ->after('trip_ticket_id')
                ->constrained('national_to')
                ->onDelete('cascade');
            $table->boolean('is_national')->default(false)->after('national_to_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_trackings', function (Blueprint $table) {
            $table->dropColumn('is_national');
            $table->dropForeign(['national_to_id']);
            $table->dropColumn('national_to_id');
            $table->foreignId('trip_ticket_id')
                ->nullable(false)
                ->change();
        });
    }
};