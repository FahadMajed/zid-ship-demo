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
        if (!Schema::hasTable('courier_routes')) {
            Schema::create('courier_routes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('courier_id');
                $table->string('origin');
                $table->string('destination');
                $table->timestamps();

                $table->foreign('courier_id')->references('id')->on('couriers')->onDelete('cascade');
            });
        }
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courier_routes');
    }
};
