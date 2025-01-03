<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));

            $table->foreignUuid('restaurant_id')->constrained('restaurants')->cascadeOnDelete();

            $table->json('name'); // { "fr": "Menu Principal", "en": "Main Menu" }
            $table->string('status', 50)->default('active'); // 'draft', 'active', ...
            
            // Bannière publicitaire
            $table->json('banner')->nullable(); 
            // ex: { "image_url": "...", "link": "...", "text": "Promo" }

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
