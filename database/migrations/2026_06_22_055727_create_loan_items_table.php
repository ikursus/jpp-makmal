<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained()->onDelete('restrict');
            $table->integer('quantity_loaned');
            $table->integer('quantity_returned')->default(0);
            $table->enum('condition_before', ['baik', 'rosak', 'service']);
            $table->enum('condition_after', ['baik', 'rosak', 'service'])->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->timestamps();

            $table->unique(['loan_id', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_items');
    }
};
