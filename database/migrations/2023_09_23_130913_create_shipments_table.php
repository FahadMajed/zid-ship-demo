<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // create_shipments_table.php
    public function up()
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('courier_id');
            $table->unsignedBigInteger('courier_route_id');
            $table->unsignedBigInteger('delivery_type_id');
            $table->string('waybill_url')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('status');
            $table->timestamp('timestamp')->nullable();
            $table->unsignedBigInteger('retailer_id');
            $table->unsignedBigInteger('package_id');
            $table->string('customer_phone');
            $table->string('customer_city');
            $table->string('customer_email');
            $table->text('customer_address');
            $table->decimal('price', 8, 2);
            $table->timestamps();

            $table->foreign('courier_id')->references('id')->on('couriers');
            $table->foreign('courier_route_id')->references('id')->on('courier_routes');
            $table->foreign('delivery_type_id')->references('id')->on('delivery_types');
            $table->foreign('retailer_id')->references('id')->on('retailers');
            $table->foreign('package_id')->references('id')->on('packages');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
