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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
           
           $table->enum('status', [
            'pending',
            'assigned',
            'in_transit',
            'delivered',
           ])->default('pending');    


           $table->decimal('total_amount', 10, 2)->default(0);
           $table->string('delivery_address');
           $table->string('payment_method')->default('cash_on_delivery');
           
            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
