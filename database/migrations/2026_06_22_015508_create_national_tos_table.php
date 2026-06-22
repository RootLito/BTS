<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('national_to', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->string('to_no')->nullable();
            $table->date('date')->nullable();
            $table->json('personnel')->nullable(); 
            $table->string('route')->nullable();
            $table->date('departure')->nullable();
            $table->date('return_date')->nullable();
            $table->text('purpose')->nullable();
            $table->string('rd')->default('RELLY B. GARCIA');
            $table->string('oic')->default('EMMILOU J. UY, CPA, MBA');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('national_to');
    }
};