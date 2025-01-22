<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            // 1. Supprimer l'ancienne colonne banner
            $table->dropColumn('banner');
            
            // 2. Ajouter la nouvelle colonne banners
            $table->json('banners')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            // 1. Supprimer la nouvelle colonne banners
            $table->dropColumn('banners');
            
            // 2. Recréer l'ancienne colonne banner
            $table->json('banner')->nullable();
        });
    }
}; 