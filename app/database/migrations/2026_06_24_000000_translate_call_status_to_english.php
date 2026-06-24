<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Map of Portuguese status values to their English replacements.
     *
     * @var array<string, string>
     */
    private array $map = [
        'aberto'       => 'open',
        'em_andamento' => 'in_progress',
        'concluido'    => 'done',
        'recusado'     => 'rejected',
    ];

    /**
     * Convert the status column from a Portuguese enum to a plain string
     * (portable across MySQL/SQLite) and translate existing data.
     */
    public function up(): void
    {
        Schema::table('calls', function (Blueprint $table) {
            $table->string('status')->default('open')->change();
        });

        foreach ($this->map as $old => $new) {
            DB::table('calls')->where('status', $old)->update(['status' => $new]);
        }
    }

    public function down(): void
    {
        foreach ($this->map as $old => $new) {
            DB::table('calls')->where('status', $new)->update(['status' => $old]);
        }

        Schema::table('calls', function (Blueprint $table) {
            $table->enum('status', ['aberto', 'em_andamento', 'concluido', 'recusado'])
                ->default('aberto')
                ->change();
        });
    }
};
