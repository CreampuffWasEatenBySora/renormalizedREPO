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
        Schema::create('request_records', function (Blueprint $table) {
            $table->id();

            // Foreign Keys
            $table->string('resident_id');
            $table->string('barangay_officer_id');
            
            // Table-unique keys
            $table->timestamp('date_requested')-> useCurrent();
            $table->timestamp('date_responded')-> nullable();
            $table -> string('status', 3)->default('PEN');
            $table -> string('remarks')->default(null);
            $table->timestamps();

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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_records');
    }
};
