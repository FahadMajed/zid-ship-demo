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
        Schema::create('pricings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('courier_route_id');
            $table->unsignedBigInteger('delivery_type_id');
            $table->decimal('price', 8, 2);
            $table->timestamps();

            $table->foreign('courier_route_id')->references('id')->on('courier_routes');
            $table->foreign('delivery_type_id')->references('id')->on('delivery_types');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricings');
    }
};
