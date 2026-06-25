<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->text('description')->nullable();
            $table->integer('quantity')->default(0);
            $table->integer('available_quantity')->default(0);
            $table->enum('condition', ['baik', 'rosak', 'service'])->default('baik');
            $table->enum('status', ['tersedia', 'dipinjam', 'disimpan', 'rosak'])->default('tersedia');
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->foreignId('storage_location_id')->constrained()->onDelete('restrict');
            $table->date('expiry_date')->nullable();
            $table->string('image')->nullable();
            $table->string('qr_code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('status');
            $table->index('condition');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
