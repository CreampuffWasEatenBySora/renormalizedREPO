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
        Schema::create('requested_document', function (Blueprint $table) {
            $table->id();


            // Foreign Keys
            $table->unsignedBigInteger('for_request_id');
            $table->unsignedBigInteger('for_document_id');
              
            // Table-unique keys
            $table->string('request_reason');
            $table->string('request_quantity');
            $table->timestamps();
            

            // Imposing consraints on the Foreign Keys
            $table -> foreign('for_request_id')
                   -> references('id') 
                   -> on('request_records') 
                   -> onDelete('cascade');

            $table -> foreign('for_document_id')
                   -> references('id') 
                   -> on('documents') 
                   -> onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requested_document');
    }
};
