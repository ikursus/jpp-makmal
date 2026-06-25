<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_application_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_application_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained()->onDelete('restrict');
            $table->integer('quantity_requested');
            $table->integer('quantity_approved')->nullable();
            $table->timestamps();

            $table->unique(['loan_application_id', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_application_items');
    }
};
