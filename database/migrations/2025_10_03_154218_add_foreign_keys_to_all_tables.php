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
        // Foreign keys para users
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('rol_id')->references('id')->on('roles');
        });

        // Foreign keys para reservations
        Schema::table('reservations', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('court_id')->references('id')->on('courts');
        });

        // Foreign keys para reservation_items
        Schema::table('reservation_items', function (Blueprint $table) {
            $table->foreign('reservation_id')->references('id')->on('reservations')->onDelete('cascade');
        });

        // Foreign keys para orders
        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('reservation_id')->references('id')->on('reservations');
        });

        // Foreign keys para order_items
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products');
        });

        // Foreign keys para payments
        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Quitar foreign keys en orden inverso
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropForeign(['product_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['reservation_id']);
        });

        Schema::table('reservation_items', function (Blueprint $table) {
            $table->dropForeign(['reservation_id']);
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['court_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['rol_id']);
        });
    }
};
