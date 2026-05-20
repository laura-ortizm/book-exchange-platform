<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exchanges', function (Blueprint $table) {
            $table->timestamp('requester_confirmed_at')->nullable();
            $table->timestamp('owner_confirmed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('exchanges', function (Blueprint $table) {
            $table->dropColumn(['requester_confirmed_at', 'owner_confirmed_at']);
        });
    }
};
