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
        Schema::table('trip_tickets', function (Blueprint $table) {
            $table->string('supervisor')->nullable()->after('status');
            $table->string('passengers2')->nullable()->after('supervisor');
            $table->string('purpose2')->nullable()->after('passengers2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trip_tickets', function (Blueprint $table) {
            $table->dropColumn(['supervisor', 'passengers2', 'purpose2']);
        });
    }
};
