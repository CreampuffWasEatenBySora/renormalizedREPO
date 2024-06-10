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
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();

             
            // Table-unique keys
            $table->timestamp('date_registered')-> useCurrent();
            $table->timestamp('date_responded')->  nullable();
            $table -> string('remarks')->  nullable();
            $table -> string('requirement_type')->  nullable();
            $table->string('selfie_filename')  ->nullable();
            $table->string('document_filename')  ->nullable();
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
