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
        Schema::create('websites', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));

            $table->foreignUuid('restaurant_id')->constrained('restaurants')->cascadeOnDelete();

            $table->string('domain')->nullable();
            $table->json('title')->nullable();
            $table->json('description')->nullable();

            $table->string('presentation_image_url')->nullable();
            $table->json('opening_hours')->nullable();
            // ex.: { "monday": { "start": "09:00", "end": "18:00" }, ... }

            $table->json('theme_config')->nullable();
            // ex.: { "colors": { "primary": "#ff0000" } }

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('websites');
    }
};
