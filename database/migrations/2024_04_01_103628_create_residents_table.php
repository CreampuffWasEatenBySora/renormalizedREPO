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
        Schema::create('barangay_residents', function (Blueprint $table) {
            $table->id();
            $table->string('UUID')->unique();
            $table->text('fullName');
            $table->string('email')->unique();
            $table->string('password');
            $table->char('status')->default('N');
            $table->char('access_level')->default('R');

            //foreign keys
            $table->unsignedBigInteger('address_id')->nullable();
            $table->unsignedBigInteger('registration_id')->nullable();


             // Imposing constraints in foreign keys
             $table -> foreign('address_id')
             -> references('id') 
             -> on('addresses') 
             -> onDelete('cascade');

             $table -> foreign('registration_id')
             -> references('id') 
             -> on('registrations') 
             -> onDelete('cascade');


            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangay_residents');
    }
};
