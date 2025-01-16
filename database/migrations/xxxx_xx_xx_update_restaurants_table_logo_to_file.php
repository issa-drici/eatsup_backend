<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn('logo_url');
            $table->uuid('logo_id')->nullable();
            $table->foreign('logo_id')->references('id')->on('files');
        });
    }

    public function down()
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->string('logo_url')->nullable();
            $table->dropForeign(['logo_id']);
            $table->dropColumn('logo_id');
        });
    }
}; 