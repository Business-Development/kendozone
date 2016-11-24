<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFights extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fight', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('tree_id')->unsigned()->index();
            $table->foreign('tree_id')
                ->references('id')
                ->on('tree')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->integer('c1')->nullable()->unsigned()->index();
            $table->foreign('c1')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->integer('c2')->nullable()->unsigned()->index();
            $table->foreign('c2')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->tinyInteger("order");
            $table->timestamps();
            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
