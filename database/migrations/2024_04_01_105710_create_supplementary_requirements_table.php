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
        Schema::create('submitted_requirements', function (Blueprint $table) {
            $table->id();

            // Foreign Keys
            $table->unsignedBigInteger('from_request_id');
            $table->unsignedBigInteger('for_document_id');
              
            // Table-unique keys
            $table->string('requirement_type');
            $table->string('requirement_filename');

            $table->timestamps();
            

            // Imposing consraints on the Foreign Keys
            $table -> foreign('from_request_id')
                   -> references('id') 
                   -> on('request_records') 
                   -> onDelete('cascade');

            $table -> foreign('for_document_id')
                   -> references('id') 
                   -> on('barangay_documents') 
                   -> onDelete('cascade');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submitted_requirements');
    }
};
