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
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('seller_id');
            $table->string('user_name');
            $table->string('user_email');
            $table->text('message');
            $table->integer('quantity');
            $table->decimal('approx_order_value', 10, 2);
            $table->enum('status', ['pending', 'responded', 'resolved'])->default('pending');
            $table->timestamps();
        
            $table->foreign('seller_id')->references('id')->on('users')->onDelete('cascade');
            // Optionally, add a foreign key for product_id if needed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};
