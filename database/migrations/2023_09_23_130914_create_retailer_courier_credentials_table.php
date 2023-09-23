<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    public function up()
    {
        Schema::create('retailer_courier_credentials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('retailer_id');
            $table->unsignedBigInteger('courier_id');
            $table->string('api_key');
            $table->string('account_id');
            $table->timestamps();

            $table->unique(['retailer_id', 'courier_id']);
            $table->foreign('retailer_id')->references('id')->on('retailers');
            $table->foreign('courier_id')->references('id')->on('couriers');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retailer_courier_credentials');
    }
};
