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
        Schema::create('qr_codes', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));

            $table->foreignUuid('restaurant_id')->nullable()->constrained('restaurants')->nullOnDelete();
            $table->foreignUuid('menu_id')->nullable()->constrained('menus')->nullOnDelete();

            $table->string('qr_type', 50)->nullable();

            $table->string('label')->nullable(); // ex: "Table 12"
            $table->string('status', 50)->default('unassigned');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qr_codes');
    }
};
