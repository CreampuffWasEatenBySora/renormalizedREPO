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
        Schema::create('collection_records', function (Blueprint $table) {
            $table->id();

            // Foreign Keys
            $table->unsignedBigInteger('request_id');
            
            // Table-unique keys
            $table->timestamp('date_granted')-> useCurrent();
            $table->timestamp('date_scheduled')-> nullable();
            $table->timestamp('date_collected')-> nullable();
            $table -> string('status', 3)->default('PEN');
            $table -> string('remarks')->default(null);
            $table->timestamps();

            // Imposing consraints on the Foreign Keys
            $table -> foreign('request_id')
                   -> references('id') 
                   -> on('request_records') 
                   -> onDelete('cascade');
 
                    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collection_records');
    }
};
