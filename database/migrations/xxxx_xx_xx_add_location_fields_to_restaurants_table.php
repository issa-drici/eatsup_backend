<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->string('postal_code', 10)->nullable()->after('address');
            $table->string('city')->nullable()->after('postal_code');
            $table->string('city_slug')->nullable()->after('city');
            $table->string('type_slug')->nullable()->after('city_slug');
        });
    }

    public function down()
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['postal_code', 'city', 'city_slug', 'type_slug']);
        });
    }
}; 