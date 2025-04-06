<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('email_variables', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description');
            $table->string('example');
            $table->timestamps();
        });

        // Insérer les variables par défaut
        DB::table('email_variables')->insert([
            [
                'name' => '{name}',
                'description' => 'Nom du destinataire',
                'example' => 'John Doe',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{email}',
                'description' => 'Adresse email du destinataire',
                'example' => 'john@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{company}',
                'description' => "Nom de l'entreprise",
                'example' => 'ACME Corp',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{date}',
                'description' => 'Date courante',
                'example' => '01/01/2024',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{unsubscribe_link}',
                'description' => 'Lien de désabonnement',
                'example' => 'https://example.com/unsubscribe',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('email_variables');
    }
}; 