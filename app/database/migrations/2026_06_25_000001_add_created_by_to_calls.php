<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Track which registered user opened the call. Stays null for calls opened
     * anonymously through the public form.
     */
    public function up(): void
    {
        Schema::table('calls', function (Blueprint $table) {
            $table->foreignId('created_by')
                ->nullable()
                ->after('id')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('calls', function (Blueprint $table) {
            $table->dropConstrainedForeignId('created_by');
        });
    }
};
