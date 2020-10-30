<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecipesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('type');
        Schema::create('type', function(Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
        });

        Schema::dropIfExists('ingredient');
        Schema::create('ingredient', function(Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image_url');
        });

        Schema::dropIfExists('recipe');
        Schema::create('recipe', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_type')->references('id')->on('type');
            $table->string('name');
            $table->enum('difficulty', ['Super Simple', 'Fairly Easy', 'Average', 'Hard', 'Very Difficult']);
            $table->string('image_url');
            $table->text('steps');
            $table->timestamps();
        });

        Schema::dropIfExists('ingredient_recipe');
        Schema::create('ingredient_recipe', function(Blueprint $table) {
            $table->foreignId('id_recipe')->references('id')->on('recipe');
            $table->foreignId('id_ingredient')->references('id')->on('ingredient');
                $table->primary(['id_recipe', 'id_ingredient']);
            $table->integer('quantity');
        });

        Schema::dropIfExists('rating');
        Schema::create('rating', function(Blueprint $table) {
            $table->foreignId('id_recipe')->references('id')->on('recipe');
            $table->foreignId('id_user')->references('id')->on('user');
                $table->primary(['id_recipe', 'id_user']);
            $table->integer('rate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rating');
        Schema::dropIfExists('ingredient_recipe');
        Schema::dropIfExists('recipe');
        Schema::dropIfExists('ingredient');
        Schema::dropIfExists('type');
    }
}
