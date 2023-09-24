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
        Schema::table('shipments', function (Blueprint $table) {
            $table->string('label_url')->nullable(); // Assuming label_url is a nullable string
            $table->unsignedBigInteger('order_id');  // Assuming order_id is an unsigned big integer
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn('label_url');
            $table->dropColumn('order_id');
        });
    }
};
