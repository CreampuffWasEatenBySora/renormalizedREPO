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
        Schema::create('requirement_listing', function (Blueprint $table) {
            $table->id();
             // Foreign Keys
             $table->unsignedBigInteger('for_document_id');
             $table->unsignedBigInteger('from_requirement_id');
               

             
            // Imposing consraints on the Foreign Keys
            $table -> foreign('for_document_id')
            -> references('id') 
            -> on('barangay_documents') 
            -> onDelete('cascade');

            $table -> foreign('from_requirement_id')
            -> references('id') 
            -> on('document_requirements') 
            -> onDelete('cascade');

           

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requirement_listing');
    }
};
