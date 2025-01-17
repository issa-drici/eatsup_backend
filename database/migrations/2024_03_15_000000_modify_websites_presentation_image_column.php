<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('websites', function (Blueprint $table) {
            $table->dropColumn('presentation_image_url');
            $table->uuid('presentation_image_id')->nullable();
            $table->foreign('presentation_image_id')
                ->references('id')
                ->on('files')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('websites', function (Blueprint $table) {
            $table->dropForeign(['presentation_image_id']);
            $table->dropColumn('presentation_image_id');
            $table->string('presentation_image_url')->nullable();
        });
    }
}; 