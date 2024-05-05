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
        Schema::create('document_requirements', function (Blueprint $table) {
            $table->id();
            
            $table->string('name');
            $table->string('description');
            

            $table->timestamps();
        });


        Schema::table('supplementary_requirements', function (Blueprint $table) {
            $table->dropColumn('requirement_type');
            $table->unsignedBigInteger('for_requirement_id');

              // Imposing consraints on the Foreign Keys
              $table -> foreign('for_requirement_id')
              -> references('id') 
              -> on('document_requirements') 
              -> onDelete('cascade');
            
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_requirements');
    }
};
