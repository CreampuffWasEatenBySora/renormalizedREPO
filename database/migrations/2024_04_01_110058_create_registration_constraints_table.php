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
        Schema::table('registrations', function (Blueprint $table) {
            
            // Foreign Keys
            $table->string('resident_id')->nullable();
            $table->string('barangay_officer_id')->nullable();
            
            // Imposing consraints on the Foreign Keys
            $table -> foreign('resident_id')
                   -> references('UUID') 
                   -> on('users') 
                   -> onDelete('cascade');

            $table -> foreign('barangay_officer_id')
                   -> references('UUID') 
                   -> on('users') 
                   -> onDelete('cascade');
        });

        Schema::table('addresses', function (Blueprint $table) {
            
            // Foreign Keys
            $table->string('resident_id')->nullable();
            
            // Imposing consraints on the Foreign Keys
            $table -> foreign('resident_id')
                   -> references('UUID') 
                   -> on('users') 
                   -> onDelete('cascade');
 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registration_constraints');
    }
};
