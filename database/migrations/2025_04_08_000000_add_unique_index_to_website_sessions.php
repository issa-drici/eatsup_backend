<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('website_sessions', function (Blueprint $table) {
            // Créer un index unique sur la combinaison des colonnes
            $table->unique(['website_id', 'ip_address', 'user_agent', 'created_at'], 'website_sessions_unique_visit');
        });
    }

    public function down(): void
    {
        Schema::table('website_sessions', function (Blueprint $table) {
            $table->dropUnique('website_sessions_unique_visit');
        });
    }
};
