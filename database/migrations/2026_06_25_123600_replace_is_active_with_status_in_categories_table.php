<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->enum('status', ['dipinjam', 'dalam_proses_permohonan', 'dikembalikan'])->default('dipinjam')->after('description');
        });

        // Migrate existing data: is_active=true -> dipinjam, is_active=false -> dikembalikan
        DB::statement("UPDATE categories SET status = CASE WHEN is_active = 1 THEN 'dipinjam' ELSE 'dikembalikan' END");

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('is_active')->default(true);
        });

        DB::statement("UPDATE categories SET is_active = CASE WHEN status = 'dikembalikan' THEN 0 ELSE 1 END");

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
