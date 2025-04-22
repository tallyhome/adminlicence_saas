<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Supprimer la migration en double de la table des migrations
        DB::table('migrations')
            ->where('migration', '2025_04_21_185425_add_user_id_to_licences_table')
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rien à faire ici, car nous ne voulons pas réintroduire la migration en double
    }
};
