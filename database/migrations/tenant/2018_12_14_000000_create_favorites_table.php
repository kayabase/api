<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFavoritesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create(config('favorite.favorites_table'), function (Blueprint $table) {
            $table->id();
            $table->foreignId(config('favorite.user_foreign_key', 'user_id'))->constrained(config('favorite.user_table', 'users'))->cascadeOnDelete()->comment('user_id');
            $table->morphs('favoriteable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists(config('favorite.favorites_table'));
    }
}
