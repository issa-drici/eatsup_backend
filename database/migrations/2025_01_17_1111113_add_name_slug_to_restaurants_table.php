<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->string('name_slug')->nullable()->after('name');
        });
    }

    public function down()
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn('name_slug');
        });
    }
}; 