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
        Schema::table('history_orders', function($table) {
            $table->unsignedBigInteger('color_id');
            $table->foreign('color_id')->references('id')->on('item_colors');
            $table->unsignedBigInteger('size_id');
            $table->foreign('size_id')->references('id')->on('item_sizes');
            $table->unsignedBigInteger('status_id');
            $table->foreign('status_id')->references('id')->on('order_statuses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('history_orders', function (Blueprint $table) {
            $table->dropColumn('color_id');
            $table->dropColumn('size_id');
            $table->dropColumn('status_id');
        });
    }
};
