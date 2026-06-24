<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add soft-delete support so removing a call or employee preserves history
     * (and is reversible) instead of destroying the row.
     */
    public function up(): void
    {
        Schema::table('calls', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('calls', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
