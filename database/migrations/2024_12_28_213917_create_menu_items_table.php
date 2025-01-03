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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));

            $table->foreignUuid('category_id')->constrained('menu_categories')->cascadeOnDelete();

            $table->json('name');        // { "fr": "Pizza", "en": "Pizza" }
            $table->json('description')->nullable();
            $table->decimal('price', 10, 2);

            $table->string('allergens')->nullable();
            $table->json('images')->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
