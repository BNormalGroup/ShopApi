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
        Schema::create('order_delivery_addresses', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('firstName');
            $table->string('lastName');
            $table->string('phoneNumber');
            $table->string('country');
            $table->string('postcode');
            $table->string('city');
            $table->string('streetAddress');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_delivery_addresses');
    }
};
