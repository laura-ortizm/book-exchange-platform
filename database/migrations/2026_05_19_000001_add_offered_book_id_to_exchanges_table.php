<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exchanges', function (Blueprint $table) {
            $table->foreignId('offered_book_id')
                ->nullable()
                ->after('book_id')
                ->constrained('books')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('exchanges', function (Blueprint $table) {
            $table->dropConstrainedForeignId('offered_book_id');
        });
    }
};
