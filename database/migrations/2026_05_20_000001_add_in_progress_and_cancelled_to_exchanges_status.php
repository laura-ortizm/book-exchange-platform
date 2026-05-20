<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Status is now a string column so SQLite and MySQL both work.
        // Allowed values are controlled by the application flow.
    }

    public function down(): void
    {
        // Nothing to revert.
    }
};
