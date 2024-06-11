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
        Schema::create('notifications', function (Blueprint $table) {
            
            $table->id();

             $table->unsignedBigInteger('for_user_id');
             $table->unsignedBigInteger('from_user_id');
             $table->unsignedBigInteger('for_event_id');
             $table->string('event_type');
             $table->string('event_description');
             $table->boolean('read_status')->default(false);
            
            
            // Imposing consraints on the Foreign Keys
            $table -> foreign('for_user_id')
                   -> references('id') 
                   -> on('users') 
                   -> onDelete('cascade');

            // Imposing consraints on the Foreign Keys
            $table -> foreign('from_user_id')
            -> references('id') 
            -> on('users') 
            -> onDelete('cascade');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
